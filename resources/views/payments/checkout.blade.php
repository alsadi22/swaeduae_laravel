@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ __('Checkout') }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-8">
                    <form id="payment-form" class="space-y-6">
                        <!-- Billing Information -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Billing Information') }}</h2>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('First Name') }}</label>
                                        <input type="text" name="first_name" required 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                               value="{{ auth()->user()->first_name ?? '' }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Last Name') }}</label>
                                        <input type="text" name="last_name" required 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                               value="{{ auth()->user()->last_name ?? '' }}">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                                    <input type="email" name="email" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ auth()->user()->email ?? '' }}">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone') }}</label>
                                    <input type="tel" name="phone" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ auth()->user()->phone ?? '' }}">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Address') }}</label>
                                    <input type="text" name="address" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('City') }}</label>
                                        <input type="text" name="city" required 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Country') }}</label>
                                        <input type="text" name="country" required 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                               value="United Arab Emirates">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Payment Method') }}</h2>
                            
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border-2 border-blue-500 rounded-lg cursor-pointer">
                                    <input type="radio" name="payment_method" value="card" checked required>
                                    <span class="ml-3">
                                        <span class="block font-medium text-gray-900">{{ __('Credit / Debit Card') }}</span>
                                        <span class="block text-sm text-gray-500">{{ __('Visa, Mastercard, or American Express') }}</span>
                                    </span>
                                </label>

                                <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                                    <input type="radio" name="payment_method" value="wallet" required>
                                    <span class="ml-3">
                                        <span class="block font-medium text-gray-900">{{ __('Wallet Balance') }}</span>
                                        <span class="block text-sm text-gray-500">{{ __('Available: AED ') }}{{ auth()->user()->wallet_balance ?? '0.00' }}</span>
                                    </span>
                                </label>

                                <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-gray-400">
                                    <input type="radio" name="payment_method" value="bank_transfer" required>
                                    <span class="ml-3">
                                        <span class="block font-medium text-gray-900">{{ __('Bank Transfer') }}</span>
                                        <span class="block text-sm text-gray-500">{{ __('Direct bank transfer') }}</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Card Details (shown if card selected) -->
                        <div id="card-details" class="border-t border-gray-200 pt-6 space-y-4">
                            <h3 class="font-medium text-gray-900">{{ __('Card Details') }}</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Card Number') }}</label>
                                <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Expiry Date') }}</label>
                                    <input type="text" name="card_expiry" placeholder="MM/YY" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('CVV') }}</label>
                                    <input type="text" name="card_cvv" placeholder="123" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Terms & Conditions -->
                        <div class="border-t border-gray-200 pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="agree_terms" required class="rounded border-gray-300">
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ __('I agree to the ') }}
                                    <a href="#" class="text-blue-600 hover:text-blue-700">{{ __('Terms & Conditions') }}</a>
                                    {{ __(' and ') }}
                                    <a href="#" class="text-blue-600 hover:text-blue-700">{{ __('Privacy Policy') }}</a>
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                            {{ __('Complete Payment') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">{{ __('Order Summary') }}</h2>

                    <!-- Items -->
                    <div class="space-y-4 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ $item->name ?? 'Item' }}</span>
                            <span class="font-medium text-gray-900">AED {{ number_format($item->price ?? 0, 2) }}</span>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Subtotal') }}</span>
                            <span class="text-gray-900">AED {{ number_format($item->price ?? 0, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Tax (5%)') }}</span>
                            <span class="text-gray-900">AED {{ number_format(($item->price ?? 0) * 0.05, 2) }}</span>
                        </div>

                        <div class="flex justify-between pt-3 border-t border-gray-200">
                            <span class="font-bold text-gray-900">{{ __('Total') }}</span>
                            <span class="font-bold text-lg text-blue-600">AED {{ number_format(($item->price ?? 0) * 1.05, 2) }}</span>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414L10 3.586l4.707 4.707a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Secure payment') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414L10 3.586l4.707 4.707a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('SSL encrypted') }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414L10 3.586l4.707 4.707a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Money-back guarantee') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cardDetails = document.getElementById('card-details');
            cardDetails.style.display = this.value === 'card' ? 'block' : 'none';
        });
    });
</script>
@endsection
