<div class="dashboard-main-body">
    <!-- Order Status Pipeline - Single Card -->
    <div class="tw-mb-6 fade-in">
        <div class="card shadow-none border h-100">
            <div class="card-body tw-p-6">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                    <h5 class="tw-text-lg tw-font-bold tw-mb-0">{{ $lang->data['order_pipeline'] ?? 'Order Pipeline' }}</h5>
                    <span class="badge bg-primary-100 text-primary-600 tw-px-3 tw-py-1">
                        {{ $pending_count + $processing_count + $ready_count + $delivered_count }} {{ $lang->data['total_orders'] ?? 'Total Orders' }}
                    </span>
                </div>

                <!-- Order Status Steps -->
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
                    <!-- Step 1: Advance Done -->
                    <div class="tw-relative tw-group">
                        <div class="tw-bg-gradient-to-br tw-from-cyan-50 tw-to-cyan-100 tw-rounded-xl tw-p-4 smooth-transition hover:tw-shadow-md hover:tw-scale-105">
                            <div class="tw-flex tw-items-start tw-justify-between">
                                <div class="tw-flex-1">
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-mb-2">
                                        <div class="tw-w-10 tw-h-10 tw-bg-cyan-500 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                                            <iconify-icon icon="solar:cart-check-outline" class="tw-text-white tw-text-xl"></iconify-icon>
                                        </div>
                                        <span class="tw-text-xs tw-font-semibold tw-text-cyan-600 tw-bg-cyan-200 tw-px-2 tw-py-0.5 tw-rounded-full">Step 1</span>
                                    </div>
                                    <p class="tw-text-xs tw-text-gray-600 tw-mb-1">{{ $lang->data['advance_done'] ?? 'Advance Done' }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $pending_count }}</h3>
                                    <p class="tw-text-xs tw-text-gray-500 tw-mt-1">Pending Orders</p>
                                </div>
                            </div>
                        </div>
                        <!-- Arrow connector (hidden on mobile) -->
                        <div class="tw-hidden lg:tw-block tw-absolute tw-top-1/2 -tw-right-2 tw-transform -tw-translate-y-1/2 tw-z-10">
                            <iconify-icon icon="solar:arrow-right-outline" class="tw-text-2xl tw-text-gray-300"></iconify-icon>
                        </div>
                    </div>

                    <!-- Step 2: Design Ready -->
                    <div class="tw-relative tw-group">
                        <div class="tw-bg-gradient-to-br tw-from-purple-50 tw-to-purple-100 tw-rounded-xl tw-p-4 smooth-transition hover:tw-shadow-md hover:tw-scale-105">
                            <div class="tw-flex tw-items-start tw-justify-between">
                                <div class="tw-flex-1">
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-mb-2">
                                        <div class="tw-w-10 tw-h-10 tw-bg-purple-500 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                                            <iconify-icon icon="solar:palette-outline" class="tw-text-white tw-text-xl"></iconify-icon>
                                        </div>
                                        <span class="tw-text-xs tw-font-semibold tw-text-purple-600 tw-bg-purple-200 tw-px-2 tw-py-0.5 tw-rounded-full">Step 2</span>
                                    </div>
                                    <p class="tw-text-xs tw-text-gray-600 tw-mb-1">{{ $lang->data['design_ready'] ?? 'Design Ready' }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $processing_count }}</h3>
                                    <p class="tw-text-xs tw-text-gray-500 tw-mt-1">In Production</p>
                                </div>
                            </div>
                        </div>
                        <div class="tw-hidden lg:tw-block tw-absolute tw-top-1/2 -tw-right-2 tw-transform -tw-translate-y-1/2 tw-z-10">
                            <iconify-icon icon="solar:arrow-right-outline" class="tw-text-2xl tw-text-gray-300"></iconify-icon>
                        </div>
                    </div>

                    <!-- Step 3: Ready to Deliver -->
                    <div class="tw-relative tw-group">
                        <div class="tw-bg-gradient-to-br tw-from-blue-50 tw-to-blue-100 tw-rounded-xl tw-p-4 smooth-transition hover:tw-shadow-md hover:tw-scale-105">
                            <div class="tw-flex tw-items-start tw-justify-between">
                                <div class="tw-flex-1">
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-mb-2">
                                        <div class="tw-w-10 tw-h-10 tw-bg-blue-500 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                                            <iconify-icon icon="solar:box-outline" class="tw-text-white tw-text-xl"></iconify-icon>
                                        </div>
                                        <span class="tw-text-xs tw-font-semibold tw-text-blue-600 tw-bg-blue-200 tw-px-2 tw-py-0.5 tw-rounded-full">Step 3</span>
                                    </div>
                                    <p class="tw-text-xs tw-text-gray-600 tw-mb-1">{{ $lang->data['ready_to_deliver'] ?? 'Ready To Deliver' }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $ready_count }}</h3>
                                    <p class="tw-text-xs tw-text-gray-500 tw-mt-1">Quality Checked</p>
                                </div>
                            </div>
                        </div>
                        <div class="tw-hidden lg:tw-block tw-absolute tw-top-1/2 -tw-right-2 tw-transform -tw-translate-y-1/2 tw-z-10">
                            <iconify-icon icon="solar:arrow-right-outline" class="tw-text-2xl tw-text-gray-300"></iconify-icon>
                        </div>
                    </div>

                    <!-- Step 4: Delivered -->
                    <div class="tw-relative tw-group">
                        <div class="tw-bg-gradient-to-br tw-from-green-50 tw-to-green-100 tw-rounded-xl tw-p-4 smooth-transition hover:tw-shadow-md hover:tw-scale-105">
                            <div class="tw-flex tw-items-start tw-justify-between">
                                <div class="tw-flex-1">
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-mb-2">
                                        <div class="tw-w-10 tw-h-10 tw-bg-green-500 tw-rounded-lg tw-flex tw-items-center tw-justify-center">
                                            <iconify-icon icon="solar:check-circle-outline" class="tw-text-white tw-text-xl"></iconify-icon>
                                        </div>
                                        <span class="tw-text-xs tw-font-semibold tw-text-green-600 tw-bg-green-200 tw-px-2 tw-py-0.5 tw-rounded-full">Step 4</span>
                                    </div>
                                    <p class="tw-text-xs tw-text-gray-600 tw-mb-1">{{ $lang->data['delivered_orders'] ?? 'Delivered Orders' }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ $delivered_count }}</h3>
                                    <p class="tw-text-xs tw-text-gray-500 tw-mt-1">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Performance Section (Admin Only) -->
    @if(Auth::user()->user_type == 1)
    <div class="tw-grid 2xl:tw-grid-cols-3 tw-gap-4 lg:tw-grid-cols-1 tw-grid-cols-1 fade-in" style="animation-delay: 0.2s;">
        <!-- Revenue Summary Card -->
        <div class="2xl:tw-col-span-2">
            <div class="card shadow-none border h-100">
                <div class="card-body tw-p-6">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                        <h5 class="tw-text-lg tw-font-bold tw-mb-0 tw-flex tw-items-center tw-gap-2">
                            <iconify-icon icon="solar:dollar-minimalistic-outline" class="tw-text-2xl tw-text-primary-600"></iconify-icon>
                            {{ $lang->data['revenue_overview'] ?? 'Revenue Overview' }}
                        </h5>
                        <span class="badge bg-success-100 text-success-600 tw-px-3 tw-py-1 tw-flex tw-items-center tw-gap-1">
                            <iconify-icon icon="solar:graph-up-outline" class="tw-text-sm"></iconify-icon>
                            {{ $lang->data['financial_summary'] ?? 'Financial Summary' }}
                        </span>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                        <!-- Today's Revenue -->
                        <div class="tw-relative tw-overflow-hidden tw-group">
                            <div class="tw-bg-white tw-rounded-2xl tw-p-4 smooth-transition hover:tw-shadow-xl tw-border-l-4 tw-border-green-500">
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                    <div class="tw-w-12 tw-h-12 tw-bg-green-100 tw-rounded-xl tw-flex tw-items-center tw-justify-center group-hover:tw-scale-110 smooth-transition">
                                        <iconify-icon icon="solar:wallet-money-bold" class="tw-text-green-600 tw-text-2xl"></iconify-icon>
                                    </div>
                                    <div class="tw-bg-green-50 tw-px-2 tw-py-1 tw-rounded-full">
                                        <span class="tw-text-xs tw-font-bold tw-text-green-600">{{ $lang->data['today'] ?? 'Today' }}</span>
                                    </div>
                                </div>
                                <div class="tw-mt-2">
                                    <p class="tw-text-xs tw-text-gray-500 tw-mb-1">{{ $lang->data['today_revenue'] ?? "Today's Revenue" }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ getFormattedCurrency($today_revenue) }}</h3>
                                    <div class="tw-flex tw-items-center tw-gap-1 tw-mt-2 tw-text-xs tw-text-green-600">
                                        <iconify-icon icon="solar:chart-2-bold" class="tw-text-sm"></iconify-icon>
                                        <span class="tw-font-medium">Daily earnings</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- This Week's Revenue -->
                        <div class="tw-relative tw-overflow-hidden tw-group">
                            <div class="tw-bg-white tw-rounded-2xl tw-p-4 smooth-transition hover:tw-shadow-xl tw-border-l-4 tw-border-blue-500">
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                    <div class="tw-w-12 tw-h-12 tw-bg-blue-100 tw-rounded-xl tw-flex tw-items-center tw-justify-center group-hover:tw-scale-110 smooth-transition">
                                        <iconify-icon icon="solar:calendar-mark-bold" class="tw-text-blue-600 tw-text-2xl"></iconify-icon>
                                    </div>
                                    <div class="tw-bg-blue-50 tw-px-2 tw-py-1 tw-rounded-full">
                                        <span class="tw-text-xs tw-font-bold tw-text-blue-600">{{ $lang->data['this_week'] ?? 'This Week' }}</span>
                                    </div>
                                </div>
                                <div class="tw-mt-2">
                                    <p class="tw-text-xs tw-text-gray-500 tw-mb-1">{{ $lang->data['week_revenue'] ?? "Week's Revenue" }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ getFormattedCurrency($week_revenue) }}</h3>
                                    <div class="tw-flex tw-items-center tw-gap-1 tw-mt-2 tw-text-xs tw-text-blue-600">
                                        <iconify-icon icon="solar:clock-circle-bold" class="tw-text-sm"></iconify-icon>
                                        <span class="tw-font-medium">7 days earnings</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- This Month's Revenue -->
                        <div class="tw-relative tw-overflow-hidden tw-group">
                            <div class="tw-bg-white tw-rounded-2xl tw-p-4 smooth-transition hover:tw-shadow-xl tw-border-l-4 tw-border-orange-500">
                                <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                    <div class="tw-w-12 tw-h-12 tw-bg-orange-100 tw-rounded-xl tw-flex tw-items-center tw-justify-center group-hover:tw-scale-110 smooth-transition">
                                        <iconify-icon icon="solar:chart-2-bold" class="tw-text-orange-600 tw-text-2xl"></iconify-icon>
                                    </div>
                                    <div class="tw-bg-orange-50 tw-px-2 tw-py-1 tw-rounded-full">
                                        <span class="tw-text-xs tw-font-bold tw-text-orange-600">{{ $lang->data['this_month'] ?? 'This Month' }}</span>
                                    </div>
                                </div>
                                <div class="tw-mt-2">
                                    <p class="tw-text-xs tw-text-gray-500 tw-mb-1">{{ $lang->data['month_revenue'] ?? "Month's Revenue" }}</p>
                                    <h3 class="tw-text-3xl tw-font-bold tw-text-gray-800">{{ getFormattedCurrency($month_revenue) }}</h3>
                                    <div class="tw-flex tw-items-center tw-gap-1 tw-mt-2 tw-text-xs tw-text-orange-600">
                                        <iconify-icon icon="solar:graph-up-bold" class="tw-text-sm"></iconify-icon>
                                        <span class="tw-font-medium">Monthly earnings</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent Performance Card -->
        <div class="col">
            <div class="card shadow-none border h-100">
                <div class="card-body tw-p-6">
                    <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-mb-4">
                        <div class="tw-text-center">
                            <p class="tw-text-xs tw-text-gray-500 tw-mb-2">{{ $lang->data['this_month'] ?? "This Month" }}</p>
                        </div>
                    </div>

                    <!-- Agent Performance List -->
                    <div class="tw-space-y-4">
                        @if(count($agent_revenues) > 0)
                            @foreach($agent_revenues as $index => $agent)
                                <div class="tw-bg-gradient-to-r tw-from-white tw-to-gray-50 tw-rounded-xl tw-p-4 smooth-transition hover:tw-shadow-lg tw-border tw-border-gray-100">
                                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                        <div class="tw-flex tw-items-center tw-gap-3">
                                            <div class="tw-w-10 tw-h-10 {{ $agent['color'] }} tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-text-white tw-font-bold tw-text-base tw-shadow-md">
                                                {{ substr($agent['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="tw-font-bold tw-text-sm tw-text-gray-800 tw-block">{{ $agent['name'] }}</span>
                                                <span class="tw-text-xs tw-text-gray-500">{{ $lang->data['agent'] ?? 'Agent' }}</span>
                                            </div>
                                        </div>
                                        <div class="tw-text-right">
                                            <span class="tw-font-bold tw-text-base tw-text-gray-800 tw-block">{{ getFormattedCurrency($agent['revenue']) }}</span>
                                            @php
                                                // Fixed scale of 3 lakhs (₹3,00,000)
                                                $maxScale = 300000;
                                                $percentage = $maxScale > 0 ? min(($agent['revenue'] / $maxScale) * 100, 100) : 0;
                                            @endphp
                                            <span class="tw-text-xs tw-text-gray-500">{{ number_format($percentage, 1) }}% of ₹3L</span>
                                        </div>
                                    </div>

                                    <!-- Performance Bar with Scale -->
                                    <div class="tw-relative">
                                        <div class="tw-w-full tw-h-3 tw-bg-gray-200 tw-rounded-full tw-overflow-hidden tw-shadow-inner">
                                            <div class="{{ $agent['color'] }} tw-h-full smooth-transition tw-rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>

                                        <!-- Scale markers -->
                                        <div class="tw-flex tw-justify-between tw-mt-1 tw-px-1">
                                            <span class="tw-text-xs tw-text-gray-400">₹0</span>
                                            <span class="tw-text-xs tw-text-gray-400">₹1.5L</span>
                                            <span class="tw-text-xs tw-text-gray-400">₹3L</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Scale Legend -->
                            <div class="tw-mt-4 tw-p-3 tw-bg-blue-50 tw-rounded-lg tw-border tw-border-blue-100">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <iconify-icon icon="solar:info-circle-bold" class="tw-text-blue-600 tw-text-lg"></iconify-icon>
                                    <span class="tw-text-xs tw-text-blue-700 tw-font-medium">{{ $lang->data['scale_info'] ?? 'Performance scale: ₹3,00,000 (3 Lakhs)' }}</span>
                                </div>
                            </div>
                        @else
                            <div class="tw-text-center tw-py-12">
                                <div class="tw-w-16 tw-h-16 tw-bg-gray-100 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mx-auto tw-mb-3">
                                    <iconify-icon icon="solar:users-group-rounded-outline" class="tw-text-4xl tw-text-gray-300"></iconify-icon>
                                </div>
                                <p class="tw-text-sm tw-text-gray-500 tw-font-medium">{{ $lang->data['no_agent_data'] ?? 'No agent data available' }}</p>
                                <p class="tw-text-xs tw-text-gray-400 tw-mt-1">{{ $lang->data['no_agent_desc'] ?? 'Agent performance will appear here' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row gy-4 mt-1">
        <div class="col-xxl-9 col-xl-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <h6 class="text-lg mb-0">{{ $lang->data['todays_orders'] ?? "Today's Orders" }}</h6>
                        <div class="tw-flex tw-items-center tw-gap-4">
                            <input type="text" class="form-control"
                                placeholder="{{ $lang->data['search_here'] ?? 'Search Here...' }}"
                                wire:model.live="search_query">

                            <select class="form-select" wire:model.live="order_filter">
                                <option class="select-box" value="">
                                    {{ $lang->data['all_orders'] ?? 'All Orders' }}</option>
                                <option class="select-box" value="0">{{ $lang->data['advance_done'] ?? 'Advance Done' }}
                                </option>
                                <option class="select-box" value="1">
                                    {{ $lang->data['design_ready'] ?? 'Design Ready' }}</option>
                                <option class="select-box" value="2">
                                    {{ $lang->data['ready_to_deliver'] ?? 'Ready To Deliver' }}</option>
                                <option class="select-box" value="3">{{ $lang->data['delivered'] ?? 'Delivered Orders' }}
                                </option>
                                <option class="select-box" value="4">{{ $lang->data['returned'] ?? 'Returned' }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="tw-grid tw-mt-4 tw-grid-cols-1 lg:tw-grid-cols-2 xl:tw-grid-cols-3 tw-gap-3">
                        @foreach ($orders as $item)
                            <div class="bg-neutral-50 p-16 radius-8 card-hover smooth-transition tw-cursor-pointer"
                                 wire:click="$redirect('{{ route('order.view', $item->id) }}')"
                                 role="button">
                                <div class="tw-flex tw-justify-between tw-items-start">
                                    <div class="tw-flex tw-flex-col tw-flex-1">
                                        <div class="tw-font-bold text-primary-light tw-text-base">{{ $item->order_number }}</div>

                                        <div class="text-sm text-secondary-light fw-normal tw-flex tw-items-center tw-gap-2 tw-mt-2">
                                            <iconify-icon icon="mdi:user-outline" class="text-primary-light"></iconify-icon>
                                            <span class="tw-font-semibold tw-text-gray-700">
                                                {{ $item->customer_name ?? ($lang->data['walk_in_customer'] ?? 'Walk In Customer') }}
                                            </span>
                                        </div>

                                        <div class="tw-flex tw-items-center tw-gap-2 tw-mt-3">
                                            @php
                                                $createdByName = $item->user?->name ?? 'Website';
                                                $badgeColor = match($createdByName) {
                                                    'Siddhi' => 'bg-warning-600 text-white',
                                                    'Sayali' => 'bg-purple-600 text-white',
                                                    'Hitesh' => 'bg-info-600 text-white',
                                                    'Website' => 'bg-success-600 text-white',
                                                    default => 'bg-secondary-600 text-white'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeColor }} status-badge">
                                                {{ $createdByName }}
                                            </span>

                                            @php
                                                $statusBadge = match($item->status) {
                                                    0 => ['text' => $lang->data['advance_done'] ?? 'Advance Done', 'class' => 'bg-cyan-100 text-cyan-600'],
                                                    1 => ['text' => $lang->data['design_ready'] ?? 'Design Ready', 'class' => 'bg-purple-100 text-purple-600'],
                                                    2 => ['text' => $lang->data['ready_to_deliver'] ?? 'Ready To Deliver', 'class' => 'bg-blue-100 text-blue-600'],
                                                    3 => ['text' => $lang->data['delivered'] ?? 'Delivered Orders', 'class' => 'bg-success-100 text-success-600'],
                                                    4 => ['text' => $lang->data['returned'] ?? 'Returned', 'class' => 'bg-danger-100 text-danger-600'],
                                                    default => ['text' => 'Unknown', 'class' => 'bg-gray-200 text-gray-600']
                                                };

                                                // Extract service type from customization_data or note
                                                $service = null;
                                                if ($item->customization_data) {
                                                    $customData = json_decode($item->customization_data, true);
                                                    $service = $customData['service'] ?? null;
                                                }
                                                // Fallback: check note field
                                                if (!$service && $item->note) {
                                                    if (stripos($item->note, 'DIY') !== false || stripos($item->note, 'diy') !== false) {
                                                        $service = 'diy';
                                                    } elseif (stripos($item->note, 'Full Service') !== false || stripos($item->note, 'full-service') !== false) {
                                                        $service = 'full-service';
                                                    }
                                                }

                                                $serviceBadge = match($service) {
                                                    'diy' => ['text' => 'DIY', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'solar:palette-2-linear'],
                                                    'full-service' => ['text' => 'Full Service', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'solar:star-shine-linear'],
                                                    default => null
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadge['class'] }} status-badge">
                                                {{ $statusBadge['text'] }}
                                            </span>

                                            @if($serviceBadge)
                                                <span class="badge {{ $serviceBadge['class'] }} status-badge tw-flex tw-items-center tw-gap-1">
                                                    <iconify-icon icon="{{ $serviceBadge['icon'] }}" class="tw-text-sm"></iconify-icon>
                                                    {{ $serviceBadge['text'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="tw-mt-4 tw-pt-3 tw-border-t tw-border-gray-200 tw-flex tw-items-center tw-justify-between">
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <iconify-icon icon="solar:calendar-outline" class="text-primary-light tw-text-lg"></iconify-icon>
                                        <span class="start-date text-secondary-light tw-text-sm">
                                            {{ \Carbon\Carbon::parse($item->order_date)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="tw-text-primary tw-font-bold tw-text-base">
                                        {{ getFormattedCurrency($item->total) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(count($orders) <= 0)
                    <x-empty-item/>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-xl-6" wire:ignore>
            <div class="card h-100 radius-8 border-0 overflow-hidden">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg">{{$lang->data['payment_overview'] ?? 'Payment Overview'}}</h6>
                    </div>

                    <!-- Total Order Amount Display -->
                    <div class="tw-mb-4 tw-p-3 tw-bg-gray-50 tw-rounded-lg">
                        <p class="tw-text-xs tw-text-gray-500 tw-mb-1">{{$lang->data['total_order_value'] ?? 'Total Order Value'}}</p>
                        <h5 class="tw-text-xl tw-font-bold tw-text-gray-800 tw-mb-0">{{ getFormattedCurrency($total_order_amount) }}</h5>
                    </div>

                    <div id="userOverviewDonutChart"></div>

                    <ul class="d-flex flex-wrap align-items-center justify-content-center mt-3 gap-3">
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-12-px h-12-px radius-2 tw-bg-[#2dce89]"></span>
                            <span class="text-secondary-light text-sm fw-semibold">{{$lang->data['paid_amount'] ?? 'Paid Amount'}}
                            </span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="w-12-px h-12-px radius-2 tw-bg-[#ff6b6b]"></span>
                            <span class="text-secondary-light text-sm fw-semibold">{{$lang->data['unpaid_amount'] ?? 'Unpaid Amount'}}
                            </span>
                        </li>
                    </ul>

                    <!-- Payment Breakdown -->
                    <div class="tw-mt-4 tw-space-y-2">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <span class="tw-text-xs tw-text-gray-600">{{$lang->data['paid'] ?? 'Paid'}}</span>
                            <span class="tw-text-sm tw-font-bold tw-text-green-600">{{ getFormattedCurrency($total_paid_amount) }}</span>
                        </div>
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <span class="tw-text-xs tw-text-gray-600">{{$lang->data['unpaid'] ?? 'Unpaid'}}</span>
                            <span class="tw-text-sm tw-font-bold tw-text-red-600">{{ getFormattedCurrency($total_unpaid_amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<input type="hidden" name="" id="chartdata" value="{{$array}}">
</div>

    @push('js')
        <script>
        var chartdata = document.getElementById("chartdata").value;
        var options = {
                series: JSON.parse(chartdata),
                labels: ['Paid Amount', 'Unpaid Amount'],
                legend: {
                    show: false
                },
                colors: ['#2dce89', '#ff6b6b'],

                chart: {
                    type: 'donut',
                    height: 270,
                    sparkline: {
                        enabled: true // Remove whitespace
                    },
                    margin: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },

                },
                stroke: {
                    width: 0,
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Payment Rate',
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#6b7280',
                                    formatter: function (w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        const paid = w.globals.series[0];
                                        const percentage = total > 0 ? ((paid / total) * 100).toFixed(1) : 0;
                                        return percentage + '%';
                                    }
                                }
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
            };
            var chart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), options);
            chart.render();
        </script>
    @endpush
</div>