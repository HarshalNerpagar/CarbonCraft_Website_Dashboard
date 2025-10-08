<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\PreOrderToken;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GenerateCustomerLink extends Component
{
    // Form fields
    public $total_amount = '';  // Total order amount (required)
    public $advance_amount = 1000;  // Advance payment (default 1000)
    public $payment_method = 'upi';  // Hidden field
    public $notes = '';  // Optional notes

    // Generated data
    public $generatedUrl = '';
    public $showSuccessModal = false;

    // Token list
    public $tokens = [];

    #[Title('Generate Customer Link')]

    public function mount()
    {
        $this->loadTokens();
    }

    public function loadTokens()
    {
        $this->tokens = PreOrderToken::where('agent_id', Auth::id())
            ->with('order:id,order_number,total,status')
            ->latest()
            ->take(20)
            ->get();
    }

    public function generateLink()
    {
        // Validate
        $this->validate([
            'total_amount' => 'required|numeric|min:500|max:500000',
            'advance_amount' => 'required|numeric|min:100|max:100000',
            'notes' => 'nullable|string|max:1000',
        ], [
            'total_amount.required' => 'Total order amount is required',
            'total_amount.min' => 'Total amount must be at least ₹500',
            'advance_amount.min' => 'Advance amount must be at least ₹100',
        ]);

        // Validate advance is not more than total
        if ($this->advance_amount > $this->total_amount) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Advance amount cannot be greater than total amount'
            ]);
            return;
        }

        try {
            // Generate unique token
            $token = PreOrderToken::generateUniqueToken();

            // Create token record
            PreOrderToken::create([
                'token' => $token,
                'agent_id' => Auth::id(),
                'payment_method' => $this->payment_method,
                'advance_amount' => $this->advance_amount,
                'total_amount' => $this->total_amount,
                'customer_phone' => null,
                'customer_name' => null,
                'notes' => $this->notes,
                'expires_at' => Carbon::now()->addHours(48),
            ]);

            // Generate URL
            $this->generatedUrl = env('WEBSITE_URL', 'http://localhost:3000') . '/order/' . $token;
            $this->showSuccessModal = true;

            // Reload tokens
            $this->loadTokens();

            // Reset form
            $this->reset(['total_amount', 'notes']);
            $this->advance_amount = 1000; // Reset to default

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Customer link generated successfully!'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Error generating link: ' . $e->getMessage()
            ]);
        }
    }

    public function copyToClipboard()
    {
        $this->dispatch('copy-to-clipboard', url: $this->generatedUrl);
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
        $this->generatedUrl = '';
    }

    public function render()
    {
        return view('livewire.staff.generate-customer-link');
    }
}
