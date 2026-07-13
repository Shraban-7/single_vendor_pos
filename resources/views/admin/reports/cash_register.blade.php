@extends('admin.layouts.app')

@section('title', 'Cash Registers Reports')
@section('content')

<div class="space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Cash Register Reports</h1>
            <p class="text-xs text-slate-500">Monitor cashier opening and closing balances</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Cashier</th>
                        <th class="px-4 py-3 text-right">Opening Cash</th>
                        <th class="px-4 py-3 text-right">Closing Cash</th>
                        <th class="px-4 py-3 text-right">Difference</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse ($cashRegisters as $cashRegister)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="px-4 py-2.5 whitespace-nowrap font-medium text-slate-700">
                                {{ $cashRegister->opened_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                                {{ $cashRegister?->employee?->name }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right font-medium text-slate-700">
                                {{ money($cashRegister->opening_amount) }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right font-medium text-slate-700">
                                {{ money($cashRegister->closing_amount) }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right text-sm font-semibold">
                                @if ($cashRegister->difference < 0)
                                    <span class="text-rose-600">-{{ money(abs($cashRegister->difference)) }}</span>
                                @else
                                    <span class="text-emerald-600">+{{ money($cashRegister->difference) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center">
                                @if (!empty($cashRegister->closed_at))
                                    <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Closed
                                    </span>
                                @else
                                    <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-600 border border-slate-200/80">
                                        Not Closed
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-slate-500">
                                <div class="flex flex-col items-center max-w-xs mx-auto">
                                    <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                                        <i data-lucide="banknote" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="font-bold text-slate-900">No cash register reports found</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">There are no recorded cash register sessions yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($cashRegisters, 'hasPages') && $cashRegisters->hasPages())
            <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
                <div class="flex items-center justify-between text-xs text-slate-600">
                    <div>
                        Showing <span class="font-semibold text-slate-800">{{ $cashRegisters->firstItem() }}</span> to <span class="font-semibold text-slate-800">{{ $cashRegisters->lastItem() }}</span> of <span class="font-semibold text-slate-800">{{ $cashRegisters->total() }}</span> entries
                    </div>
                    <div class="font-medium">{{ $cashRegisters->links() }}</div>
                </div>
            </div>
        @else
            <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
                {{ $cashRegisters->links() }}
            </div>
        @endif
    </div>

</div>

@endsection
