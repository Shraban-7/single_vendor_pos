@extends('customer.layout')
@section('title', 'Reviews')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">My Reviews</h2>
        <p class="text-gray-600 mt-1">Your product reviews and ratings</p>
    </div>

    @if($reviews->count() > 0)
    <div class="space-y-4">
        @foreach($reviews as $review)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Product Image -->
                <div class="w-full md:w-32 h-32 flex-shrink-0">
                    @if($review->product->images->count() > 0)
                    <img src="{{ asset('storage/' . $review->product->images->first()->image_path) }}"
                        alt="{{ $review->product->name }}"
                        class="w-full h-full object-cover rounded-lg">
                    @else
                    <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                    @endif
                </div>

                <!-- Review Content -->
                <div class="flex-1">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <a href="{{ route('products.show', $review->product->slug) }}"
                                class="font-semibold text-gray-800 hover:text-brand-blue transition">
                                {{ $review->product->name }}
                            </a>
                            <p class="text-sm text-gray-500 mt-1">
                                Reviewed on {{ $review->created_at->format('F d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                        </div>
                    </div>

                    @if($review->comment)
                    <p class="text-gray-700 mb-3">{{ $review->comment }}</p>
                    @endif

                    <!-- Review Images -->
                    @if($review->images->count() > 0)
                    <div class="flex gap-2 mb-3">
                        @foreach($review->images as $image)
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                            alt="Review image"
                            class="w-16 h-16 object-cover rounded cursor-pointer hover:opacity-75 transition"
                            onclick="viewImage('{{ asset('storage/' . $image->image_path) }}')">
                        @endforeach
                    </div>
                    @endif

                    <!-- Admin Response -->
                    @if($review->admin_response)
                    <div class="bg-brand-blue-50 border-l-4 border-brand-blue p-4 rounded mt-3">
                        <p class="text-sm font-semibold text-brand-blue-900 mb-1">
                            <i class="fas fa-reply mr-1"></i>
                            Store Response
                        </p>
                        <p class="text-sm text-gray-700">{{ $review->admin_response }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-8">
        {{ $reviews->links() }}
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
            <i class="fas fa-star text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No Reviews Yet</h3>
        <p class="text-gray-600 mb-6">Share your experience by reviewing products you've purchased.</p>
        <a href="{{ route('orders.index') }}"
            class="inline-block bg-brand-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-brand-blue-700 transition">
            <i class="fas fa-shopping-bag mr-2"></i>
            View Your Orders
        </a>
    </div>
    @endif
</div>

<!-- Image Viewer Modal -->
<div id="imageViewerModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4" onclick="closeImageViewer()">
    <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">
        <i class="fas fa-times"></i>
    </button>
    <img id="viewerImage" src="" alt="Review image" class="max-w-full max-h-full object-contain">
</div>

@push('scripts')
<script>
    function viewImage(imageUrl) {
        document.getElementById('viewerImage').src = imageUrl;
        document.getElementById('imageViewerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageViewer() {
        document.getElementById('imageViewerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Prevent closing when clicking the image
    document.getElementById('viewerImage').addEventListener('click', function(e) {
        e.stopPropagation();
    });
</script>
@endpush
@endsection