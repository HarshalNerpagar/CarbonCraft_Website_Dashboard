<div class="dashboard-main-body">
    <div class="row gy-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card h-100">
                <!-- Header -->
                <div class="card-header border-bottom bg-neutral-50 py-16 px-24">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h6 class="text-lg mb-0">{{ $sitename }}</h6>
                            <p class="text-sm text-secondary-light mb-0 mt-1">{{$phone ? getCountryCode() : ''}}{{ (int)$phone }} | {{ $store_email }}</p>
                            <p class="text-sm text-secondary-light mb-0">{{ $address }} - {{ $zipcode }}</p>
                            <p class="text-sm text-secondary-light mb-0 mt-1">{{ $lang->data['tax'] ?? 'TAX' }}: {{ $tax_number }}</p>
                        </div>
                        <div class="text-end">
                            <h6 class="text-primary-600 mb-2">#{{ $order->order_number }}</h6>
                            <p class="text-sm text-secondary-light mb-1">{{ $lang->data['order_date'] ?? 'Order Date' }}: {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</p>
                            <p class="text-sm text-secondary-light mb-1">{{ $lang->data['delivery_date'] ?? 'Delivery Date' }}: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                            <div class="mt-2">
                                @can('order_status_change')
                                @if($order->status != 3 && $order->status != 4)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary-600 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        {{ getOrderStatus($order->status) }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="changeStatus(1)">{{ $lang->data['processing'] ?? 'Processing' }}</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="changeStatus(2)">{{ $lang->data['ready_to_deliver'] ?? 'Ready To Deliver' }}</a></li>
                                        <li>
                                            @if($balance > 0)
                                            <button disabled class="dropdown-item text-muted">{{ $lang->data['delivered'] ?? 'Delivered' }} <span class="text-danger text-xs">({{ $lang->data['payment_incomplete'] ?? 'Payment Incomplete' }})</span></button>
                                            @else
                                            <a class="dropdown-item" href="#" wire:click.prevent="changeStatus(3)">{{ $lang->data['delivered'] ?? 'Delivered' }}</a>
                                            @endif
                                        </li>
                                        <li><a class="dropdown-item" href="#" wire:click.prevent="changeStatus(4)">{{ $lang->data['returned'] ?? 'Returned' }}</a></li>
                                    </ul>
                                </div>
                                @else
                                <span class="badge {{ $order->status == 4 ? 'bg-danger' : 'bg-success' }}">
                                    {{ getOrderStatus($order->status) }}
                                </span>
                                @endif
                                @endcan
                                @cannot('order_status_change')
                                <span class="badge bg-secondary">{{ getOrderStatus($order->status) }}</span>
                                @endcannot
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items Table -->
                <div class="card-body p-24">
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ $lang->data['service_name'] ?? 'Service Name' }}</th>
                                    <th>{{ $lang->data['color'] ?? 'Color' }}</th>
                                    <th>{{ $lang->data['rate'] ?? 'Rate' }}</th>
                                    <th>{{ $lang->data['qty'] ?? 'QTY' }}</th>
                                    <th>{{ $lang->data['total'] ?? 'Total' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderdetails as $item)
                                @php
                                    $service = \App\Models\Service::where('id', $item->service_id)->first();
                                @endphp
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($service)
                                            <img src="{{ asset('assets/img/service-icons/' . $service->icon) }}" class="w-32-px" alt="">
                                            @endif
                                            <div>
                                                <p class="mb-0">{{ $service->service_name ?? '' }}</p>
                                                <p class="text-xs text-secondary-light mb-0">[{{$item->service_name}}]</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->color_code)
                                        <div class="w-24-px h-24-px rounded" style="background-color: {{$item->color_code}}"></div>
                                        @endif
                                    </td>
                                    <td>{{ getFormattedCurrency($item->service_price) }}</td>
                                    <td>{{ $item->service_quantity }}</td>
                                    <td>{{ getFormattedCurrency($item->service_detail_total) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Invoice To & Payment Details -->
                    <div class="row mt-24">
                        <div class="col-md-6">
                            <h6 class="mb-12">{{ $lang->data['invoice_to'] ?? 'Invoice To' }}</h6>
                            <p class="mb-4 fw-semibold">{{ $customer->name ?? 'Walk-In Customer' }}</p>
                            <p class="mb-4 text-sm">{{$customer && $customer->phone ? getCountryCode() : ''}} {{ $customer && $customer->phone ? (int)$customer->phone : 'Phone' }}</p>
                            <p class="mb-4 text-sm">{{ $customer->email ?? 'Email' }}</p>
                            @if($customer && $customer->address)
                            <p class="mb-4 text-sm">{{ $customer->address }}</p>
                            @endif
                            <p class="mb-0 text-sm">{{ $lang->data['vat'] ?? 'VAT' }}: {{ $customer->tax_number ?? 'TAX' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-12">{{ $lang->data['payment_details'] ?? 'Payment Details' }}</h6>
                            <div class="d-flex justify-content-between mb-8">
                                <span class="text-sm">{{ $lang->data['sub_total'] ?? 'Sub Total' }}</span>
                                <span class="text-sm">{{ getFormattedCurrency($order->sub_total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-8">
                                <span class="text-sm">{{ $lang->data['addon'] ?? 'Addon' }}</span>
                                <span class="text-sm">{{ getFormattedCurrency($order->addon_total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-8">
                                <span class="text-sm">{{ $lang->data['discount'] ?? 'Discount' }}</span>
                                <span class="text-sm">{{ getFormattedCurrency($order->discount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-12 pb-12 border-bottom">
                                <span class="text-sm">{{ $lang->data['tax'] ?? 'Tax' }} ({{ $order->tax_percentage }}%)</span>
                                <span class="text-sm">{{ getFormattedCurrency($order->tax_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">{{ $lang->data['gross_total'] ?? 'Gross Total' }}</span>
                                <span class="fw-bold text-primary-600">{{ getFormattedCurrency($order->total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($order->note)
                    <div class="mt-24 p-16 bg-warning-50 border-start border-warning-600 border-3 radius-8">
                        <p class="mb-4 fw-semibold">{{ $lang->data['notes'] ?? 'Notes' }}</p>
                        <p class="mb-0 text-sm">{{ $order->note }}</p>
                    </div>
                    @endif

                    <!-- Website Order Customization -->
                    @if($order->order_source === 'online')
                    <div class="mt-24 p-20 bg-info-50 radius-8 border border-info-600">
                        <h6 class="mb-16 text-info-600">
                            <iconify-icon icon="mdi:web" class="text-xl"></iconify-icon>
                            Website Order - Customization Details
                        </h6>
                        @php
                            $customization = json_decode($order->customization_data, true);
                        @endphp
                        <div class="row g-3">
                            @if(isset($customization['service']))
                            <div class="col-md-6">
                                <p class="mb-4 text-sm text-secondary-light">Service Type</p>
                                <p class="mb-0 fw-semibold">{{ $customization['service'] === 'diy' ? 'DIY Service' : 'Full Service' }}</p>
                            </div>
                            @endif
                            @if(isset($customization['selectedColor']))
                            <div class="col-md-6">
                                <p class="mb-4 text-sm text-secondary-light">Metal Finish</p>
                                <p class="mb-0 fw-semibold text-capitalize">{{ str_replace('-', ' ', $customization['selectedColor']) }}</p>
                            </div>
                            @endif
                            @if(isset($customization['namePosition']))
                            <div class="col-md-6">
                                <p class="mb-4 text-sm text-secondary-light">Name Position</p>
                                <p class="mb-0 fw-semibold text-capitalize">{{ $customization['namePosition'] }}</p>
                            </div>
                            @endif
                            @if(isset($customization['paymentOption']))
                            <div class="col-md-6">
                                <p class="mb-4 text-sm text-secondary-light">Payment Method</p>
                                <p class="mb-0 fw-semibold text-capitalize">{{ str_replace('-', ' ', $customization['paymentOption']) }}</p>
                            </div>
                            @endif
                            @if(isset($customization['needsPickup']) && $customization['needsPickup'])
                            <div class="col-12">
                                <div class="p-12 bg-success-50 radius-8">
                                    <p class="mb-0 fw-semibold text-success-600">
                                        <iconify-icon icon="mdi:truck-delivery"></iconify-icon>
                                        Pickup Service Requested (+₹350)
                                    </p>
                                </div>
                            </div>
                            @endif
                            @if(isset($customization['requirements']) && $customization['requirements'])
                            <div class="col-12">
                                <p class="mb-4 text-sm text-secondary-light">Custom Requirements</p>
                                <p class="mb-0">{{ $customization['requirements'] }}</p>
                            </div>
                            @endif
                        </div>
                        @if($order->razorpay_payment_id)
                        <div class="mt-16 p-16 bg-white radius-8 border">
                            <p class="mb-12 fw-semibold">Razorpay Payment Details</p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <p class="mb-4 text-xs text-secondary-light">Payment ID</p>
                                    <p class="mb-0 text-sm font-monospace">{{ $order->razorpay_payment_id }}</p>
                                </div>
                                @if($order->razorpay_order_id)
                                <div class="col-md-6">
                                    <p class="mb-4 text-xs text-secondary-light">Order ID</p>
                                    <p class="mb-0 text-sm font-monospace">{{ $order->razorpay_order_id }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-24">
                    <!-- Service Addons -->
                    @if ($orderaddons && count($orderaddons) > 0)
                    <h6 class="mb-16">{{ $lang->data['service_addons'] ?? 'Service Addons' }}</h6>
                    @foreach ($orderaddons as $item)
                    <div class="mb-12 p-12 bg-success-50 radius-8">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="tabler:puzzle" class="text-xl text-success-600"></iconify-icon>
                            <div>
                                <p class="mb-0 fw-medium">{{ $item->addon_name }}</p>
                                <p class="mb-0 text-sm text-success-600">{{ getFormattedCurrency($item->addon_price) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <hr class="my-20">
                    @endif

                    <!-- Payments -->
                    @can('payment_create')
                    <h6 class="mb-16">{{ $lang->data['payments'] ?? 'Payments' }}</h6>
                    @if(count($payments) > 0)
                    @foreach ($payments as $item)
                    <div class="mb-12 p-12 bg-primary-50 radius-8">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-4 fw-semibold">{{ getFormattedCurrency($item->received_amount) }}</p>
                                <p class="mb-0 text-xs text-secondary-light">{{ Carbon\Carbon::parse($item->payment_date)->format('d/m/Y') }}</p>
                            </div>
                            <span class="badge bg-primary-600">{{ getpaymentMode($item->payment_type) }}</span>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-center text-secondary-light py-16">No payments recorded yet</p>
                    @endif

                    <!-- Payment Actions -->
                    <div class="mt-20">
                        @if ($balance > 0)
                            @if($order->status != 4)
                            <button data-bs-toggle="modal" data-bs-target="#exampleModal" type="button" class="btn btn-success-600 w-100 mb-12">
                                {{ $lang->data['add_payment'] ?? 'Add Payment' }}
                            </button>
                            @endif
                        @else
                        <button type="button" class="btn btn-success-600 w-100 mb-12" disabled>
                            {{ $lang->data['fully_paid'] ?? 'Fully Paid' }}
                        </button>
                        @endif

                        @can('order_print')
                        <a href="{{url('admin/orders/print/'.$order->id)}}" target="_blank" class="btn btn-warning-600 w-100">
                            {{ $lang->data['print_invoice'] ?? 'Print Invoice' }}
                        </a>
                        @endcan
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border-bottom">
                    <h1 class="modal-title text-md" id="exampleModalLabel">{{ $lang->data['payment_details'] ?? 'Payment Details' }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($order)
                <div class="modal-body p-24">
                    <form>
                        <div class="mb-16">
                            <div class="d-flex justify-content-between mb-8">
                                <span class="fw-semibold">{{ $lang->data['customer'] ?? 'Customer' }}:</span>
                                <span>{{ $customer->name ?? '' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-8">
                                <span class="fw-semibold">{{ $lang->data['order_id'] ?? 'Order ID' }}:</span>
                                <span>{{ $order->order_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-8">
                                <span class="fw-semibold">{{ $lang->data['order_amount'] ?? 'Order Amount' }}:</span>
                                <span>{{ getFormattedCurrency($order->total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-8">
                                <span class="fw-semibold">{{ $lang->data['paid_amount'] ?? 'Paid Amount' }}:</span>
                                <span>{{ getFormattedCurrency($order->total - $balance) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">{{ $lang->data['balance'] ?? 'Balance' }}:</span>
                                <span class="text-danger">{{ getFormattedCurrency($balance) }}</span>
                            </div>
                        </div>
                        <hr class="my-16">
                        <div class="mb-16">
                            <label class="form-label">{{ $lang->data['paid_amount'] ?? 'Paid Amount' }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="paid_amount">
                            @error('balance')
                            <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-16">
                            <label class="form-label">{{ $lang->data['payment_type'] ?? 'Payment Type' }} <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="payment_type">
                                <option value="2" selected>{{ $lang->data['online'] ?? 'Online' }}</option>
                                <option value="1">{{ $lang->data['cash'] ?? 'Cash' }}</option>
                            </select>
                            @error('payment_type')
                            <span class="text-danger text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-16">
                            <label class="form-label">{{ $lang->data['notes'] ?? 'Notes' }}</label>
                            <textarea class="form-control" wire:model="notes"></textarea>
                        </div>
                        <div class="d-flex gap-3 justify-content-end">
                            <button data-bs-dismiss="modal" type="button" class="btn btn-outline-danger">
                                {{ $lang->data['cancel'] ?? 'Cancel' }}
                            </button>
                            <button wire:click.prevent="addPayment" type="button" class="btn btn-primary">
                                {{ $lang->data['save'] ?? 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
