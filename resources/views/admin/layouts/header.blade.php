<header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-40">
    {{-- Menu Toggle Button (All Screens) --}}
    <button id="sidebarToggle" class="text-slate-500 hover:text-slate-700 hover:bg-slate-100 p-2 rounded-xl transition focus:outline-none">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Search Bar --}}
    <div class="flex-1 max-w-2xl mx-auto hidden md:block" x-data="adminSearch()" @click.away="open = false">
        <div class="relative">
            <input type="search" x-model="query" @input.debounce.250ms="search()" @focus="query.length >= 2 && (open = true)"
                   placeholder="Search orders, products, customers..." autocomplete="off"
                   class="w-full h-10 pl-10 pr-4 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 transition">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

            <div x-show="open" x-cloak x-transition.opacity
                 class="absolute left-0 right-0 mt-2 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden max-h-96 overflow-y-auto">
                <template x-if="loading">
                    <div class="px-4 py-6 text-center text-sm text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>Searching...
                    </div>
                </template>

                <template x-if="!loading && results.length === 0 && queried">
                    <div class="px-4 py-6 text-center text-sm text-slate-400">No results found.</div>
                </template>

                <template x-for="item in results" :key="item.type + item.label">
                    <a :href="item.url" @click="open = false"
                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition border-b border-slate-50 last:border-0">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg shrink-0"
                             :class="{
                                 'bg-indigo-100 text-indigo-600': item.type === 'sale',
                                 'bg-emerald-100 text-emerald-600': item.type === 'product',
                                 'bg-sky-100 text-sky-600': item.type === 'customer'
                             }">
                            <i class="fa-solid"
                               :class="item.type === 'sale' ? 'fa-receipt' : (item.type === 'product' ? 'fa-box' : 'fa-user')"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900 truncate" x-text="item.label"></p>
                            <p class="text-xs text-slate-500 truncate" x-text="item.subtitle || ''"></p>
                        </div>
                        <span class="text-[10px] uppercase font-semibold tracking-wide px-1.5 py-0.5 rounded bg-slate-100 text-slate-500" x-text="item.type"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>

    {{-- Header Actions --}}
    <div class="flex items-center gap-3">
        {{-- Cash Register Status --}}
        @php
            $cashRegister = \App\Models\CashRegister::whereNull('closed_at')->latest()->first();
        @endphp
        @if($cashRegister)
            <div class="hidden sm:flex items-center gap-2 px-3 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                <div class="leading-tight">
                    <span class="text-[10px] uppercase font-semibold tracking-wide text-emerald-600">Register Open</span>
                    <p class="text-sm font-bold tabular-nums">{{ money($cashRegister->opening_amount) }}</p>
                </div>
            </div>
        @else
            <a href="{{ route('admin.pos.index') }}"
               class="hidden sm:flex items-center gap-2 px-3 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 hover:bg-amber-100 transition">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                <div class="leading-tight">
                    <span class="text-[10px] uppercase font-semibold tracking-wide text-amber-600">Register</span>
                    <p class="text-sm font-bold">Not Open</p>
                </div>
            </a>
        @endif

        {{-- Notifications --}}
        <div class="relative" x-data="adminNotifications()" x-init="init()">
            <button @click="toggle()" class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 99 ? '99+' : unreadCount"
                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center text-[10px] font-bold text-white bg-rose-500 rounded-full"></span>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak x-transition.origin.top.right
                 class="dropdown-menu absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-900">Notifications</p>
                    <button x-show="unreadCount > 0" @click="markAll()" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Mark all read</button>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <template x-if="loading && items.length === 0">
                        <div class="px-4 py-8 text-center text-sm text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...
                        </div>
                    </template>

                    <template x-if="!loading && items.length === 0">
                        <div class="px-4 py-10 text-center text-sm text-slate-400">
                            <i class="fa-regular fa-bell-slash mr-2"></i>You're all caught up!
                        </div>
                    </template>

                    <template x-for="item in items" :key="item.id">
                        <div class="flex gap-3 px-4 py-3 border-b border-slate-50 transition hover:bg-slate-50"
                             :class="item.is_unread ? 'bg-indigo-50/40' : ''">
                            <div class="flex items-center justify-center w-9 h-9 rounded-lg shrink-0 text-white"
                                 :style="'background-color:' + colorHex(item.color)">
                                <i class="fa-solid" :class="item.icon"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <a :href="item.action_url" @click="markRead(item)" class="block">
                                    <p class="text-sm font-medium text-slate-900 leading-snug" x-text="item.title"></p>
                                    <p class="text-xs text-slate-500 mt-0.5 leading-snug" x-text="item.message"></p>
                                    <p class="text-[11px] text-slate-400 mt-1" x-text="item.time"></p>
                                </a>
                            </div>
                            <button x-show="item.is_unread" @click="markRead(item)"
                                    class="self-center text-slate-300 hover:text-indigo-600" title="Mark as read">
                                <i class="fa-solid fa-circle text-[8px]"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <a href="{{ route('admin.notifications.index') }}"
                   class="block px-4 py-3 text-center text-sm font-medium text-indigo-600 hover:bg-slate-50 border-t border-slate-100">
                    View all notifications
                </a>
            </div>
        </div>

        {{-- User Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-slate-100 rounded-xl transition">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 text-slate-500"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-lg py-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="circle-user" class="w-5 h-5"></i>
                    <span>My Profile</span>
                </a>
                <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span>Account Settings</span>
                </a>
                <div class="border-t border-slate-100 my-2"></div>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition w-full text-left">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    function adminSearch() {
        return {
            query: '',
            results: [],
            open: false,
            loading: false,
            queried: false,
            search() {
                const q = this.query.trim();
                if (q.length < 2) {
                    this.results = [];
                    this.open = false;
                    this.queried = false;
                    return;
                }
                this.open = true;
                this.loading = true;
                this.queried = true;
                fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => { this.results = data.results || []; })
                    .catch(() => { this.results = []; })
                    .finally(() => { this.loading = false; });
            }
        };
    }

    function adminNotifications() {
        return {
            open: false,
            items: [],
            unreadCount: 0,
            loading: false,
            timer: null,
            init() {
                this.poll();
                this.timer = setInterval(() => this.poll(), 30000);
                document.addEventListener('turbolinks:visit', () => clearInterval(this.timer));
            },
            toggle() {
                this.open = !this.open;
                if (this.open) this.poll();
            },
            poll() {
                this.loading = true;
                fetch('{{ route('admin.notifications.poll') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        this.items = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                    })
                    .catch(() => {})
                    .finally(() => { this.loading = false; });
            },
            markRead(item) {
                if (!item.is_unread) return;
                item.is_unread = false;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                fetch(item.read_url || '{{ url('admin/notifications') }}/' + item.id + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(() => {});
            },
            markAll() {
                const prev = this.unreadCount;
                this.items.forEach(i => i.is_unread = false);
                this.unreadCount = 0;
                fetch('{{ route('admin.notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(() => { this.unreadCount = prev; this.items.forEach(i => i.is_unread = true); });
            },
            colorHex(color) {
                const map = {
                    blue: '#3b82f6', indigo: '#6366f1', teal: '#14b8a6', purple: '#a855f7',
                    green: '#22c55e', red: '#ef4444', yellow: '#eab308', amber: '#f59e0b'
                };
                return map[color] || '#6366f1';
            }
        };
    }
</script>
