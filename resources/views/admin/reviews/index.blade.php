@extends('admin.layouts.app')
@section('title', 'Reviews')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Product Reviews</h2>
            <p class="text-sm text-gray-500">Monitor customer feedback and moderate content</p>
        </div>
        <div class="flex gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-sm">
                <i class="fas fa-clock mr-2"></i> {{ $reviews->where('is_approved', false)->count() }} Pending Approval
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($reviews as $review)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-5">
                <div class="flex flex-col lg:flex-row gap-6">

                    <div class="w-full lg:w-1/4 border-b lg:border-b-0 lg:border-r border-gray-100 pb-4 lg:pb-0 lg:pr-6">
                        <div class="flex items-center mb-3">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                {{ strtoupper(substr($review->reviewer_name ?? $review->user->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $review->reviewer_name ?? $review->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Product</div>
                            <a href="#" class="text-sm text-blue-600 hover:underline font-medium line-clamp-1">
                                {{ $review->product->name }}
                            </a>

                            @if($review->is_verified_purchase)
                            <div class="flex items-center text-green-600 text-xs font-bold">
                                <i class="fas fa-check-circle mr-1"></i> Verified Purchase
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <div class="flex text-amber-400 mr-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star text-sm"></i>
                                    @endfor
                            </div>
                            <h4 class="font-bold text-gray-900">{{ $review->title }}</h4>
                        </div>

                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            {{ $review->comment }}
                        </p>

                        @if($review->images->count() > 0)
                        <div class="flex gap-2 mb-4">
                            @foreach($review->images as $image)
                            <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="block">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="h-16 w-16 object-cover rounded-lg border border-gray-200 hover:opacity-75 transition">
                            </a>
                            @endforeach
                        </div>
                        @endif

                        <div class="flex items-center text-xs text-gray-400">
                            <i class="fas fa-thumbs-up mr-1"></i> {{ $review->helpful_count }} people found this helpful
                        </div>
                    </div>

                    <div class="w-full lg:w-48 flex lg:flex-col justify-center gap-2">
                        @if(!$review->is_approved)
                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="w-full">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full py-2 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg text-sm font-semibold transition">
                                <i class="fas fa-check mr-2"></i> Approve
                            </button>
                        </form>
                        @else
                        <div class="w-full py-2 bg-gray-50 text-gray-500 border border-gray-200 rounded-lg text-sm font-semibold text-center italic">
                            Approved
                        </div>
                        @endif

                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Delete this review forever?')" class="w-full">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full py-2 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-lg text-sm font-semibold transition">
                                <i class="fas fa-trash-alt mr-2"></i> Delete
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center text-gray-500">
            <i class="fas fa-comment-slash text-4xl mb-4 opacity-20"></i>
            <p>No reviews found to display.</p>
        </div>
        @endforelse
    </div>

    @if(method_exists($reviews, 'hasPages') && $reviews->hasPages())
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection