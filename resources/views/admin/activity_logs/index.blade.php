@extends('admin.layouts.app')
@section('title', 'Activity Logs')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Activity Logs</h1>
        <p class="text-xs text-slate-500">Track all user actions across the system</p>
    </div>
    <div>
        <span class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg">
            <i data-lucide="list" class="w-3.5 h-3.5"></i>
            <span>{{ $activities->total() }} Records</span>
        </span>
    </div>
</div>

{{-- Compact Filter Framework --}}
<div class="p-3.5 mb-4 bg-white border border-slate-200 rounded-xl shadow-sm">
    <form method="GET" class="space-y-2.5">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">

            <div class="sm:col-span-3">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search user, action, model, IP..."
                        class="w-full pl-8 pr-3 text-xs transition border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50 focus:bg-white">
                    <i data-lucide="search" class="absolute w-3.5 h-3.5 -translate-y-1/2 left-2.5 top-1/2 text-slate-400"></i>
                </div>
            </div>

            <div class="sm:col-span-2">
                <select name="action" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="model_type" class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
                    <option value="">All Models</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                            {{ class_basename($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
            </div>

            <div class="sm:col-span-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-2 text-xs border rounded-lg h-9 border-slate-200 focus:outline-none focus:ring-1 focus:ring-slate-400 bg-slate-50/50">
            </div>

            <div class="flex gap-1 sm:col-span-1">
                <button type="submit" class="flex items-center justify-center flex-1 text-xs text-white transition rounded-lg shadow-sm h-9 bg-slate-800 hover:bg-slate-900" title="Filter">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                </button>
                <a href="{{ route('admin.activityLogs.index') }}" class="flex items-center justify-center transition bg-white border rounded-lg shadow-sm w-9 h-9 text-slate-500 border-slate-200 hover:bg-slate-50 hover:text-slate-800" title="Clear Filters">
                    <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i>
                </a>
            </div>

        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-slate-50/70 border-slate-200 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Model</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">IP Address</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3 text-center">Details</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($activities as $log)
                <tr class="transition-colors hover:bg-slate-50/60">
                    <td class="px-4 py-2.5 text-slate-400 font-mono text-[11px]">{{ $log->id }}</td>

                    <td class="px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 font-bold text-[11px] flex-shrink-0">
                                {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $log->user->name ?? '<em class="text-slate-400">Deleted</em>' }}</span>
                        </div>
                    </td>

                    <td class="px-4 py-2.5">
                        @php
                        $actionColors = [
                            'created' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'updated' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'deleted' => 'bg-rose-50 text-rose-700 border-rose-100/70',
                            'restored' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                            'login' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                            'logout' => 'bg-slate-100 text-slate-600 border-slate-200/80',
                        ];
                        $color = $actionColors[$log->action] ?? 'bg-purple-50 text-purple-700 border-purple-100';
                        @endphp
                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded border {{ $color }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>

                    <td class="px-4 py-2.5">
                        @if($log->model_type)
                            <span class="font-medium text-slate-700">{{ class_basename($log->model_type) }}</span>
                            @if($log->model_id)
                                <span class="text-slate-400 text-[10px] ml-0.5">#{{ $log->model_id }}</span>
                            @endif
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    <td class="px-4 py-2.5 max-w-[200px] truncate text-slate-500" title="{{ $log->description }}">
                        {{ $log->description ?? '—' }}
                    </td>

                    <td class="px-4 py-2.5 font-mono text-[11px] text-slate-500">
                        {{ $log->ip_address ?? '—' }}
                    </td>

                    <td class="px-4 py-2.5 whitespace-nowrap">
                        <span class="block text-slate-700 text-xs font-medium">{{ $log->created_at->format('M d, Y') }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ $log->created_at->format('h:i A') }}</span>
                    </td>

                    <td class="px-4 py-2.5 text-center">
                        <button onclick="openOffcanvas(this)"
                            data-log="{{ json_encode([
                                'id'         => $log->id,
                                'user'       => $log->user->name ?? null,
                                'action'     => $log->action,
                                'model_type' => $log->model_type ? class_basename($log->model_type) : null,
                                'model_id'   => $log->model_id,
                                'description'=> $log->description,
                                'ip_address' => $log->ip_address,
                                'address'    => $log->address,
                                'user_agent' => $log->user_agent,
                                'old_values' => $log->old_values,
                                'new_values' => $log->new_values,
                                'created_at' => $log->created_at->format('M d, Y  h:i A'),
                            ]) }}"
                            class="p-1.5 transition rounded text-slate-400 hover:text-indigo-600 hover:bg-slate-100" title="View Details">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center text-slate-500">
                        <div class="flex flex-col items-center max-w-xs mx-auto">
                            <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                                <i data-lucide="history" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">No activity logs found</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Try adjusting your search terms or date range.</p>
                            <a href="{{ route('admin.activityLogs.index') }}" class="inline-block mt-3 text-xs font-semibold text-indigo-600 hover:underline">Reset Filters</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
    <div class="px-4 py-3 border-t bg-slate-50/50 border-slate-100">
        <div class="flex flex-col gap-2 text-xs sm:flex-row sm:items-center sm:justify-between text-slate-600">
            <div>
                Showing <span class="font-semibold text-slate-800">{{ $activities->firstItem() }}</span> to <span class="font-semibold text-slate-800">{{ $activities->lastItem() }}</span> of <span class="font-semibold text-slate-800">{{ $activities->total() }}</span> entries
            </div>
            <div class="font-medium">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ── Offcanvas Backdrop ── --}}
