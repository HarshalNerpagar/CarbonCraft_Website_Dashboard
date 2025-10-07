<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Translation;
use Livewire\Attributes\Title;
use Livewire\Component;
use Carbon\Carbon;

class HomePage extends Component
{
    #[Title('Dashboard')]
    public $pending_count,$processing_count,$ready_count,$delivered_count,$orders,$array,$search_query,$order_filter,$lang;
    public $today_revenue, $week_revenue, $month_revenue, $agent_revenues;
    public $total_order_amount, $total_paid_amount, $total_unpaid_amount;
    public function render()
    {
        // Optimized: Single query instead of 4 separate queries
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->whereIn('status', [0, 1, 2, 3])
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->pending_count = $statusCounts[0] ?? 0;
        $this->processing_count = $statusCounts[1] ?? 0;
        $this->ready_count = $statusCounts[2] ?? 0;
        $this->delivered_count = $statusCounts[3] ?? 0;

        return view('livewire.home-page');
    }

    /* process before mount */
    public function mount()
    {
        // Optimized: Single query instead of 5 separate queries
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->whereIn('status', [0, 1, 2, 3, 4])
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->pending_count = $statusCounts[0] ?? 0;
        $this->processing_count = $statusCounts[1] ?? 0;
        $this->ready_count = $statusCounts[2] ?? 0;
        $this->delivered_count = $statusCounts[3] ?? 0;
        $returned_count = $statusCounts[4] ?? 0;
        $this->orders = Order::whereDate('order_date',\Carbon\Carbon::today()->toDateString())->with('user')->latest()->get();

        // Calculate revenues
        $this->calculateRevenues();

        // Calculate payment overview (Total, Paid, Unpaid)
        $this->calculatePaymentOverview();

        if(session()->has('selected_language'))
        {
            /* if the session has selected language */
            $this->lang = Translation::where('id',session()->get('selected_language'))->first();
        }
        else{
            /* if the session has no selected language */
            $this->lang = Translation::where('default',1)->first();
        }

        // Update array to show payment data instead of status data
        $this->array = json_encode(array(
            $this->total_paid_amount,
            $this->total_unpaid_amount
        ));
    }

    /* calculate payment overview */
    private function calculatePaymentOverview()
    {
        // Get all orders total (excluding returned orders)
        $this->total_order_amount = Order::whereIn('status', [0, 1, 2, 3])
            ->sum('total');

        // Get total paid amount
        $this->total_paid_amount = Payment::sum('received_amount');

        // Calculate unpaid amount
        $this->total_unpaid_amount = $this->total_order_amount - $this->total_paid_amount;

        // Ensure unpaid is not negative
        if ($this->total_unpaid_amount < 0) {
            $this->total_unpaid_amount = 0;
        }
    }

    /* calculate revenues for different periods */
    private function calculateRevenues()
    {
        // Today's revenue
        $this->today_revenue = Payment::whereDate('payment_date', Carbon::today()->toDateString())
            ->sum('received_amount');

        // This week's revenue (Monday to Sunday)
        $this->week_revenue = Payment::whereBetween('payment_date', [
            Carbon::now()->startOfWeek()->toDateString(),
            Carbon::now()->endOfWeek()->toDateString()
        ])->sum('received_amount');

        // This month's revenue
        $this->month_revenue = Payment::whereYear('payment_date', Carbon::now()->year)
            ->whereMonth('payment_date', Carbon::now()->month)
            ->sum('received_amount');

        // Agent-wise revenue (this month)
        $agents = User::where('user_type', 2)->get();
        $this->agent_revenues = [];

        foreach ($agents as $agent) {
            $revenue = Payment::whereYear('payment_date', Carbon::now()->year)
                ->whereMonth('payment_date', Carbon::now()->month)
                ->where('created_by', $agent->id)
                ->sum('received_amount');

            $this->agent_revenues[] = [
                'name' => $agent->name,
                'revenue' => $revenue,
                'color' => $this->getAgentColor($agent->name)
            ];
        }

        // Sort by revenue (highest first)
        usort($this->agent_revenues, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
    }

    /* get agent badge color */
    private function getAgentColor($name)
    {
        return match($name) {
            'Siddhi' => 'bg-warning-600',
            'Sayali' => 'bg-warning-600',
            'Hitesh' => 'bg-warning-600',
            default => 'bg-secondary-600'
        };
    }
    /* process while update the element */
    public function updated($name,$value)
    {
        /*if the updated element is search_query and value is not empty */
        if($name == 'search_query' && $value != '')
        {
            if($this->order_filter == '')
            {
                $this->orders = \App\Models\Order::whereDate('order_date',\Carbon\Carbon::today()->toDateString())
                                            ->where(function($q) use ($value) {
                                                $q->where('order_number','like','%'.$value.'%')
                                                    ->orwhere('customer_name','like','%'.$value.'%');
                                                })
                                            ->with('user')
                                            ->latest()
                                            ->get();
            }
            else{
                $this->orders = \App\Models\Order::where('status',$this->order_filter)
                                            ->whereDate('order_date',\Carbon\Carbon::today()->toDateString())
                                            ->where(function($q) use ($value) {
                                                $q->where('order_number','like','%'.$value.'%')
                                                ->orwhere('customer_name','like','%'.$value.'%');
                                            })
                                            ->latest()
                                            ->get();
            }
        }
        elseif($name == 'search_query' && $value == '')
        {
            /* if the updated element is search_query and value is empty */
            if($this->order_filter == '')
            {  /* if the order filter value is empty */
                $this->orders = \App\Models\Order::whereDate('order_date',\Carbon\Carbon::today()->toDateString())->with('user')->latest()->get();
            }
            else{
                /* if the order filter value is not empty */
                $this->orders = \App\Models\Order::whereDate('order_date',\Carbon\Carbon::today()->toDateString())->where('status',$this->order_filter)->with('user')->latest()->get();

            }
        }
        /* if the updated value is order filter */
        if($name == 'order_filter')
        {
            $this->search_query = '';
            if($value == '')
            {    /* if the order filter value is empty */
                $this->orders = \App\Models\Order::whereDate('order_date',\Carbon\Carbon::today()->toDateString())->with('user')->latest()->get();
            }
            else{
                /* if the order filter value is not empty */
                $this->orders = \App\Models\Order::whereDate('order_date',\Carbon\Carbon::today()->toDateString())->where('status',$value)->with('user')->latest()->get();
            }
        }
    }
}
