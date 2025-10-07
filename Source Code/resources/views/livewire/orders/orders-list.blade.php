<div class="dashboard-main-body">
    <div class="card h-100 p-0">
        <div class="tw-py-1.5 tw-px-3 bg-base d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <form class="navbar-search">
                    <input type="text" class="bg-base tw-px-3 tw-py-1.5 w-auto" name="search" placeholder="{{ $lang->data['search_here'] ?? 'Search Here' }}" wire:model.live="search_query">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            @can('order_create')
            <a href="{{route('orders.pos')}}" type="button" class="btn btn-primary text-sm btn-sm radius-8 d-flex align-items-center gap-2" >
                <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                {{ $lang->data['add_new_order'] ?? 'Add New Order' }}
            </a>
            @endcan
        </div>
        <div class="tw-p-0">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                  <thead>
                    <tr>
                      <th scope="col" class="">{{ $lang->data['order_info'] ?? 'Order Info' }}</th>
                      <th scope="col" class="">{{ $lang->data['customer'] ?? 'Customer' }}</th>
                      <th scope="col" class="">{{ $lang->data['order_amount'] ?? 'Order Amount' }}</th>
                      <th scope="col" class=""> {{ $lang->data['status'] ?? 'Status' }}</th>
                      <th scope="col" class="">{{ $lang->data['payment'] ?? 'Payment' }}</th>
                      <th scope="col" class=""> {{ $lang->data['created_by'] ?? 'Created By' }}</th>
                      <th scope="col" class="text-center">{{ $lang->data['action'] ?? 'Action' }}</th>
                    </tr>
                  </thead>
                  <tbody>
                        @foreach ($orders as $item)
                        <tr class="tw-text-xs smooth-transition hover:tw-bg-gray-50">
                            <td>
                                <div class="tw-flex tw-flex-col tw-gap-1">
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <iconify-icon icon="mdi:receipt-text-outline" class="tw-text-primary tw-text-base"></iconify-icon>
                                        <span class="tw-font-bold text-primary tw-text-sm">{{ $item->order_number }}</span>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-text-neutral-600">
                                        <iconify-icon icon="solar:calendar-outline" class="tw-text-xs"></iconify-icon>
                                        <span class="tw-text-xs">{{ \Carbon\Carbon::parse($item->order_date)->format('d/m/y') }}</span>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-text-neutral-600">
                                        <iconify-icon icon="solar:delivery-outline" class="tw-text-xs"></iconify-icon>
                                        <span class="tw-text-xs">{{ \Carbon\Carbon::parse($item->delivery_date)->format('d/m/y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="tw-flex tw-flex-col">
                                    <p class="tw-font-semibold tw-text-gray-800 tw-mb-1">
                                        {{ $item->customer_name ?? ($lang->data['walk_in_customer'] ?? 'Walk In Customer') }}
                                    </p>
                                    @if($item->phone_number)
                                    <p class="tw-text-gray-600 tw-flex tw-items-center tw-gap-1">
                                        <iconify-icon icon="ph:phone" class="tw-text-xs"></iconify-icon>
                                        {{getCountryCode()}}{{(int)$item->phone_number}}
                                    </p>
                                    @endif
                                </div>
                            </td>
                            <td class="text-primary">
                                <span class="tw-font-bold tw-text-base">{{ getFormattedCurrency($item->total) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = match($item->status) {
                                        0 => ['text' => $lang->data['advance_done'] ?? 'Advance Done', 'class' => 'text-cyan-600 bg-cyan-100', 'icon' => 'solar:cart-check-outline'],
                                        1 => ['text' => $lang->data['design_ready'] ?? 'Design Ready', 'class' => 'text-purple-600 bg-purple-100', 'icon' => 'solar:document-text-outline'],
                                        2 => ['text' => $lang->data['ready_to_deliver'] ?? 'Ready To Deliver', 'class' => 'text-blue-600 bg-blue-100', 'icon' => 'solar:box-outline'],
                                        3 => ['text' => $lang->data['delivered'] ?? 'Delivered Orders', 'class' => 'text-success-600 bg-success-100', 'icon' => 'solar:check-circle-outline'],
                                        4 => ['text' => $lang->data['returned'] ?? 'Returned', 'class' => 'text-danger-600 bg-danger-100', 'icon' => 'solar:close-circle-outline'],
                                        default => ['text' => 'Unknown', 'class' => 'text-gray-600 bg-gray-100', 'icon' => 'solar:question-circle-outline']
                                    };
                                @endphp
                                <span class="badge {{ $statusConfig['class'] }} status-badge tw-flex tw-items-center tw-gap-1 tw-w-fit">
                                    <iconify-icon icon="{{ $statusConfig['icon'] }}" class="tw-text-sm"></iconify-icon>
                                    {{ $statusConfig['text'] }}
                                </span>
                            </td>
                            <td>
                                @php
                                // Use preloaded payment sum to avoid N+1 queries
                                $paidamount = $item->paid_amount ?? 0;
                                $paymentPercentage = $item->total > 0 ? ($paidamount / $item->total) * 100 : 0;
                                @endphp
                                <div class="tw-flex tw-flex-col tw-gap-2">
                                    <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                                        <span class="tw-text-xs tw-text-gray-600">Paid:</span>
                                        <span class="tw-font-bold tw-text-sm text-success-600">{{ getFormattedCurrency($paidamount) }}</span>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-justify-between tw-gap-2">
                                        <span class="tw-text-xs tw-text-gray-600">Balance:</span>
                                        <span class="tw-font-bold tw-text-sm text-danger-600">{{ getFormattedCurrency($item->total - $paidamount) }}</span>
                                    </div>

                                    <!-- Payment Progress Bar -->
                                    <div class="tw-w-full tw-h-1.5 tw-bg-gray-200 tw-rounded-full tw-overflow-hidden">
                                        <div class="tw-h-full tw-bg-success-600 smooth-transition" style="width: {{ $paymentPercentage }}%"></div>
                                    </div>

                                    @if ($paidamount < $item->total && $item->status != 4)
                                        @can('payment_create')
                                            <button type="button"
                                                    class="btn btn-sm rounded-pill btn-success-100 text-success-600 radius-8 tw-text-xs tw-py-1 tw-px-3 smooth-transition hover:tw-scale-105"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal"
                                                    wire:click="payment({{ $item->id }})">
                                                <iconify-icon icon="solar:wallet-money-outline" class="tw-mr-1"></iconify-icon>
                                                {{ $lang->data['add_payment'] ?? 'Add Payment' }}
                                            </button>
                                        @endcan
                                    @elseif($item->status != 4)
                                        <span class="badge bg-success-100 text-success-600 status-badge tw-w-fit">
                                            <iconify-icon icon="solar:check-circle-outline"></iconify-icon>
                                            {{ $lang->data['fully_paid'] ?? 'Fully Paid' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $createdBy = $item->user->name ?? 'Website';
                                    $badgeClass = match($createdBy) {
                                        'Siddhi' => 'bg-warning-100 text-warning-600',
                                        'Sayali' => 'bg-purple-100 text-purple-600',
                                        'Hitesh' => 'bg-info-100 text-info-600',
                                        'Website' => 'bg-success-100 text-success-600',
                                        default => 'bg-secondary-100 text-secondary-600'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} status-badge">{{ $createdBy }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center tw-gap-2 justify-content-center">
                                    @can('order_view')
                                    <a href="{{route('order.view',$item->id)}}"
                                       class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium tw-size-9 d-flex justify-content-center align-items-center rounded-circle smooth-transition hover:tw-scale-110"
                                       data-bs-toggle="tooltip"
                                       title="View Order">
                                        <iconify-icon icon="lucide:eye" class="tw-text-base"></iconify-icon>
                                    </a>
                                    @endcan
                                    @can('order_print')
                                    <a href="{{route('order.print',$item->id)}}"
                                       target="_blank"
                                       class="bg-warning-100 text-warning-600 bg-hover-warning-200 fw-medium tw-size-9 d-flex justify-content-center align-items-center rounded-circle smooth-transition hover:tw-scale-110"
                                       data-bs-toggle="tooltip"
                                       title="Print Invoice">
                                        <iconify-icon icon="material-symbols-light:print-outline" class="tw-text-lg"></iconify-icon>
                                    </a>
                                    @endcan
                                    @can('order_edit')
                                    <a href="{{route('orders.pos.edit',$item->id)}}"
                                       class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium tw-size-9 d-flex justify-content-center align-items-center rounded-circle smooth-transition hover:tw-scale-110"
                                       data-bs-toggle="tooltip"
                                       title="Edit Order">
                                        <iconify-icon icon="lucide:edit" class="tw-text-base"></iconify-icon>
                                    </a>
                                    @endcan
                                    @can('order_delete')
                                    <button type="button"
                                            wire:click.prevent="deleteOrder({{$item->id}})"
                                            class="remove-item-button bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium tw-size-9 d-flex justify-content-center align-items-center rounded-circle smooth-transition hover:tw-scale-110"
                                            data-bs-toggle="tooltip"
                                            title="Delete Order">
                                        <iconify-icon icon="fluent:delete-24-regular" class="tw-text-base"></iconify-icon>
                                    </button>
                                    @endcan
                                </div>
                            </td> 
                        </tr>
                        @endforeach
                  </tbody>
                </table>
                @if(count($orders) == 0)
                    <x-empty-item/>
                @endif
                @if($hasMorePages)
                <div x-data="{
                        init () {
                            let observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        @this.call('loadOrders')
                                        console.log('loading...')
                                    }
                                })
                            }, {
                                root: null
                            });
                            observer.observe(this.$el);
                        }
                    }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-4">
                    <div class="text-center pb-2 d-flex justify-content-center align-items-center">
                    {{ $lang->data['loading'] ?? 'Loading...' }}
                        <div class="spinner-grow d-inline-flex mx-2 text-primary" role="status">
                            <span class="visually-hidden"> {{ $lang->data['loading'] ?? 'Loading...' }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title text-md" id="exampleModalLabel">{{ $lang->data['payment_details'] ?? 'Payment Details' }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($order)
                    <div class="modal-body p-24">
                        <form action="#">
                            <div class="row">   
                                <div class="col-12">
                                    <div class="">
                                        <ul>
                                            <li class="d-flex align-items-center gap-1 mb-12 tw-justify-between tw-w-full">
                                                <span class="text-md fw-semibold text-primary-light">{{ $lang->data['customer'] ?? 'Customer' }} :</span>
                                                <span class="text-secondary-light fw-medium">{{ $customer_name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1 mb-12 tw-justify-between ">
                                                <span class="text-md fw-semibold text-primary-light"> {{ $lang->data['order_id'] ?? 'Order ID' }} :</span>
                                                <span class="text-secondary-light fw-medium">{{ $order->order_number }}</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1 mb-12 tw-justify-between">
                                                <span class="text-md fw-semibold text-primary-light">  {{ $lang->data['order_date'] ?? 'Order Date' }} :</span>
                                                <span class="text-secondary-light fw-medium">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1 mb-12 tw-justify-between">
                                                <span class="text-md fw-semibold text-primary-light">  {{ $lang->data['delivery_date'] ?? 'Delivery Date' }} :</span>
                                                <span class="text-secondary-light fw-medium">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1 mb-12 tw-justify-between">
                                                <span class="text-md fw-semibold text-primary-light"> {{ $lang->data['order_amount'] ?? 'Order Amount' }} :</span>
                                                <span class="text-secondary-light fw-medium"> {{ getFormattedCurrency($order->total) }}</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1 mb-12 tw-justify-between">
                                                <span class="text-md fw-semibold text-primary-light"> {{ $lang->data['paid_amount'] ?? 'Paid Amount' }} :</span>
                                                <span class="text-secondary-light fw-medium"> {{ getFormattedCurrency($paid_amount) }}</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-1 tw-justify-between">
                                                <span class="text-md fw-semibold text-primary-light"> {{ $lang->data['balance'] ?? 'Balance' }} :</span>
                                                <span class="text-secondary-light fw-medium"> {{ getFormattedCurrency($order->total - $paid_amount) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-12 tw-my-6">
                                    <hr>
                                </div>
                                <div class="col-12 mb-20 ">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['paid_amount'] ?? 'Paid Amount' }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_amount'] ?? 'Enter Amount' }}" wire:model="balance" >
                                    @error('balance')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-20 ">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['payment_type'] ?? 'Payment Type' }} <span class="text-danger">*</span></label>
                                    <select class="form-select radius-8" wire:model="payment_type">
                                        <option value="2" selected>{{ $lang->data['online'] ?? 'Online' }}</option>
                                        <option value="1">{{ $lang->data['cash'] ?? 'Cash' }}</option>
                                    </select>
                                    @error('payment_type')
                                    <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['notes'] ?? 'Notes' }} </label>
                                    <textarea class="form-control radius-8" placeholder="{{ $lang->data['enter_notes'] ?? 'Enter Notes' }}"  wire:model="note"></textarea>
                                    @error('note')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="d-flex align-items-start justify-content-end gap-3 mt-24">
                                    <button data-bs-dismiss="modal" type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8"> 
                                    {{ $lang->data['cancel'] ?? 'Cancel' }}
                                    </button>
                                    <button type="button" wire:click.prevent="addPayment()" class="btn btn-primary border border-primary-600 text-md px-24 py-12 radius-8"> 
                                    {{ $lang->data['save'] ?? 'Save' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>