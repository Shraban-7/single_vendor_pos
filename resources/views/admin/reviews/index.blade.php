@extends('admin.layouts.app')
@section('title', 'Reviews')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Product Reviews</h1>
        <p class="text-xs text-slate-500">Monitor customer feedback and moderate content</p>
    </div>
    <div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200/60">
            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
            {{ $reviews->where('is_approved', false)->count() }} Pending
        </span>
    </div>
</div>

<div class="space-y-3">
    @forelse($reviews as $review)
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-4">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="w-full lg:w-1/5 border-b lg:border-b-0 lg:border-r border-slate-100 pb-3 lg:pb-0 lg:pr-4">
                    <div class="flex items-center gap-2.5 mb-2.5">
                        <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
                            {{ strtoupper(substr($review->reviewer_name ?? $review->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">{{ $review->reviewer_name ?? $review->user->name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $review->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Product</p>
                        <a href="#" class="text-xs text-indigo-600 hover:underline font-medium line-clamp-1">
                            {{ $review->product->name }}
                        </a>
                        @if($review->is_verified_purchase)
                        <div class="flex items-center text-emerald-600 text-[10px] font-semibold">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Verified Purchase
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="flex text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : '' }}"></i>
                            @endfor
                        </div>
                        <h4 class="font-semibold text-slate-800 text-sm">{{ $review->title }}</h4>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">{{ $review->comment }}</p>

                    @if($review->images->count() > 0)
                    <div class="flex gap-1.5 mb-3">
                        @foreach($review->images as $image)
                        <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="h-12 w-12 object-cover rounded-lg border border-slate-200 hover:opacity-75 transition">
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <div class="flex items-center text-[10px] text-slate-400">
                        <i data-lucide="thumbs-up" class="w-3 h-3 mr-1"></i> {{ $review->helpful_count }} people found helpful
                    </div>
                </div>

                <div class="w-full lg:w-40 flex lg:flex-col justify-center gap-1.5">
                    @if(!$review->is_approved)
                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-full h-8 inline-flex items-center justify-center gap-1 text-[11px] font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/60 rounded-lg transition">
                            <i data-lucide="check" class="w-3 h-3"></i> Approve
                        </button>
                    </form>
                    @else
                    <div class="w-full h-8 inline-flex items-center justify-center text-[11px] font-medium bg-slate-50 text-slate-500 border border-slate-200 rounded-lg italic">
                        Approved
                    </div>
                    @endif

                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Delete this review forever?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full h-8 inline-flex items-center justify-center gap-1 text-[11px] font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 rounded-lg transition">
                            <i data-lucide="trash-2" class="w-3 h-3"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-12 text-center">
        <div class="flex flex-col items-center max-w-xs mx-auto">
            <div class="flex items-center justify-center w-12 h-12 mb-3 border rounded-xl bg-slate-50 text-slate-400 border-slate-100">
                <i data-lucide="message-square-off" class="w-5 h-5"></i>
            </div>
            <h3 class="font-bold text-slate-900">No reviews found</h3>
            <p class="text-xs text-slate-500 mt-0.5">Customer reviews will appear here once they start submitting feedback.</p>
        </div>
    </div>
    @endforelse
</div>

@if(method_exists($reviews, 'hasPages') && $reviews->hasPages())
<div class="mt-4">
    {{ $reviews->links() }}
</div>
@endif

@endsection
