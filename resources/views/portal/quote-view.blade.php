<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quote {{ $quote->quote_number }} - {{ $company['name'] }}</title>
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold" style="color: {{ $company['primary_color'] }}">
                            {{ $company['name'] }}
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">Quote {{ $quote->quote_number }}</p>
                    </div>
                    @if ($company['logo'])
                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="h-12">
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Status Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-blue-800">{{ session('info') }}</p>
                </div>
            @endif

            <!-- Quote Status Badge -->
            <div class="mb-6">
                @if ($quote->acceptance)
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        {{ $quote->acceptance->isAcceptance() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        @if ($quote->acceptance->isAcceptance())
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Quote Accepted
                        @else
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            Quote Declined
                        @endif
                        <span class="ml-2 text-xs text-gray-600">
                            on {{ $quote->acceptance->getTimestamp()->format('M d, Y \a\t g:i A') }}
                        </span>
                    </div>
                @elseif($quote->isExpired())
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        This quote has expired
                    </div>
                @endif
            </div>

            <!-- Quote Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Client Info -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Quote For</h3>
                            <div class="text-gray-900">
                                <p class="font-semibold">{{ $quote->client->name }}</p>
                                @if ($quote->client->company)
                                    <p class="text-sm text-gray-600">{{ $quote->client->company }}</p>
                                @endif
                                @if ($quote->client->email)
                                    <p class="text-sm text-gray-600">{{ $quote->client->email }}</p>
                                @endif
                                @if ($quote->client->phone)
                                    <p class="text-sm text-gray-600">{{ $quote->client->phone }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Quote Info -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Quote Details
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Quote Number:</span>
                                    <span class="font-semibold">{{ $quote->quote_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span>{{ $quote->quote_date->format('M d, Y') }}</span>
                                </div>
                                @if ($quote->valid_until)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Valid Until:</span>
                                        <span class="{{ $quote->isExpired() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $quote->valid_until->format('M d, Y') }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Prepared By:</span>
                                    <span>{{ $quote->user->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($quote->title)
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $quote->title }}</h2>
                    @endif

                    @if ($quote->description)
                        <p class="text-gray-600 mb-4">{{ $quote->description }}</p>
                    @endif

                    <!-- Line Items -->
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Unit Price</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($quote->items as $item)
                                        <tr>
                                            <td class="px-4 py-4">
                                                <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                                @if ($item->description)
                                                    <div class="text-sm text-gray-500 mt-1">{{ $item->description }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center text-sm text-gray-900">
                                                {{ $item->quantity }} {{ $item->unit }}
                                            </td>
                                            <td class="px-4 py-4 text-right text-sm text-gray-900">
                                                {{ $quote->currency }}{{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="px-4 py-4 text-right font-medium text-gray-900">
                                                {{ $quote->currency }}{{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="flex justify-end">
                            <div class="w-full max-w-xs space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span
                                        class="font-medium">{{ $quote->currency }}{{ number_format($quote->subtotal, 2) }}</span>
                                </div>
                                @if ($quote->discount_amount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Discount:</span>
                                        <span
                                            class="text-red-600">-{{ $quote->currency }}{{ number_format($quote->discount_amount, 2) }}</span>
                                    </div>
                                @endif
                                @if ($quote->tax_amount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax ({{ $quote->tax_rate }}%):</span>
                                        <span
                                            class="font-medium">{{ $quote->currency }}{{ number_format($quote->tax_amount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2"
                                    style="color: {{ $company['primary_color'] }}">
                                    <span>Total:</span>
                                    <span>{{ $quote->currency }}{{ number_format($quote->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if ($quote->notes)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border-l-4"
                            style="border-color: {{ $company['primary_color'] }}">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes & Terms</h3>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $quote->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Accept/Reject Section -->
            @if (!$quote->acceptance && !$quote->isExpired())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Respond to this Quote</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Accept Form -->
                        <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                            <h4 class="font-semibold text-green-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Accept Quote
                            </h4>
                            <form id="acceptForm" method="POST"
                                action="{{ route('portal.quote.accept', $quote->portal_token) }}">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                        <input type="text" name="client_name"
                                            value="{{ old('client_name', $quote->client->name) }}" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Email
                                            *</label>
                                        <input type="email" name="client_email"
                                            value="{{ old('client_email', $quote->client->email) }}" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Signature *</label>
                                        <div class="border border-gray-300 rounded-md bg-white">
                                            <canvas id="signaturePad" class="w-full"
                                                style="touch-action: none;"></canvas>
                                        </div>
                                        <input type="hidden" name="signature" id="signatureData">
                                        <button type="button" onclick="clearSignature()"
                                            class="mt-1 text-xs text-gray-600 hover:text-gray-900">
                                            Clear Signature
                                        </button>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes
                                            (Optional)</label>
                                        <textarea name="notes" rows="2"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('notes') }}</textarea>
                                    </div>
                                    <button type="submit" onclick="return submitAcceptance()"
                                        class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Accept Quote
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Reject Form -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                Decline Quote
                            </h4>
                            <form method="POST" action="{{ route('portal.quote.reject', $quote->portal_token) }}">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                        <input type="text" name="client_name"
                                            value="{{ old('client_name', $quote->client->name) }}" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Email
                                            *</label>
                                        <input type="email" name="client_email"
                                            value="{{ old('client_email', $quote->client->email) }}" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason
                                            (Optional)</label>
                                        <textarea name="rejection_reason" rows="3"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Please let us know why you're declining...">{{ old('rejection_reason') }}</textarea>
                                    </div>
                                    <button type="submit"
                                        class="w-full px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        Decline Quote
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Already Responded -->
            @if ($quote->acceptance)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Response Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Responded by:</span>
                            <span class="font-medium">{{ $quote->acceptance->client_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span>{{ $quote->acceptance->client_email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span>{{ $quote->acceptance->getTimestamp()->format('M d, Y \a\t g:i A') }}</span>
                        </div>
                        @if ($quote->acceptance->isAcceptance() && $quote->acceptance->signature_data)
                            <div class="mt-4">
                                <span class="text-gray-600 block mb-2">Signature:</span>
                                <div class="border border-gray-200 rounded p-2 bg-white inline-block">
                                    <img src="{{ $quote->acceptance->signature_data }}" alt="Signature"
                                        class="max-h-24">
                                </div>
                            </div>
                        @endif
                        @if ($quote->acceptance->isRejection() && $quote->acceptance->rejection_reason)
                            <div class="mt-4">
                                <span class="text-gray-600 block mb-2">Reason:</span>
                                <p class="text-gray-900 bg-gray-50 p-3 rounded">
                                    {{ $quote->acceptance->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-600">
                    <p class="font-semibold">{{ $company['name'] }}</p>
                    @if ($company['email'])
                        <p>{{ $company['email'] }}</p>
                    @endif
                    @if ($company['phone'])
                        <p>{{ $company['phone'] }}</p>
                    @endif
                    @if ($company['website'])
                        <p><a href="{{ $company['website'] }}"
                                class="text-blue-600 hover:underline">{{ $company['website'] }}</a></p>
                    @endif
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Initialize signature pad
        const canvas = document.getElementById('signaturePad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Resize canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = 150 * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        function clearSignature() {
            signaturePad.clear();
        }

        function submitAcceptance() {
            if (signaturePad.isEmpty()) {
                alert('Please provide a signature');
                return false;
            }

            document.getElementById('signatureData').value = signaturePad.toDataURL();
            return true;
        }
    </script>
</body>

</html>
