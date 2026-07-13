@extends('admin.layouts.app')
@section('title', 'Financial Reports')
@section('content')

<div>
    {{-- Page Header --}}
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Financial Reports</h1>
            <p class="text-xs text-slate-500">Monitor profitability, expenses, and inventory value</p>
        </div>
        <div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="range" onchange="toggleCustomDates(this.value)"
                    class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="daily" {{ request('range') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('range') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('range') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ request('range') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    <option value="custom" {{ request('range') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
                <div id="customDateRange" class="{{ request('range') == 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-1 px-3 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg shadow-sm hover:bg-slate-900 transition">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Filter</span>
                </button>
            </form>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 shrink-0">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Revenue</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($currentMetrics['totalRevenue']) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $changes['revenue'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $changes['revenue'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($changes['revenue']), 2) }}%
                </span>
            </div>
        </div>

        {{-- Gross Profit --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100 shrink-0">
                    <i data-lucide="hand-coins" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Gross Profit</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($currentMetrics['grossProfit']) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $changes['grossProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $changes['grossProfit'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($changes['grossProfit']), 2) }}%
                </span>
            </div>
        </div>

        {{-- Net Profit --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 shrink-0">
                    <i data-lucide="circle-dollar-sign" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Net Profit</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($currentMetrics['netProfit']) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $changes['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $changes['netProfit'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($changes['netProfit']), 2) }}%
                </span>
            </div>
        </div>

        {{-- Total Expenses --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Expenses</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($currentMetrics['totalExpense']) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $changes['expense'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $changes['expense'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($changes['expense']), 2) }}%
                </span>
            </div>
        </div>

        {{-- Inventory Value --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 shrink-0">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Inventory Value</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ money($inventoryValue) }}</p>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-400">Current stock value</div>
        </div>

        {{-- Profit Margin --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-violet-50 text-violet-600 border border-violet-100 shrink-0">
                    <i data-lucide="percent" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Profit Margin</p>
                    <p class="text-base font-extrabold text-slate-900 tracking-tight">{{ number_format($currentMetrics['profitMargin'], 2) }}%</p>
                </div>
            </div>
            <div class="mt-2 text-[10px]">
                <span class="{{ $changes['profitMargin'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                    <i data-lucide="{{ $changes['profitMargin'] >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 inline mr-0.5"></i>
                    {{ number_format(abs($changes['profitMargin']), 2) }}%
                </span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-5">
        <div class="border-b border-slate-200">
            <nav class="flex gap-1 px-4 pt-2" aria-label="Tabs">
                <button onclick="switchTab('pnl')" id="tab-pnl"
                    class="tab-btn px-4 py-3 text-xs font-semibold text-slate-900 border-b-2 border-slate-900 rounded-t-lg hover:bg-slate-50 transition flex items-center gap-2">
                    <i data-lucide="chart-line" class="w-3.5 h-3.5"></i>
                    Profit & Loss
                </button>
                <button onclick="switchTab('expenses')" id="tab-expenses"
                    class="tab-btn px-4 py-3 text-xs font-semibold text-slate-400 border-b-2 border-transparent rounded-t-lg hover:text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                    Expenses
                </button>
                <button onclick="switchTab('inventory')" id="tab-inventory"
                    class="tab-btn px-4 py-3 text-xs font-semibold text-slate-400 border-b-2 border-transparent rounded-t-lg hover:text-slate-600 hover:bg-slate-50 transition flex items-center gap-2">
                    <i data-lucide="warehouse" class="w-3.5 h-3.5"></i>
                    Inventory Value
                </button>
            </nav>
        </div>

        <div class="p-5">
            {{-- Profit & Loss Tab --}}
            <div id="panel-pnl" class="tab-panel">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {{-- Chart --}}
                    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-4">
                        @php
                            $filterText = match (request('range', 'daily')) {
                                'daily' => 'Daily Profit Trend',
                                'weekly' => 'Weekly Profit Trend',
                                'monthly' => 'Monthly Profit Trend',
                                'yearly' => 'Yearly Profit Trend',
                                'custom' => 'Custom Profit Trend',
                                default => 'Daily Profit Trend',
                            };
                            $descriptionText = match (request('range', 'daily')) {
                                'daily' => 'Net Profit Over the Last 30 Days',
                                'weekly' => 'Net Profit Over the Last 12 Weeks',
                                'monthly' => 'Net Profit Over the Last 12 Months',
                                'yearly' => 'Net Profit Over the Last 5 Years',
                                'custom' => 'Net Profit Over the Selected Date Range',
                                default => 'Net Profit Over the Last 30 Days',
                            };
                        @endphp
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">{{ $filterText }}</h5>
                        <p class="text-[11px] text-slate-500 mb-3">{{ $descriptionText }}</p>
                        <div class="bg-slate-50 rounded-lg border border-slate-200 p-3">
                            <canvas id="profitChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                        <div class="mt-3 px-4 py-2.5 rounded-lg font-bold text-center text-xs {{ $changes['profitMargin'] >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                            Net Profit Margin: {{ number_format($currentMetrics['profitMargin'], 2) }}%
                        </div>
                    </div>

                    {{-- P&L Summary Table --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">P&L Summary</h5>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="px-3 py-2 text-left font-semibold text-slate-500 rounded-tl-lg">Category</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500">Amount</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500 rounded-tr-lg">Change %</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="px-3 py-2.5 text-slate-600">Total Sales</td>
                                        <td class="px-3 py-2.5 text-right font-semibold text-slate-800">{{ money($currentMetrics['totalRevenue']) }}</td>
                                        <td class="px-3 py-2.5 text-right {{ $changes['revenue'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $changes['revenue'] >= 0 ? '+' : '' }}{{ number_format($changes['revenue'], 2) }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2.5 text-slate-600">Cost of Goods Sold</td>
                                        <td class="px-3 py-2.5 text-right font-semibold text-slate-800">{{ money($currentMetrics['totalProductCost']) }}</td>
                                        <td class="px-3 py-2.5 text-right {{ $changes['grossProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $changes['grossProfit'] >= 0 ? '+' : '' }}{{ number_format($changes['grossProfit'], 2) }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2.5 text-slate-600">Gross Profit</td>
                                        <td class="px-3 py-2.5 text-right font-semibold text-slate-800">{{ money($currentMetrics['grossProfit']) }}</td>
                                        <td class="px-3 py-2.5 text-right {{ $changes['grossProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $changes['grossProfit'] >= 0 ? '+' : '' }}{{ number_format($changes['grossProfit'], 2) }}%
                                        </td>
                                    </tr>
                                    <tr class="bg-emerald-50/50">
                                        <td class="px-3 py-2.5 font-bold text-slate-800 rounded-bl-lg">Net Profit</td>
                                        <td class="px-3 py-2.5 text-right font-bold text-slate-800">{{ money($currentMetrics['netProfit']) }}</td>
                                        <td class="px-3 py-2.5 text-right font-bold {{ $changes['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} rounded-br-lg">
                                            {{ $changes['netProfit'] >= 0 ? '+' : '' }}{{ number_format($changes['netProfit'], 2) }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 p-3 rounded-xl {{ $changes['profitMargin'] >= 0 ? 'bg-emerald-600' : 'bg-rose-600' }} text-white">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-[11px]">Current Profit Margin</span>
                                <span class="font-bold text-sm">{{ number_format($currentMetrics['profitMargin'], 2) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expenses Tab --}}
            <div id="panel-expenses" class="tab-panel hidden">
                {{-- Expense KPI Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Expense</p>
                        <h4 class="text-base font-extrabold text-rose-600">{{ money($totalExpense) }}</h4>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Highest Expense Category</p>
                        <h4 class="text-base font-extrabold text-amber-600">
                            {{ $highestExpense->category->name ?? '' }}
                            <span class="text-xs font-normal text-slate-500">
                                {{ isset($highestExpense->totalAmount) ? '('.money($highestExpense->totalAmount).')' : '' }}
                            </span>
                        </h4>
                    </div>
                    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Expense Growth %</p>
                        <h4 class="text-base font-extrabold {{ $expenseGrowth >= 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            <i data-lucide="{{ $expenseGrowth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3.5 h-3.5 inline mr-0.5"></i>
                            {{ number_format($expenseGrowth, 2) }}%
                        </h4>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Expense Trend Chart --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Expense Trend</h5>
                        <p class="text-[11px] text-slate-500 mb-3">{{ ucfirst(request('range')) }} expense comparison.</p>
                        <div class="bg-slate-50 rounded-lg border border-slate-200 p-3">
                            <canvas id="expenseBarChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                    </div>

                    {{-- Expense Breakdown Table --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Expense Breakdown</h5>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="px-3 py-2 text-left font-semibold text-slate-500 rounded-tl-lg">Category</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500">Amount</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500 rounded-tr-lg">Change</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($expenseCategories ?? [] as $expense)
                                        @php
                                            $lastAmount = \App\Models\Expense::where('category_id', $expense['category_id'])
                                                ->whereBetween('created_at', [$lastStart, $lastEnd])
                                                ->sum('amount');
                                            $change = $lastAmount > 0
                                                ? (($expense['totalAmount'] - $lastAmount) / $lastAmount) * 100
                                                : 100;
                                            $categoryName = $expense['category']->name ?? '';
                                            $progressWidth = ($expense['totalAmount'] / ($totalExpense ?: 1)) * 100;
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-3 py-2.5 font-semibold text-slate-800">{{ $categoryName }}</td>
                                            <td class="px-3 py-2.5 text-right text-slate-600">{{ money($expense['totalAmount']) }}</td>
                                            <td class="px-3 py-2.5 text-right {{ $change >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-3 pb-2">
                                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                                    <div class="bg-{{ $loop->index % 2 == 0 ? 'amber' : 'blue' }}-500 h-1.5 rounded-full transition-all"
                                                        style="width: {{ $progressWidth }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory Value Tab --}}
            <div id="panel-inventory" class="tab-panel hidden">
                {{-- Inventory Header --}}
                <div class="bg-white rounded-xl border-l-4 border-amber-500 p-4 mb-4 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Total Inventory Value: <span class="text-base font-extrabold text-slate-900 ml-1">{{ money($inventoryValue) }}</span>
                        </h4>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100">
                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                            Low Turnover Warning: {{ $lowTurnoverDays }} Days ({{ $lowTurnoverCount }} SKUs)
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Value by Category Chart --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Value by Category</h5>
                        <p class="text-[11px] text-slate-500 mb-3">Horizontal bar chart showing stock worth.</p>
                        <div class="bg-slate-50 rounded-lg border border-slate-200 p-3">
                            <canvas id="inventoryChart" class="w-full" style="max-height: 280px;"></canvas>
                        </div>
                    </div>

                    {{-- Inventory Details Table --}}
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Inventory Details</h5>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="px-3 py-2 text-left font-semibold text-slate-500 rounded-tl-lg">Category</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500">SKU Count</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500">Stock Value</th>
                                        <th class="px-3 py-2 text-right font-semibold text-slate-500 rounded-tr-lg">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($inventoryByCategory as $item)
                                        @php
                                            $categoryName = $item['category']->name ?? '';
                                            $skuCount = $item['skuCount'];
                                            $stockValue = $item['stockValue'];
                                            $percent = $totalStockValue > 0 ? ($stockValue / $totalStockValue) * 100 : 0;
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-3 py-2.5 font-semibold text-slate-800">{{ $categoryName }}</td>
                                            <td class="px-3 py-2.5 text-right text-slate-600">{{ $skuCount }}</td>
                                            <td class="px-3 py-2.5 text-right text-slate-600">{{ money($stockValue) }}</td>
                                            <td class="px-3 py-2.5 text-right text-slate-600">{{ number_format($percent, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    function toggleCustomDates(value) {
        const custom = document.getElementById('customDateRange');
        custom.classList.toggle('hidden', value !== 'custom');
    }

    function switchTab(tabId) {
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
        document.getElementById('panel-' + tabId).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-slate-900', 'border-slate-900');
            btn.classList.add('text-slate-400', 'border-transparent');
        });
        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.remove('text-slate-400', 'border-transparent');
        activeBtn.classList.add('text-slate-900', 'border-slate-900');
    }

    const ctx = document.getElementById('profitChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trendData->pluck('label')) !!},
            datasets: [
                { label: 'Net Profit', data: {!! json_encode($trendData->pluck('netProfit')) !!}, backgroundColor: 'rgba(54, 162, 235, 0.2)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 2, fill: true, tension: 0.3 },
                { label: 'Gross Profit', data: {!! json_encode($trendData->pluck('grossProfit')) !!}, backgroundColor: 'rgba(75, 192, 192, 0.2)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 2, fill: true, tension: 0.3 },
                { label: 'Revenue', data: {!! json_encode($trendData->pluck('totalRevenue')) !!}, backgroundColor: 'rgba(255, 206, 86, 0.2)', borderColor: 'rgba(255, 206, 86, 1)', borderWidth: 2, fill: false, tension: 0.3 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top', labels: { font: { size: 11 } } }, tooltip: { mode: 'index', intersect: false } },
            interaction: { mode: 'nearest', intersect: false },
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(value) { return '৳' + value.toLocaleString(); } } },
                x: { ticks: { maxRotation: 45, minRotation: 0 } }
            }
        }
    });

    const expenseCtx = document.getElementById('expenseBarChart').getContext('2d');
    new Chart(expenseCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($expenseTrend->pluck('label')) !!},
            datasets: [{ label: 'Expenses', data: {!! json_encode($expenseTrend->pluck('amount')) !!}, backgroundColor: '#dc3545' }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(context) { return '৳ ' + context.formattedValue; } } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(value) { return '৳ ' + value; } } },
                x: { ticks: { maxRotation: 45, minRotation: 0 } }
            }
        }
    });

    const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(inventoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($inventoryByCategory->pluck('category.name')) !!},
            datasets: [{ label: "Stock Value", data: {!! json_encode($inventoryByCategory->pluck('stockValue')) !!}, backgroundColor: 'rgba(255, 193, 7, 0.7)' }]
        },
        options: { indexAxis: 'y', responsive: true, scales: { x: { beginAtZero: true } } }
    });
</script>
@endpush

@endsection
