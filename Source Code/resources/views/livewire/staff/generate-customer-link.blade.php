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
                        <!-- Total Order Amount (Required) -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold tw-text-base">Total Order Amount <span class="tw-text-danger-600">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">₹</span>
                                <input type="number"
                                       wire:model.live="total_amount"
                                       class="form-control form-control-lg"
                                       placeholder="Enter total order amount"
                                       min="500"
                                       step="1"
                                       required>
                            </div>
                            @error('total_amount')
                                <div class="tw-text-danger-600 tw-text-sm tw-mt-1">{{ $message }}</div>
                            @enderror
                            <div class="tw-text-gray-500 tw-text-sm tw-mt-1">Minimum: ₹500</div>
                        </div>

                        <!-- Advance Payment (Default 1000) -->
                        <div class="tw-mb-4">
                            <label class="form-label tw-font-semibold tw-text-base">Advance Payment <span class="tw-text-danger-600">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">₹</span>
                                <input type="number"
                                       wire:model.live="advance_amount"
                                       class="form-control form-control-lg"
                                       placeholder="1000"
                                       min="100"
                                       step="100"
                                       required>
                            </div>
                            @error('advance_amount')
                                <div class="tw-text-danger-600 tw-text-sm tw-mt-1">{{ $message }}</div>
                            @enderror
                            <div class="tw-text-gray-500 tw-text-sm tw-mt-1">Amount already collected from customer</div>
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="tw-mb-5">
                            <label class="form-label tw-font-semibold tw-text-base">Notes <small class="tw-text-gray-400">(Optional)</small></label>
                            <textarea wire:model.live="notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Add any special notes or requirements..."></textarea>
                            @error('notes')
                                <div class="tw-text-danger-600 tw-text-sm tw-mt-1">{{ $message }}</div>
                            @enderror>
                        </div>

                        <!-- Remaining Amount Display -->
                        @if($total_amount && $advance_amount)
                            <div class="tw-bg-info-50 tw-border tw-border-info-200 tw-rounded-lg tw-p-3 tw-mb-4">
                                <div class="tw-flex tw-justify-between tw-items-center">
                                    <span class="tw-text-gray-700 tw-font-medium">Remaining Amount:</span>
                                    <span class="tw-text-lg tw-font-bold tw-text-info-600">₹{{ number_format($total_amount - $advance_amount) }}</span>
                                </div>
                                <div class="tw-text-xs tw-text-gray-500 tw-mt-1">To be collected on delivery</div>
                            </div>
                        @endif

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100 tw-py-3 tw-text-base tw-font-semibold">
                            <iconify-icon icon="solar:link-circle-bold" class="tw-mr-2"></iconify-icon>
                            Generate Customer Link
                        </button>

                        <div class="tw-text-center tw-text-sm tw-text-gray-500 tw-mt-3">
                            Link will be valid for 48 hours
                        </div>
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
                                        </div>
                                        <div class="tw-flex tw-gap-4 tw-text-sm tw-mb-1">
                                            <div>
                                                <span class="tw-text-gray-500">Total:</span>
                                                <span class="tw-font-bold tw-text-gray-900">₹{{ number_format($token->total_amount ?? 0) }}</span>
                                            </div>
                                            <div>
                                                <span class="tw-text-gray-500">Advance:</span>
                                                <span class="tw-font-semibold tw-text-success-600">₹{{ number_format($token->advance_amount) }}</span>
                                            </div>
                                            <div>
                                                <span class="tw-text-gray-500">Remaining:</span>
                                                <span class="tw-font-semibold tw-text-warning-600">₹{{ number_format(($token->total_amount ?? 0) - $token->advance_amount) }}</span>
                                            </div>
                                        </div>
                                        @if($token->notes)
                                            <div class="tw-text-xs tw-text-gray-600 tw-italic tw-mb-1">"{{ Str::limit($token->notes, 50) }}"</div>
                                        @endif
                                        <div class="tw-text-xs tw-text-gray-500">{{ $token->created_at->diffForHumans() }}</div>
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
