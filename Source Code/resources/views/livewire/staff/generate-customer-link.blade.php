<div class="dashboard-main-body">
    <div class="row gy-4">
        <!-- Generate Link Form -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-3 tw-mb-6">
                        <div class="tw-w-12 tw-h-12 tw-bg-primary-100 tw-rounded-xl tw-flex tw-items-center tw-justify-center">
                            <iconify-icon icon="solar:link-circle-bold" class="tw-text-primary-600 tw-text-2xl"></iconify-icon>
                        </div>
                        <div>
                            <h5 class="tw-text-lg tw-font-bold tw-mb-0">Generate Customer Link</h5>
                            <p class="tw-text-sm tw-text-gray-500 tw-mb-0">Create order link after collecting advance</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="generateLink">
                        <!-- Payment Method -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold">Payment Method Collected</label>
                            <div class="tw-grid tw-grid-cols-3 tw-gap-2">
                                <label class="tw-cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="upi" class="tw-hidden peer">
                                    <div class="tw-border-2 tw-border-gray-200 peer-checked:tw-border-primary-600 peer-checked:tw-bg-primary-50 tw-rounded-lg tw-p-3 tw-text-center smooth-transition">
                                        <div class="tw-text-2xl tw-mb-1">💳</div>
                                        <div class="tw-text-sm tw-font-medium">UPI</div>
                                    </div>
                                </label>
                                <label class="tw-cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="razorpay" class="tw-hidden peer">
                                    <div class="tw-border-2 tw-border-gray-200 peer-checked:tw-border-primary-600 peer-checked:tw-bg-primary-50 tw-rounded-lg tw-p-3 tw-text-center smooth-transition">
                                        <div class="tw-text-2xl tw-mb-1">💎</div>
                                        <div class="tw-text-sm tw-font-medium">Razorpay</div>
                                    </div>
                                </label>
                                <label class="tw-cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="cash" class="tw-hidden peer">
                                    <div class="tw-border-2 tw-border-gray-200 peer-checked:tw-border-primary-600 peer-checked:tw-bg-primary-50 tw-rounded-lg tw-p-3 tw-text-center smooth-transition">
                                        <div class="tw-text-2xl tw-mb-1">💰</div>
                                        <div class="tw-text-sm tw-font-medium">Cash</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Advance Amount -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold">Advance Amount Collected</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" wire:model="advance_amount" class="form-control" placeholder="1000" min="100" step="100">
                            </div>
                        </div>

                        <!-- Customer Name -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold">Customer Name <small class="tw-text-gray-400">(Optional)</small></label>
                            <input type="text" wire:model="customer_name" class="form-control" placeholder="e.g., Rahul Verma">
                        </div>

                        <!-- Customer Phone -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold">Customer WhatsApp <small class="tw-text-gray-400">(Optional)</small></label>
                            <input type="tel" wire:model="customer_phone" class="form-control" placeholder="+91 98765 43210">
                        </div>

                        <!-- Notes -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold">Notes <small class="tw-text-gray-400">(Optional)</small></label>
                            <textarea wire:model="notes" class="form-control" rows="2" placeholder="Any notes..."></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100 tw-py-3 tw-font-semibold">Generate Link</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Tokens -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body tw-p-6">
                    <h5 class="tw-font-bold tw-mb-4">Recent Links ({{ count($tokens) }})</h5>
                    <div class="tw-space-y-3 tw-max-h-[600px] tw-overflow-y-auto">
                        @forelse($tokens as $token)
                            <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4 tw-border">
                                <div class="tw-flex tw-justify-between">
                                    <div class="tw-flex-1">
                                        <div class="tw-flex tw-gap-2 tw-mb-2">
                                            @if($token->used)
                                                <span class="badge bg-success-600">Used</span>
                                            @elseif($token->isValid())
                                                <span class="badge bg-primary-600">Active</span>
                                            @else
                                                <span class="badge bg-danger-600">Expired</span>
                                            @endif
                                            <span class="badge bg-gray-200 text-gray-800">{{ strtoupper($token->payment_method) }}</span>
                                            <span class="tw-font-bold">₹{{ number_format($token->advance_amount) }}</span>
                                        </div>
                                        <div class="tw-text-sm">{{ $token->customer_name ?? 'No name' }}</div>
                                        <div class="tw-text-xs tw-text-gray-500 tw-mt-1">{{ $token->created_at->diffForHumans() }}</div>
                                        @if($token->order)
                                            <a href="{{ route('order.view', $token->order_id) }}" class="tw-text-sm tw-text-primary-600">
                                                Order: {{ $token->order->order_number }}
                                            </a>
                                        @endif
                                    </div>
                                    @if($token->isValid() && !$token->used)
                                        <button onclick="navigator.clipboard.writeText('{{ env('WEBSITE_URL') }}/order/{{ $token->token }}'); alert('Copied!')"
                                                class="btn btn-sm btn-outline-primary">
                                            Copy
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="tw-text-center tw-py-12 tw-text-gray-400">
                                <div class="tw-text-4xl tw-mb-2">🔗</div>
                                <p>No links generated yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Link Generated!</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body tw-text-center">
                        <div class="tw-text-6xl tw-text-success-600 tw-mb-3">✓</div>
                        <p class="tw-mb-4">Share this link with customer:</p>
                        <div class="tw-flex tw-gap-2 tw-mb-4">
                            <input type="text" value="{{ $generatedUrl }}" class="form-control" readonly>
                            <button onclick="navigator.clipboard.writeText('{{ $generatedUrl }}'); alert('Copied!')" class="btn btn-primary">Copy</button>
                        </div>
                        <a href="https://wa.me/?text={{ urlencode($generatedUrl) }}" target="_blank" class="btn btn-success w-100">Share on WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
