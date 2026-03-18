<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - #{{ $transaction->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans p-6 md:p-12">
    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-[28px] shadow-sm border border-slate-100 relative">
        <!-- Action Buttons (Hidden when printing) -->
        <div class="absolute top-8 right-8 no-print flex gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 text-white text-xs font-extrabold rounded-xl shadow-md hover:bg-indigo-700 transition">
                Print / Save PDF
            </button>
            <button onclick="window.close()" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-extrabold rounded-xl hover:bg-slate-200 transition">
                Close
            </button>
        </div>

        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">INVOICE</h1>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Receipt for Membership</p>
            </div>
            <div class="text-right">
                <p class="text-xl font-black text-indigo-600">Event Management</p>
                <p class="text-sm text-slate-500 mt-1">123 Street, City, State ZIP</p>
                <p class="text-sm text-slate-500">support@example.com</p>
            </div>
        </div>

        <!-- Meta info -->
        <div class="flex justify-between items-start mb-10">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Billed To</p>
                <p class="text-sm font-extrabold text-slate-900 mt-1">{{ $user->name ?? $user->first_name . ' ' . $user->last_name }}</p>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <p class="text-sm text-slate-500">{{ $user->mobile }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Invoice Details</p>
                <table class="mt-1 text-sm text-right ml-auto">
                    <tr>
                        <td class="pr-4 font-bold text-slate-500">Invoice Date:</td>
                        <td class="font-extrabold text-slate-900">{{ $transaction->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="pr-4 font-bold text-slate-500">Transaction ID:</td>
                        <td class="font-extrabold text-slate-900">#{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="pr-4 font-bold text-slate-500">Payment ID:</td>
                        <td class="font-bold text-slate-700 font-mono text-xs">{{ $transaction->razorpay_payment_id ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Line Items -->
        <div class="rounded-2xl border border-slate-100 overflow-hidden mb-8">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="py-4 px-5 text-xs font-black text-slate-500 uppercase tracking-widest border-b border-slate-100">Description</th>
                        <th class="py-4 px-5 text-xs font-black text-slate-500 uppercase tracking-widest border-b border-slate-100 text-right w-32">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr>
                        <td class="py-5 px-5 border-b border-slate-100 hidden md:table-cell">
                            <p class="font-extrabold text-slate-900">Membership {{ $plan->subscription_type === 'New' ? 'Registration' : 'Renewal' }}</p>
                            <p class="text-slate-500 text-xs mt-1">Plan: {{ $plan->subscription_type }} | Cycle: {{ ucfirst(str_replace('_', ' ', $plan->payment_type)) }}</p>
                        </td>
                        <td class="py-5 px-5 font-extrabold text-slate-900 text-right w-32 border-b border-slate-100">
                            ₹ {{ number_format($transaction->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-12">
            <div class="w-full max-w-sm rounded-[20px] bg-slate-50 p-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-bold text-slate-500">Subtotal</span>
                    <span class="text-sm font-extrabold text-slate-900">₹ {{ number_format($transaction->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm font-bold text-slate-500">Tax/Fees</span>
                    <span class="text-sm font-extrabold text-slate-900">₹ 0.00</span>
                </div>
                <div class="h-px bg-slate-200 mb-4"></div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-black text-slate-900 uppercase tracking-widest">Total Paid</span>
                    <span class="text-xl font-extrabold text-emerald-600">₹ {{ number_format($transaction->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-100 pt-8 text-center text-sm text-slate-500 font-semibold mb-8">
            Thank you for your membership with Event Management! If you have any questions, please contact our support.
        </div>
    </div>
    
    <script>
        // Start printing window after rendering
        window.addEventListener('load', function() {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
