<?php

namespace App\Livewire\Orders;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Order;
use App\Models\Translation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderStatusScreen extends Component
{
    public $orders, $pending_orders, $processing_orders, $ready_orders, $lang,$dateFilter='all';

    /* Helper method: Check if user can see all orders */
    private function canSeeAllOrders()
    {
        // Admin users (user_type == 1) can see all orders
        if (Auth::user()->user_type == 1) {
            return true;
        }

        // Order Management role users can see all orders
        if (Auth::user()->role && Auth::user()->role->name === 'Order Management') {
            return true;
        }

        return false;
    }

    #[Title('Order Status Screen')]
    public function render()
    {
        $pending_orders = Order::where('status', 0)->with('user')->latest();
        $processing_orders = Order::where('status', 1)->with('user')->latest();
        $ready_orders = Order::where('status', 2)->with('user')->latest();

        // Filter by user if they don't have permission to see all orders
        if (!$this->canSeeAllOrders()) {
            $pending_orders->where('created_by', Auth::user()->id);
            $processing_orders->where('created_by', Auth::user()->id);
            $ready_orders->where('created_by', Auth::user()->id);
        }

        switch($this->dateFilter){
            case 'all':
                // No date filter - show all orders
                break;
            case 'today':
                $startDate = Carbon::today()->startOfDay()->toDateString();
                $endDate = Carbon::today()->endOfDay()->toDateString();
                $pending_orders->whereDate('order_date', $startDate);
                $processing_orders->whereDate('order_date', $startDate);
                $ready_orders->whereDate('order_date', $startDate);
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek()->startOfDay();
                $endDate = Carbon::now()->endOfWeek()->endOfDay();
                $pending_orders->whereDate('order_date','>=', $startDate)->whereDate('order_date','<=', $endDate);
                $processing_orders->whereDate('order_date','>=', $startDate)->whereDate('order_date','<=', $endDate);
                $ready_orders->whereDate('order_date','>=', $startDate)->whereDate('order_date','<=', $endDate);
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                $pending_orders->whereDate('order_date','>=', $startDate)->whereDate('order_date','<=', $endDate);
                $processing_orders->whereDate('order_date','>=', $startDate)->whereDate('order_date','<=', $endDate);
                $ready_orders->whereDate('order_date','>=', $startDate)->whereDate('order_date','<=', $endDate);
                break;
        }
        $this->pending_orders = $pending_orders->get();
        $this->processing_orders = $processing_orders->get();
        $this->ready_orders = $ready_orders->get();
        return view('livewire.orders.order-status-screen');
    }

    /* process before render */
    public function mount()
    {
        if(!\Illuminate\Support\Facades\Gate::allows('order_status_change')){
            abort(404);
        }
        if (session()->has('selected_language')) {  /* if session has selected language */
            $this->lang = Translation::where('id', session()->get('selected_language'))->first();
        } else {
            /* if session has no selected language */
            $this->lang = Translation::where('default', 1)->first();
        }

      
    }
    /* change the order status */
    public function changestatus($order, $status)
    {
        $orderz = Order::where('id', $order)->first();
        switch ($status) {
            case 'processing':
                $orderz->status = 1;
                $orderz->save();
                $message = sendOrderStatusChangeSMS($orderz->id, 1);
                break;
            case 'ready':
                $orderz->status = 2;
                $orderz->save();
                $message = sendOrderStatusChangeSMS($orderz->id, 2);
                break;
            case 'pending':
                $orderz->status = 0;
                $orderz->save();
                $message = sendOrderStatusChangeSMS($orderz->id, 3);
                break;
        }

        if ($message) {
            $this->dispatch(
                'alert',
                ['type' => 'error',  'message' => $message, 'title' => 'SMS Error']
            );
        }
    }
}
