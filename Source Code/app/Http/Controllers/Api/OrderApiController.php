<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    /**
     * Create a new order from website
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'customerName' => 'required|string|max:255',
                'whatsappNumber' => 'required|string|max:20',
                'service' => 'required|in:diy,full-service',
                'selectedColor' => 'required|string',
                'namePosition' => 'required|in:front,back',
                'paymentOption' => 'required|string',
                'razorpayPaymentId' => 'required|string',
                'razorpayOrderId' => 'required|string',
                'razorpaySignature' => 'required|string',
                'product' => 'required|array',
                'paymentDetails' => 'required|array',
                'requirements' => 'nullable|string',
                'needsPickup' => 'boolean',
            ]);

            DB::beginTransaction();

            // Create or update customer
            $customer = Customer::updateOrCreate(
                ['phone' => $validated['whatsappNumber']],
                [
                    'name' => $validated['customerName'],
                    'is_active' => 1
                ]
            );

            // Find product - try multiple methods
            $product = null;

            // Try to find by mongodb_id first
            if (isset($validated['product']['_id'])) {
                $product = Product::where('mongodb_id', $validated['product']['_id'])->first();
            }

            // If not found, try by name or slug
            if (!$product && isset($validated['product']['name'])) {
                $product = Product::where('name', $validated['product']['name'])
                                 ->orWhere('slug', $validated['product']['slug'] ?? '')
                                 ->orWhere('display_name', $validated['product']['displayName'] ?? '')
                                 ->first();
            }

            // If still not found, use first Metal Card product as fallback
            if (!$product) {
                $product = Product::where('category', 'Metal Card')->first();
                \Log::warning('Product not found, using fallback product', [
                    'requested_product' => $validated['product'],
                    'fallback_product_id' => $product?->id
                ]);
            }

            if (!$product) {
                throw new \Exception('No products available in database. Please seed products first.');
            }

            // Calculate delivery date
            $deliveryDays = $validated['service'] === 'diy' ? 5 : 12;
            $deliveryDate = now()->addDays($deliveryDays);

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $validated['customerName'],
                'phone_number' => $validated['whatsappNumber'],
                'order_date' => now(),
                'delivery_date' => $deliveryDate,
                'sub_total' => $validated['paymentDetails']['price'] ?? 0,
                'addon_total' => ($validated['needsPickup'] ?? false) ? 350 : 0,
                'discount' => 0,
                'tax_percentage' => 0,
                'tax_amount' => 0,
                'total' => $validated['paymentDetails']['total'] ?? 0,
                'note' => $validated['requirements'] ?? '',
                'status' => 0, // 0=pending, 1=processing, 2=ready, 3=completed
                'order_type' => 1, // 1=online (assuming 0=POS based on existing data)
                'order_source' => 'online',
                'order_channel' => 'website', // Track that this came from website
                'assigned_to' => null, // No staff assigned for website orders
                'customization_data' => json_encode($validated),
                'razorpay_order_id' => $validated['razorpayOrderId'],
                'razorpay_payment_id' => $validated['razorpayPaymentId'],
                'razorpay_signature' => $validated['razorpaySignature'],
                'payment_method_type' => $validated['paymentOption'],
                'needs_pickup' => $validated['needsPickup'] ?? false,
                'created_by' => 7, // Website system user
                'financial_year_id' => 1
            ]);

            // Determine service_id based on service type
            $serviceId = $validated['service'] === 'diy' ? 3 : 4; // 3 = DIY, 4 = Full Service

            // Create order details
            OrderDetail::create([
                'order_id' => $order->id,
                'service_id' => $serviceId,
                'service_name' => $validated['service'] === 'diy' ? 'Metal Card - DIY Service' : 'Metal Card - Full Service',
                'service_price' => $validated['paymentDetails']['price'] ?? 0,
                'service_quantity' => '1',
                'service_detail_total' => $validated['paymentDetails']['price'] ?? 0,
                'color_code' => $validated['selectedColor'] . ' | Name Position: ' . $validated['namePosition']
            ]);

            // Record payment
            Payment::create([
                'order_id' => $order->id,
                'payment_date' => now(),
                'customer_id' => $customer->id,
                'customer_name' => $validated['customerName'],
                'received_amount' => $validated['paymentDetails']['payNow'] ?? 0,
                'payment_type' => 1, // 1 = Online/Digital payment
                'payment_note' => "Razorpay Payment | Method: {$validated['paymentOption']} | Transaction ID: {$validated['razorpayPaymentId']}",
                'created_by' => 1,
                'financial_year_id' => 1
            ]);

            DB::commit();

            // Send SMS notification for new order
            try {
                $smsResult = sendOrderCreateSMS($order->id, $customer->id);
                if ($smsResult) {
                    \Log::warning('SMS notification failed for order ' . $order->order_number . ': ' . $smsResult);
                }
            } catch (\Exception $e) {
                // Don't fail the order creation if SMS fails
                \Log::error('SMS notification error for order ' . $order->order_number . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => 'Order created successfully',
                'delivery_date' => $deliveryDate->format('Y-m-d')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to create order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track order
     */
    public function track(Request $request)
    {
        try {
            $validated = $request->validate([
                'orderNumber' => 'required|string',
                'phone' => 'required|string'
            ]);

            $order = Order::where('order_number', $validated['orderNumber'])
                         ->where('phone_number', $validated['phone'])
                         ->with(['payments'])
                         ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'error' => 'Order not found'
                ], 404);
            }

            // Map status integer to text
            $statusMap = [
                0 => 'pending',
                1 => 'processing',
                2 => 'ready_to_deliver',
                3 => 'completed',
                4 => 'cancelled'
            ];

            return response()->json([
                'success' => true,
                'order' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'status' => $statusMap[$order->status] ?? 'unknown',
                    'status_code' => $order->status,
                    'order_date' => $order->order_date->format('Y-m-d'),
                    'delivery_date' => $order->delivery_date?->format('Y-m-d'),
                    'total' => $order->total
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to track order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function generateOrderNumber()
    {
        $year = date('Y');
        $lastOrder = Order::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $number = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;
        return 'CC-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