<div id="ocBackdrop"
    onclick="closeOffcanvas()"
    class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm hidden transition-opacity duration-300">
</div>

{{-- ── Offcanvas Panel ── --}}
<div id="offcanvas"
    class="fixed top-0 right-0 z-50 h-full w-full max-w-lg bg-white shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-in-out">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600">
                <i data-lucide="history" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Log Details</h3>
                <p id="oc-id" class="text-[11px] text-slate-400">#—</p>
            </div>
        </div>
        <button onclick="closeOffcanvas()" class="flex items-center justify-center w-8 h-8 transition rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>

    {{-- Scrollable Body --}}
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

        {{-- Summary Grid --}}
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
                    <i data-lucide="user" class="w-3 h-3 inline mr-1"></i>User
                </p>
                <p id="oc-user" class="text-xs font-semibold text-slate-800">—</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
                    <i data-lucide="activity" class="w-3 h-3 inline mr-1"></i>Action
                </p>
                <p id="oc-action" class="text-xs font-semibold">—</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
                    <i data-lucide="box" class="w-3 h-3 inline mr-1"></i>Model
                </p>
                <p id="oc-model" class="text-xs font-semibold text-slate-800">—</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
                    <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>Date
                </p>
                <p id="oc-date" class="text-xs font-semibold text-slate-800">—</p>
            </div>
        </div>

        {{-- Description --}}
        <div id="oc-desc-wrap" class="hidden">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                <i data-lucide="align-left" class="w-3 h-3 inline mr-1"></i>Description
            </p>
            <p id="oc-desc" class="text-xs text-slate-700 bg-slate-50 rounded-xl px-4 py-3 leading-relaxed border border-slate-100"></p>
        </div>

        {{-- Network --}}
        <div>
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                <i data-lucide="globe" class="w-3 h-3 inline mr-1"></i>Network
            </p>
            <div class="bg-slate-50 rounded-xl border border-slate-100 divide-y divide-slate-100">
                <div class="flex items-start gap-3 px-4 py-3">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400 mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] text-slate-400 font-medium uppercase">IP Address</p>
                        <p id="oc-ip" class="text-xs text-slate-700 font-mono mt-0.5">—</p>
                    </div>
                </div>
                <div id="oc-address-row" class="flex items-start gap-3 px-4 py-3 hidden">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400 mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] text-slate-400 font-medium uppercase">Address</p>
                        <p id="oc-address" class="text-xs text-slate-700 mt-0.5 break-words"></p>
                    </div>
                </div>
                <div id="oc-ua-row" class="flex items-start gap-3 px-4 py-3 hidden">
                    <i data-lucide="monitor" class="w-3.5 h-3.5 text-slate-400 mt-0.5 flex-shrink-0"></i>
                    <div class="min-w-0">
                        <p class="text-[10px] text-slate-400 font-medium uppercase">User Agent</p>
                        <p id="oc-ua" class="text-[11px] text-slate-500 mt-0.5 break-all leading-relaxed"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Old Values --}}
        <div id="oc-old-wrap" class="hidden">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                <i data-lucide="minus-circle" class="w-3 h-3 inline mr-1 text-rose-400"></i>Old Values
            </p>
            <div class="rounded-xl border border-rose-100 overflow-hidden">
                <table class="min-w-full text-xs">
                    <tbody id="oc-old-body" class="divide-y divide-rose-50"></tbody>
                </table>
            </div>
        </div>

        {{-- New Values --}}
        <div id="oc-new-wrap" class="hidden">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                <i data-lucide="plus-circle" class="w-3 h-3 inline mr-1 text-emerald-400"></i>New Values
            </p>
            <div class="rounded-xl border border-emerald-100 overflow-hidden">
                <table class="min-w-full text-xs">
                    <tbody id="oc-new-body" class="divide-y divide-emerald-50"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Auto-submit filters on select change
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('select[name="action"], select[name="model_type"]').forEach(el => {
            el.addEventListener('change', function() { this.form.submit(); });
        });
    });

    const actionColors = {
        created: 'bg-emerald-50 text-emerald-700 border-emerald-100',
        updated: 'bg-blue-50 text-blue-700 border-blue-100',
        deleted: 'bg-rose-50 text-rose-700 border-rose-100/70',
        restored: 'bg-amber-50 text-amber-700 border-amber-200/60',
        login: 'bg-indigo-50 text-indigo-700 border-indigo-100',
        logout: 'bg-slate-100 text-slate-600 border-slate-200/80',
    };

    function openOffcanvas(btn) {
        const log = JSON.parse(btn.dataset.log);

        document.getElementById('oc-id').textContent = '#' + log.id;
        document.getElementById('oc-user').textContent = log.user ?? 'Deleted User';
        document.getElementById('oc-ip').textContent = log.ip_address ?? '—';
        document.getElementById('oc-date').textContent = log.created_at ?? '—';

        const color = actionColors[log.action] ?? 'bg-purple-50 text-purple-700 border-purple-100';
        document.getElementById('oc-action').innerHTML =
            `<span class="inline-flex px-1.5 py-0.5 text-[10px] font-medium rounded border ${color}">${cap(log.action)}</span>`;

        document.getElementById('oc-model').textContent =
            log.model_type ? (log.model_type + (log.model_id ? ' #' + log.model_id : '')) : '—';

        const descWrap = document.getElementById('oc-desc-wrap');
        if (log.description) {
            document.getElementById('oc-desc').textContent = log.description;
            descWrap.classList.remove('hidden');
        } else {
            descWrap.classList.add('hidden');
        }

        toggleRow('oc-address-row', 'oc-address', log.address);
        toggleRow('oc-ua-row', 'oc-ua', log.user_agent);

        fillValuesTable('oc-old-wrap', 'oc-old-body', log.old_values, 'bg-rose-50/30', 'text-rose-700');
        fillValuesTable('oc-new-wrap', 'oc-new-body', log.new_values, 'bg-emerald-50/30', 'text-emerald-700');

        document.getElementById('ocBackdrop').classList.remove('hidden');
        requestAnimationFrame(() => {
            document.getElementById('offcanvas').classList.remove('translate-x-full');
        });
    }

    function closeOffcanvas() {
        document.getElementById('offcanvas').classList.add('translate-x-full');
        document.getElementById('ocBackdrop').classList.add('hidden');
    }

    function toggleRow(rowId, textId, value) {
        const row = document.getElementById(rowId);
        if (value) {
            document.getElementById(textId).textContent = value;
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    }

    function fillValuesTable(wrapId, bodyId, values, rowBg, textColor) {
        const wrap = document.getElementById(wrapId);
        const body = document.getElementById(bodyId);
        body.innerHTML = '';
        if (values && Object.keys(values).length) {
            Object.entries(values).forEach(([key, val]) => {
                const display = (val === null || val === undefined) ? '<em class="text-slate-400">null</em>' :
                    (typeof val === 'object' ? `<code class="text-[10px]">${JSON.stringify(val)}</code>` : val);
                body.insertAdjacentHTML('beforeend',
                    `<tr class="${rowBg}">
                        <td class="px-4 py-2 font-medium text-slate-600 w-1/3 align-top border-r border-slate-100">${key}</td>
                        <td class="px-4 py-2 ${textColor} break-all align-top">${display}</td>
                    </tr>`);
            });
            wrap.classList.remove('hidden');
        } else {
            wrap.classList.add('hidden');
        }
    }

    function cap(str) {
        return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeOffcanvas();
    });
</script>
@endpush

@endsection
