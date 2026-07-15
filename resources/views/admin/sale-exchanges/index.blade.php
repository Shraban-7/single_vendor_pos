@extends('admin.layouts.app')

@section('title', 'Sale Exchanges')

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col gap-2 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Sale Exchanges</h1>
            <p class="text-xs text-slate-500">Track product exchanges processed against sales returns.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-medium bg-white border border-slate-200 text-slate-600 rounded-lg shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Print</span>
            </button>

            <button onclick="exportExchanges()"
                class="inline-flex items-center justify-center gap-1.5 px-3 h-9 text-xs font-medium text-white bg-emerald-600 rounded-lg shadow-sm hover:bg-emerald-700 transition">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Export</span>
            </button>
        </div>
    </div>

    {{-- Compact Filter Framework --}}
    <div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
        <form method="GET" action="{{ route('admin.saleExchanges.index') }}" class="space-y-2.5">
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">

                {{-- Global Search Bar --}}
                <div class="sm:col-span-6">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by customer name, phone, or return/invoice #..."
                            class="w-full pl-8 pr-3 text-xs border h-9 border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white transition">
                        <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                    </div>
                </div>

                {{-- From Date Picker --}}
                <div class="sm:col-span-2">
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                        class="w-full px-2 text-xs border h-9 border-slate-200 rounded-lg focus:outline-none bg-slate-50/50" title="From Date">
                </div>

                {{-- To Date Picker --}}
                <div class="sm:col-span-2">
                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                        class="w-full px-2 text-xs border h-9 border-slate-200 rounded-lg focus:outline-none bg-slate-50/50" title="To Date">
                </div>

                {{-- Filter Tool Pipelines --}}
                <div class="sm:col-span-2 flex gap-1">
                    <button type="submit" class="flex flex-1 items-center justify-center h-9 text-xs text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm" title="Apply Filters">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    </button>
                    <a href="{{ route('admin.saleExchanges.index') }}" class="flex items-center justify-center w-9 h-9 text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition shadow-sm" title="Reset Filters">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Exchange Ledger Data Table --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Return ID</th>
                        <th class="px-4 py-3">Origin Invoice</th>
                        <th class="px-4 py-3">Customer Profile</th>
                        <th class="px-4 py-3">Exchanged Items</th>
                        <th class="px-4 py-3">Exchange Value</th>
                        <th class="px-4 py-3">Returned For</th>
                        <th class="px-4 py-3">Processed By</th>
                        <th class="px-4 py-3">Date Logged</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($exchanges as $exchange)
                        <tr class="hover:bg-slate-50/60 transition-colors">

                            {{-- Return Number Identifier --}}
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium">
                                <span class="font-bold text-slate-900 text-sm">#{{ $exchange->return_number }}</span>
                            </td>

                            {{-- Originating Invoice Reference --}}
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if ($exchange->sale != null)
                                    <a href="{{ route('admin.ecommerce-sales.show', $exchange->sale->invoice_number) }}"
                                        class="text-indigo-600 hover:text-indigo-800 hover:underline font-semibold">
                                        {{ $exchange->order_number }}
                                    </a>
                                @else
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-rose-50 text-rose-700 border border-rose-100/70">
                                        {{ $exchange->order_number }} (Deleted)
                                    </span>
                                @endif
                            </td>

                            {{-- Customer Profile --}}
                            <td class="px-4 py-2.5 max-w-[150px] truncate">
                                <span class="font-medium text-slate-800 block">{{ $exchange->customer->name ?? ($exchange->sale?->customer?->name ?? 'Walk-in Customer') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 tracking-tight">{{ $exchange->customer->phone ?? ($exchange->sale?->customer?->phone ?? '—') }}</span>
                            </td>

                            {{-- Exchanged Items --}}
                            <td class="px-4 py-2.5 text-slate-600">
                                <div class="flex flex-col gap-1">
                                    @foreach($exchange->exchangeItems as $exItem)
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1 text-[10px] font-bold rounded bg-violet-50 text-violet-700 border border-violet-100">
                                                {{ $exItem->quantity }}
                                            </span>
                                            <span class="font-medium text-slate-800 truncate max-w-[180px]" title="{{ $exItem->name }}">
                                                {{ $exItem->name }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap">
                                                {{ money($exItem->unit_price) }} ea • {{ money($exItem->subtotal) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Exchange Value --}}
                            <td class="px-4 py-2.5 whitespace-nowrap font-bold text-violet-600 text-sm">
                                {{ money($exchange->exchange_value) }}
                            </td>

                            {{-- Returned For (refund) --}}
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium text-slate-700">
                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-slate-100 text-slate-700 border border-slate-200/60">
                                    {{ ucfirst(str_replace('_', ' ', $exchange->refund_method ?? '')) }}
                                </span>
                            </td>

                            {{-- Processing Operator Context --}}
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600 font-medium">
                                {{ $exchange->employee->name ?? 'System' }}
                            </td>

                            {{-- Document Timestamp fields --}}
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                                <span>{{ $exchange->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $exchange->created_at->format('h:i A') }}</span>
                            </td>

                            {{-- Action Tools Navigation --}}
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('admin.saleExchanges.show', $exchange->id) }}"
                                       class="p-1 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded transition"
                                       title="View Details">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center text-slate-500">
                                <div class="max-w-xs mx-auto flex flex-col items-center">
                                    <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 text-slate-400 mb-3 border border-slate-100">
                                        <i data-lucide="repeat" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-900">No exchange logs discovered</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">No logged exchanges map to your filter combinations right now.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination Component Section --}}
        @if($exchanges->hasPages())
            <div class="px-4 py-3 bg-slate-50/50 border-t border-slate-100">
                {{ $exchanges->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function exportExchanges() {
            const params = new URLSearchParams(window.location.search);
            params.append('export', 'csv');
            window.location.href = `{{ route('admin.saleExchanges.index') }}?${params.toString()}`;
        }
    </script>
@endpush
