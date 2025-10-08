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
    public $payment_method = 'upi';
    public $advance_amount = 1000;
    public $customer_phone = '';
    public $customer_name = '';
    public $notes = '';

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
            'payment_method' => 'required|in:upi,razorpay,cash',
            'advance_amount' => 'required|numeric|min:100|max:100000',
            'customer_phone' => 'nullable|string|max:15',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Generate unique token
            $token = PreOrderToken::generateUniqueToken();

            // Create token record
            PreOrderToken::create([
                'token' => $token,
                'agent_id' => Auth::id(),
                'payment_method' => $this->payment_method,
                'advance_amount' => $this->advance_amount,
                'customer_phone' => $this->customer_phone,
                'customer_name' => $this->customer_name,
                'notes' => $this->notes,
                'expires_at' => Carbon::now()->addHours(48),
            ]);

            // Generate URL
            $this->generatedUrl = env('WEBSITE_URL', 'http://localhost:4321') . '/order/' . $token;
            $this->showSuccessModal = true;

            // Reload tokens
            $this->loadTokens();

            // Reset form
            $this->reset(['customer_phone', 'customer_name', 'notes']);

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
