<section class="py-12 md:py-16 bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-xl md:text-3xl font-bold text-brand-black mb-2">Subscribe for Exclusive Offers</h2>
        <p class="text-gray-700 text-base md:text-lg mb-8">Stay updated with our latest collections and offers.</p>

        <form class="max-w-lg mx-auto flex flex-col md:flex-row gap-4" action="{{ route('subscribe') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-gray-800 text-sm border border-gray-300 focus:outline-none focus:ring-4 focus:ring-blue-300">
            <button type="submit" class="bg-brand-black text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-gray-900 transition-all duration-200">Subscribe</button>
        </form>
    </div>
</section>