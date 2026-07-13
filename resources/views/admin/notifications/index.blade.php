@extends('admin.layouts.app')
@section('title', 'Notifications')
@section('content')

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Notifications</h3>
            <p class="mt-1 text-sm text-gray-500">View and manage system activities and alerts.</p>
        </div>
        
        <div class="flex items-center gap-2.5">
            @if($notifications->whereNull('read_at')->count() > 0)
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-50 border border-blue-200 px-4 py-2.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition shadow-sm">
                        <i class="fas fa-check-double mr-1.5"></i> Mark all as read
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Notification List Card --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    
                    // Retrieve color and icon from the cast SystemNotificationType enum
                    $color = $notification->type->color();
                    $icon = $notification->type->icon();
                @endphp
                <div class="relative p-5 transition hover:bg-gray-50/50 flex gap-4 {{ $isUnread ? 'bg-blue-50/20' : '' }}">
                    @if($isUnread)
                        {{-- Unread blue dot indicator --}}
                        <div class="absolute left-1.5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-600 rounded-full"></div>
                    @endif

                    {{-- Icon container --}}
                    <div class="shrink-0 w-10 h-10 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-100 text-{{ $color }}-600 flex items-center justify-center">
                        <i class="fas {{ $icon }} text-sm"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-2">
                            <h4 class="text-sm font-bold text-gray-900 {{ $isUnread ? 'font-extrabold' : '' }}">
                                {{ $notification->title }}
                            </h4>
                            <span class="text-xs text-gray-400 font-medium">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 {{ $isUnread ? 'font-medium text-gray-800' : '' }}">
                            {{ $notification->message }}
                        </p>
                        
                        @if($notification->action_url)
                            <div class="mt-2.5">
                                <a href="{{ route('admin.notifications.read-redirect', $notification->id) }}" 
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    <span>View target details</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        @else
                            @if($isUnread)
                                <button type="button" data-id="{{ $notification->id }}" class="mt-2 text-xs font-semibold text-gray-500 hover:text-gray-700 transition quick-read-btn">
                                    Mark as read
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-16 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-50 text-gray-400 border border-gray-100">
                        <i class="fas fa-bell text-xl"></i>
                    </div>
                    <p class="font-bold text-gray-700 text-base">All caught up!</p>
                    <p class="mt-1 text-xs text-gray-400">No new notifications available.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="border-t border-gray-100 px-5 py-4 bg-gray-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Quick mark as read (for items without redirect url)
        $('.quick-read-btn').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            
            $.ajax({
                url: `/admin/notifications/${id}/read`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.showSuccess(response.message);
                        // Reload notifications list
                        window.location.reload();
                    }
                },
                error: function() {
                    window.showError('Could not mark notification as read.');
                }
            });
        });
    });
</script>
@endpush

@endsection
