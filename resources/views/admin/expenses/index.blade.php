@extends('admin.layouts.app')
@section('title', 'Expenses')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Expenses</h1>
        <p class="text-xs text-slate-500">Track and manage all business expenses</p>
    </div>
    <div>
        <button onclick="openAddModal()"
            class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 transition">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
            <span>Add Expense</span>
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 shrink-0 border border-indigo-100">
            <i data-lucide="receipt" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Expenses</p>
            <p class="text-lg font-extrabold text-slate-900 tracking-tight mt-0.5">{{ money($totalExpense) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-violet-50 text-violet-600 shrink-0 border border-violet-100">
            <i data-lucide="calendar" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">This Month</p>
            <p class="text-lg font-extrabold text-slate-900 tracking-tight mt-0.5">{{ money($monthlyExpense) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-amber-50 text-amber-600 shrink-0 border border-amber-100">
            <i data-lucide="tags" class="w-4 h-4"></i>
        </div>
        <div>
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Records</p>
            <p class="text-lg font-extrabold text-slate-900 tracking-tight mt-0.5">{{ $expenses->total() }}</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" action="{{ route('admin.expenses.index') }}" class="space-y-2.5">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">

            <div class="sm:col-span-3">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Category</label>
                <select name="category_id"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
            </div>

            <div class="flex gap-1 sm:col-span-3 items-end">
                <button type="submit" class="flex items-center justify-center flex-1 text-xs text-white transition rounded-lg shadow-sm h-9 bg-slate-800 hover:bg-slate-900">
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="flex items-center justify-center transition bg-white border rounded-lg shadow-sm w-9 h-9 text-slate-500 border-slate-200 hover:bg-slate-50 hover:text-slate-800" title="Clear Filters">
                    <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                </a>
            </div>

        </div>
    </form>
</div>

{{-- Table --}}
<div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3">Expense Date</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100" id="expenseTableBody">
                @forelse($expenses as $expense)
                <tr class="transition-colors hover:bg-slate-50/60" id="expense-row-{{ $expense->id }}">
                    <td class="px-4 py-2.5 text-slate-400">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2.5">
                        @if($expense->category)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <i data-lucide="tag" class="w-2.5 h-2.5"></i>
                                {{ $expense->category->name }}
                            </span>
                        @else
                            <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-500 border border-slate-200/80">Uncategorized</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 max-w-[220px] truncate text-slate-500">
                        {{ $expense->description ?? '—' }}
                    </td>
                    <td class="px-4 py-2.5 whitespace-nowrap">
                        <span class="text-sm font-bold text-slate-900">{{ money($expense->amount) }}</span>
                    </td>
                    <td class="px-4 py-2.5 whitespace-nowrap text-slate-700 font-medium">
                        {{ $expense->expense_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-2.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-0.5">
                            <button onclick="openEditModal({{ $expense->id }}, {{ $expense->category_id ?? 'null' }}, '{{ addslashes($expense->category->name ?? '') }}', '{{ $expense->amount }}', '{{ $expense->expense_date }}', '{{ addslashes($expense->description ?? '') }}')"
                                class="p-1 transition rounded text-slate-400 hover:text-emerald-600 hover:bg-slate-100" title="Edit">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="openDeleteModal({{ $expense->id }})"
                                class="p-1 transition rounded text-slate-400 hover:text-rose-600 hover:bg-slate-100" title="Delete">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center text-slate-500">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                                <i data-lucide="receipt" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">No expenses found</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Try adjusting your filters or add a new expense.</p>
                            <button onclick="openAddModal()" class="inline-flex items-center justify-center gap-1.5 px-3 h-8 mt-3 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Add Expense</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
            <div class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between text-slate-600">
                <div>
                    Showing <span class="font-semibold text-slate-800">{{ $expenses->firstItem() }}</span> to <span class="font-semibold text-slate-800">{{ $expenses->lastItem() }}</span> of <span class="font-semibold text-slate-800">{{ $expenses->total() }}</span> entries
                </div>
                <div class="font-medium">{{ $expenses->links() }}</div>
            </div>
        </div>
    @endif
</div>

{{-- ======== ADD MODAL ======== --}}
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-slate-200">

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Add Expense</h2>
                        <p class="text-[11px] text-slate-400">Fill in the details below</p>
                    </div>
                </div>
                <button onclick="closeAddModal()" class="flex items-center justify-center w-8 h-8 transition rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <div class="px-5 py-4 space-y-4 text-xs">
                {{-- Category --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select id="addCategoryId"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                        <option value="">— Select category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Amount (৳) <span class="text-rose-500">*</span></label>
                    <input type="number" id="addAmount" step="0.01" min="0" placeholder="0.00"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                    <p class="text-[11px] text-rose-600 mt-1 hidden" id="addAmountErr">Amount is required.</p>
                </div>

                {{-- Expense Date --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Expense Date <span class="text-rose-500">*</span></label>
                    <input type="date" id="addExpenseDate" value="{{ date('Y-m-d') }}"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                    <p class="text-[11px] text-rose-600 mt-1 hidden" id="addDateErr">Date is required.</p>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Description</label>
                    <textarea id="addDescription" rows="3" placeholder="Optional notes about this expense..."
                        class="w-full px-2.5 py-2 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition resize-none"></textarea>
                </div>

                <div id="addErrorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg px-4 py-3"></div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-slate-100 bg-slate-50/40">
                <button onclick="closeAddModal()" class="px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button onclick="submitAdd()" id="addSubmitBtn" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Save Expense</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======== EDIT MODAL ======== --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-slate-200">

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Edit Expense</h2>
                        <p class="text-[11px] text-slate-400">Update the expense details</p>
                    </div>
                </div>
                <button onclick="closeEditModal()" class="flex items-center justify-center w-8 h-8 transition rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <div class="px-5 py-4 space-y-4 text-xs">
                <input type="hidden" id="editExpenseId">

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select id="editCategoryId"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                        <option value="">— Select category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Amount (৳) <span class="text-rose-500">*</span></label>
                    <input type="number" id="editAmount" step="0.01" min="0" placeholder="0.00"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                    <p class="text-[11px] text-rose-600 mt-1 hidden" id="editAmountErr">Amount is required.</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Expense Date <span class="text-rose-500">*</span></label>
                    <input type="date" id="editExpenseDate"
                        class="w-full px-2.5 h-9 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition">
                    <p class="text-[11px] text-rose-600 mt-1 hidden" id="editDateErr">Date is required.</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Description</label>
                    <textarea id="editDescription" rows="3" placeholder="Optional notes..."
                        class="w-full px-2.5 py-2 text-xs border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 rounded-lg transition resize-none"></textarea>
                </div>

                <div id="editErrorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg px-4 py-3"></div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-slate-100 bg-slate-50/40">
                <button onclick="closeEditModal()" class="px-3 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button onclick="submitEdit()" id="editSubmitBtn" class="inline-flex items-center justify-center gap-1.5 px-3.5 h-9 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition shadow-sm">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Update Expense</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======== DELETE MODAL ======== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-5 text-center border border-slate-200">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-900 mb-1">Delete Expense?</h3>
            <p class="text-xs text-slate-500 mb-5">This action cannot be undone. The record will be permanently removed.</p>
            <input type="hidden" id="deleteExpenseId">
            <div class="flex gap-2">
                <button onclick="closeDeleteModal()" class="flex-1 h-9 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button onclick="submitDelete()" id="deleteSubmitBtn" class="flex-1 inline-flex items-center justify-center gap-1.5 h-9 text-xs font-semibold text-white bg-rose-600 rounded-lg hover:bg-rose-700 transition shadow-sm">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Yes, Delete</span>
                </button>
            </div>
            <div id="deleteErrorBox" class="hidden mt-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg px-4 py-3"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    const storeUrl = '{{ route("admin.expenses.store") }}';
    const baseUrl = '{{ url("admin/expenses") }}';

    function showError(msg) {
        if (window.showToast) window.showToast('error', msg);
    }
    function showSuccess(msg) {
        if (window.showToast) window.showToast('success', msg);
    }

    // -------- ADD MODAL --------
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('addCategoryId').value = '';
        document.getElementById('addAmount').value = '';
        document.getElementById('addExpenseDate').value = '{{ date("Y-m-d") }}';
        document.getElementById('addDescription').value = '';
        document.getElementById('addAmountErr').classList.add('hidden');
        document.getElementById('addDateErr').classList.add('hidden');
        document.getElementById('addErrorBox').classList.add('hidden');
    }

    async function submitAdd() {
        let valid = true;
        const amount = document.getElementById('addAmount').value;
        const expenseDate = document.getElementById('addExpenseDate').value;

        if (!amount) { document.getElementById('addAmountErr').classList.remove('hidden'); valid = false; }
        else { document.getElementById('addAmountErr').classList.add('hidden'); }
        if (!expenseDate) { document.getElementById('addDateErr').classList.remove('hidden'); valid = false; }
        else { document.getElementById('addDateErr').classList.add('hidden'); }
        if (!valid) return;

        const submitBtn = document.getElementById('addSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i><span>Saving...</span>';
        if (window.lucide) lucide.createIcons();
        document.getElementById('addErrorBox').classList.add('hidden');

        try {
            const formData = new FormData();
            formData.append('_token', CSRF);
            formData.append('category_id', document.getElementById('addCategoryId').value);
            formData.append('amount', amount);
            formData.append('expense_date', expenseDate);
            formData.append('description', document.getElementById('addDescription').value);

            const response = await fetch(storeUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                const errors = data.errors;
                const msg = errors ? Object.values(errors).flat().join(' ') : (data.message || 'Something went wrong.');
                throw new Error(msg);
            }

            showSuccess('Expense added successfully!');
            closeAddModal();
            setTimeout(() => location.reload(), 800);
        } catch (error) {
            showError(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="save" class="w-3.5 h-3.5"></i><span>Save Expense</span>';
            if (window.lucide) lucide.createIcons();
        }
    }

    // -------- EDIT MODAL --------
    function openEditModal(id, categoryId, categoryName, amount, expenseDate, description) {
        document.getElementById('editExpenseId').value = id;
        document.getElementById('editAmount').value = amount;
        document.getElementById('editExpenseDate').value = expenseDate;
        document.getElementById('editDescription').value = description;
        document.getElementById('editCategoryId').value = categoryId || '';
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('editAmountErr').classList.add('hidden');
        document.getElementById('editDateErr').classList.add('hidden');
        document.getElementById('editErrorBox').classList.add('hidden');
    }

    async function submitEdit() {
        let valid = true;
        const amount = document.getElementById('editAmount').value;
        const expenseDate = document.getElementById('editExpenseDate').value;

        if (!amount) { document.getElementById('editAmountErr').classList.remove('hidden'); valid = false; }
        else { document.getElementById('editAmountErr').classList.add('hidden'); }
        if (!expenseDate) { document.getElementById('editDateErr').classList.remove('hidden'); valid = false; }
        else { document.getElementById('editDateErr').classList.add('hidden'); }
        if (!valid) return;

        const id = document.getElementById('editExpenseId').value;
        const submitBtn = document.getElementById('editSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i><span>Updating...</span>';
        if (window.lucide) lucide.createIcons();
        document.getElementById('editErrorBox').classList.add('hidden');

        try {
            const formData = new FormData();
            formData.append('_token', CSRF);
            formData.append('_method', 'PUT');
            formData.append('category_id', document.getElementById('editCategoryId').value);
            formData.append('amount', amount);
            formData.append('expense_date', expenseDate);
            formData.append('description', document.getElementById('editDescription').value);

            const response = await fetch(`${baseUrl}/${id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                const errors = data.errors;
                const msg = errors ? Object.values(errors).flat().join(' ') : (data.message || 'Something went wrong.');
                throw new Error(msg);
            }

            showSuccess('Expense updated successfully!');
            closeEditModal();
            setTimeout(() => location.reload(), 800);
        } catch (error) {
            showError(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="save" class="w-3.5 h-3.5"></i><span>Update Expense</span>';
            if (window.lucide) lucide.createIcons();
        }
    }

    // -------- DELETE MODAL --------
    function openDeleteModal(id) {
        document.getElementById('deleteExpenseId').value = id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('deleteErrorBox').classList.add('hidden');
    }

    async function submitDelete() {
        const id = document.getElementById('deleteExpenseId').value;
        const submitBtn = document.getElementById('deleteSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i><span>Deleting...</span>';
        if (window.lucide) lucide.createIcons();
        document.getElementById('deleteErrorBox').classList.add('hidden');

        try {
            const formData = new FormData();
            formData.append('_token', CSRF);
            formData.append('_method', 'DELETE');

            const response = await fetch(`${baseUrl}/${id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.message || 'Something went wrong.');

            closeDeleteModal();
            location.reload();
        } catch (error) {
            showError(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="trash-2" class="w-3.5 h-3.5"></i><span>Yes, Delete</span>';
            if (window.lucide) lucide.createIcons();
        }
    }

    // -------- Escape key --------
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
            closeDeleteModal();
        }
    });
</script>
@endpush

@endsection
