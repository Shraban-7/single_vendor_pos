<div id="openRegisterModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-sm rounded-xl shadow-lg p-5">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Cash Register</h2>

        <form action="{{ route('admin.cashRegister.open') }}" method="POST" class="text-xs space-y-3">
            @csrf
            <label class="block font-semibold text-slate-600 mb-1">Opening Cash Amount</label>
            <div class="flex">
                <span class="px-3 flex items-center bg-slate-100 border border-r-0 border-slate-200 rounded-l-lg text-xs font-semibold text-slate-600">{{ currency('symbol') }}</span>
                <input type="number" name="opening_amount" step="0.01" min="0" required
                    class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-r-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50"
                    placeholder="Enter opening cash">
            </div>
            <button type="submit" class="w-full h-9 inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                <i data-lucide="save" class="w-3.5 h-3.5"></i> Save
            </button>
        </form>
    </div>
</div>

@if($cashRegister)
    <div id="closeRegisterModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg">
            <div class="flex justify-between items-start border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Close Cash Register</h2>
                    <div class="text-[10px] text-slate-500 mt-1 flex gap-2">
                        <span class="px-1.5 py-0.5 bg-slate-100 rounded text-[10px] font-medium">#{{ $cashRegister->id }}</span>
                        <span>Opened: {{ $cashRegister->created_at->format('h:i A') }}</span>
                        <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-medium">Active</span>
                    </div>
                </div>
                <button type="button" id="closeCloseBtn" class="text-slate-400 hover:text-rose-600 transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.cashRegister.close', $cashRegister->id) }}">
                @csrf
                <input type="hidden" name="register_id" value="{{ $cashRegister->id }}">

                <div class="p-5 space-y-4 text-xs">
                    <div class="bg-slate-50 rounded-xl p-3 text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Opening Cash</span>
                            <strong class="text-slate-800">{{ money($cashRegisterData['opening_amount']) }}</strong>
                        </div>
                        <div class="flex justify-between text-emerald-600">
                            <span>Sales</span>
                            <strong>+ {{ money($cashRegisterData['sales_amount']) }}</strong>
                        </div>
                        <div class="flex justify-between text-rose-500">
                            <span>Expenses</span>
                            <strong>- {{ money($cashRegisterData['expense']) }}</strong>
                        </div>
                        <div class="flex justify-between text-rose-500">
                            <span>Sales Returns</span>
                            <strong>- {{ money($cashRegisterData['sales_returns']) }}</strong>
                        </div>
                        <hr class="border-slate-200">
                        @php
                            $expected = $cashRegisterData['opening_amount']
                                + $cashRegisterData['sales_amount']
                                - $cashRegisterData['expense']
                                - $cashRegisterData['sales_returns'];
                        @endphp
                        <div class="flex justify-between font-semibold">
                            <span class="text-slate-600">Expected Cash</span>
                            <span class="text-indigo-600">{{ money($expected) }}</span>
                        </div>
                    </div>

                    @if (!$cashRegister->closed_at)
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Enter Closing Cash Amount</label>
                        <div class="flex">
                            <span class="px-3 flex items-center bg-slate-100 border border-r-0 border-slate-200 rounded-l-lg text-xs font-semibold text-slate-600">{{ currency('symbol') }}</span>
                            <input type="number" name="closing_amount" step="0.01" min="0" required
                                class="w-full h-9 px-2.5 text-xs border border-slate-200 rounded-r-lg focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50"
                                placeholder="Counted cash">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-3">
                    <button type="button" id="cancelCloseBtn"
                        class="h-9 px-3.5 inline-flex items-center text-xs font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="h-9 px-3.5 inline-flex items-center gap-1.5 text-xs font-semibold text-white rounded-lg shadow-sm transition
                        {{ $cashRegister->closed_at ? 'bg-amber-600 hover:bg-amber-700' : 'bg-slate-800 hover:bg-slate-900' }}">
                        <i data-lucide="{{ $cashRegister->closed_at ? 'rotate-cw' : 'save' }}" class="w-3.5 h-3.5"></i>
                        {{ $cashRegister->closed_at ? 'Reopen Register' : 'Close Register' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
