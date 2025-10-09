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
            'product_type' => 'nullable|in:metal,plastic,tap-pay', // Made optional for pre-order tokens
            'design_name' => 'nullable|string', // Made optional for pre-order tokens
            'selected_color' => 'required|string',
            'name_position' => 'required|in:front,back',
            'requirements' => 'nullable|string|max:2000',
            'needs_pickup' => 'boolean',
            'attachment_ids' => 'nullable|array', // Added for file attachments
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
                'order_type' => 1, // ✅ FIXED: 1=online (was string 'online')
                'order_source' => 'customer_link', // Track source
                'order_channel' => 'dashboard_token', // Track channel
                'created_by' => $preOrderToken->agent_id,
                'financial_year_id' => 1
            ]);

            // Create order details
            $serviceId = $request->service === 'diy' ? 1 : 2; // 1 = DIY, 2 = Full Service
            \App\Models\OrderDetail::create([
                'order_id' => $order->id,
                'service_id' => $serviceId,
                'service_name' => $request->service === 'diy' ? 'Metal Card - DIY Service' : 'Metal Card - Full Service',
                'service_price' => $subTotal,
                'service_quantity' => '1',
                'service_detail_total' => $subTotal,
                'color_code' => $request->selected_color . ' | Name Position: ' . $request->name_position
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

            // Save attachments if provided
            if ($request->has('attachment_ids') && is_array($request->attachment_ids)) {
                foreach ($request->attachment_ids as $attachmentId) {
                    try {
                        // Update the attachment with order_id
                        \DB::table('order_attachments')
                            ->where('id', $attachmentId)
                            ->update(['order_id' => $order->id]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to link attachment: ' . $e->getMessage());
                    }
                }
            }

            $preOrderToken->markAsUsed($order->id);

            // Send Telegram notification (non-blocking)
            $this->sendTelegramNotification($order, $request, $preOrderToken);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
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
        // New Format: CC-YYMM-NNNN
        // Example: CC-2510-0001 (Oct 2025, Order #1)
        // Length: 13 characters (professional & concise)

        $year = date('y');   // 25 (last 2 digits)
        $month = date('m');  // 10

        // Get last order number for current month
        $yearMonth = date('Y-m');
        $lastOrder = Order::where('order_number', 'like', "CC-{$year}{$month}-%")
                          ->orderBy('id', 'desc')
                          ->first();

        // Extract sequential number or start at 1
        if ($lastOrder && preg_match('/CC-\d{4}-(\d{4})/', $lastOrder->order_number, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        // Format: CC-YYMM-NNNN
        return sprintf(
            'CC-%s%s-%s',
            $year,
            $month,
            str_pad($number, 4, '0', STR_PAD_LEFT)
        );
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

    private function sendTelegramNotification($order, $request, $preOrderToken)
    {
        try {
            $telegramUrl = env('BACKEND_API_URL', 'http://localhost:5001') . '/api/payment-success/notify';

            $data = [
                'customerName' => $request->customer_name,
                'whatsappNumber' => $request->whatsapp_number,
                'service' => $request->service,
                'selectedColor' => $request->selected_color,
                'namePosition' => $request->name_position,
                'requirements' => $request->requirements ?? '',
                'orderId' => $order->order_number,
                'advanceAmount' => $preOrderToken->advance_amount,
                'totalAmount' => $preOrderToken->total_amount ?? 0,
                'remainingAmount' => ($preOrderToken->total_amount ?? 0) - $preOrderToken->advance_amount,
                'agentNotes' => $preOrderToken->notes ?? '',
                'timestamp' => now()->timestamp,
            ];

            // Send non-blocking request
            $ch = curl_init($telegramUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 5 second timeout

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                \Log::info('Telegram notification sent successfully for order: ' . $order->order_number);
            } else {
                \Log::warning('Telegram notification failed with status: ' . $httpCode);
            }
        } catch (\Exception $e) {
            \Log::error('Telegram notification error: ' . $e->getMessage());
            // Don't throw - this is non-critical
        }
    }
}
