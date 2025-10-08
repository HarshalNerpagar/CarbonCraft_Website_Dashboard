<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreOrderToken;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PreOrderTokenController extends Controller
{
    /**
     * Generate a new pre-order token (Staff only)
     */
    public function generateToken(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:upi,razorpay,cash',
            'advance_amount' => 'required|numeric|min:100|max:100000',
            'customer_phone' => 'nullable|string|max:15',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate unique token
            $token = PreOrderToken::generateUniqueToken();

            // Create token record
            $preOrderToken = PreOrderToken::create([
                'token' => $token,
                'agent_id' => Auth::id(),
                'payment_method' => $request->payment_method,
                'advance_amount' => $request->advance_amount,
                'customer_phone' => $request->customer_phone,
                'customer_name' => $request->customer_name,
                'notes' => $request->notes,
                'expires_at' => Carbon::now()->addHours(48), // 48 hours expiry
            ]);

            // Generate customer URL
            $customerUrl = env('WEBSITE_URL', 'http://localhost:4321') . '/order/' . $token;

            return response()->json([
                'success' => true,
                'token' => $token,
                'url' => $customerUrl,
                'expires_at' => $preOrderToken->expires_at->format('Y-m-d H:i:s'),
                'message' => 'Customer link generated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get token details (Public - for customer form)
     */
    public function getToken($token)
    {
        try {
            $preOrderToken = PreOrderToken::where('token', $token)
                ->with('agent:id,name')
                ->first();

            if (!$preOrderToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 404);
            }

            if (!$preOrderToken->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => $preOrderToken->used ? 'Token already used' : 'Token expired'
                ], 410);
            }

            return response()->json([
                'success' => true,
                'token' => [
                    'payment_method' => $preOrderToken->payment_method,
                    'total_amount' => $preOrderToken->total_amount,
                    'advance_amount' => $preOrderToken->advance_amount,
                    'remaining_amount' => ($preOrderToken->total_amount ?? 0) - $preOrderToken->advance_amount,
                    'customer_phone' => $preOrderToken->customer_phone,
                    'customer_name' => $preOrderToken->customer_name,
                    'notes' => $preOrderToken->notes,
                    'agent_name' => $preOrderToken->agent->name ?? 'CarbonCraft Support',
                    'expires_at' => $preOrderToken->expires_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit customer order using pre-order token (Public)
     */
    public function submitOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:15',
            'service' => 'required|in:diy,full-service',
            'product_type' => 'required|in:metal,plastic,tap-pay',
            'design_name' => 'required|string',
            'selected_color' => 'required|string',
            'name_position' => 'required|in:front,back',
            'requirements' => 'nullable|string|max:2000',
            'needs_pickup' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $preOrderToken = PreOrderToken::where('token', $request->token)->first();

            if (!$preOrderToken || !$preOrderToken->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => !$preOrderToken ? 'Invalid token' : ($preOrderToken->used ? 'Token already used' : 'Token expired')
                ], !$preOrderToken ? 404 : 410);
            }

            $customer = Customer::updateOrCreate(
                ['phone' => $request->whatsapp_number],
                [
                    'name' => $request->customer_name,
                    'is_active' => 1
                ]
            );

            // Use total_amount from token instead of calculating from product
            $totalAmount = $preOrderToken->total_amount ?? 2899; // Fallback to default if not set
            $pickupCharge = ($request->needs_pickup ?? false) ? 350 : 0;
            $subTotal = $totalAmount - $pickupCharge;

            $orderNumber = $this->generateOrderNumber();
            $deliveryDate = $request->service === 'diy' ? Carbon::now()->addDays(5) : Carbon::now()->addDays(12);

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'phone_number' => $request->whatsapp_number,
                'order_date' => now(),
                'delivery_date' => $deliveryDate,
                'sub_total' => $subTotal,
                'addon_total' => $pickupCharge,
                'total' => $totalAmount,
                'note' => $this->formatOrderNote($request, $preOrderToken->notes),
                'status' => 0, // Advance Done
                'order_type' => 'online',
                'created_by' => $preOrderToken->agent_id,
                'financial_year_id' => 1
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_date' => now(),
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'received_amount' => $preOrderToken->advance_amount,
                'payment_type' => $preOrderToken->payment_method === 'cash' ? '1' : '2',
                'payment_note' => 'Advance via ' . strtoupper($preOrderToken->payment_method),
                'created_by' => $preOrderToken->agent_id,
                'financial_year_id' => 1
            ]);

            $preOrderToken->markAsUsed($order->id);

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'total_amount' => $totalAmount,
                'paid_amount' => $preOrderToken->advance_amount,
                'remaining_amount' => $totalAmount - $preOrderToken->advance_amount,
                'delivery_date' => $deliveryDate->format('d M Y'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getMyTokens()
    {
        $tokens = PreOrderToken::where('agent_id', Auth::id())->with('order:id,order_number')->latest()->take(50)->get();
        return response()->json([
            'success' => true,
            'tokens' => $tokens->map(fn($t) => [
                'id' => $t->id,
                'token' => $t->token,
                'customer_name' => $t->customer_name ?? 'Not provided',
                'advance_amount' => $t->advance_amount,
                'used' => $t->used,
                'is_valid' => $t->isValid(),
                'order_number' => $t->order?->order_number,
                'created_at' => $t->created_at->format('d M Y H:i'),
            ])
        ]);
    }

    private function calculatePricing($productType, $service, $needsPickup)
    {
        $prices = ['metal' => 2899, 'plastic' => 999, 'tap-pay' => 7999];
        $subTotal = $prices[$productType] ?? 2899;
        $pickupCharge = ($service === 'full-service' && $needsPickup) ? 350 : 0;
        return ['sub_total' => $subTotal, 'pickup_charge' => $pickupCharge, 'total' => $subTotal + $pickupCharge];
    }

    private function generateOrderNumber()
    {
        $year = Carbon::now()->year;
        $lastOrder = Order::whereYear('order_date', $year)->orderBy('order_number', 'desc')->first();
        $nextNumber = ($lastOrder && preg_match('/CC-\d{4}-(\d{4})/', $lastOrder->order_number, $m)) ? intval($m[1]) + 1 : 1;
        return 'CC-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function formatOrderNote($request, $agentNotes = null)
    {
        $note = "Product: {$request->product_type}\nDesign: {$request->design_name}\nService: {$request->service}\n";
        $note .= "Color: {$request->selected_color}\nName Position: {$request->name_position}\n";

        if ($agentNotes) {
            $note .= "\n--- Agent Notes ---\n{$agentNotes}\n";
        }

        if ($request->requirements) {
            $note .= "\n--- Customer Requirements ---\n{$request->requirements}";
        }

        if ($request->needs_pickup) {
            $note .= "\n\n✓ Pickup Service (+₹350)";
        }

        return $note;
    }
}
