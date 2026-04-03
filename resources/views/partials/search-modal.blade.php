{{-- Search Overlay --}}
<div id="searchOverlay" onclick="closeSearch()" class="fixed inset-0 bg-black/50 z-50 opacity-0 invisible transition-opacity duration-300"></div>

{{-- Search Modal --}}
<div id="searchModal" class="fixed top-0 left-0 right-0 z-50 transform -translate-y-full transition-transform duration-300 ease-out">
    <div class="bg-white shadow-2xl max-h-[85vh] overflow-hidden">
        <div class="max-w-4xl mx-auto">
            {{-- Search Header --}}
            <div class="p-4 md:p-5 border-b border-gray-100">
                <div class="relative flex items-center gap-3">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search for products, categories..."
                            autocomplete="off"
                            class="w-full h-12 md:h-14 pl-12 pr-12 bg-gray-100 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-brand-blue focus:bg-white transition"
                            oninput="handleSearchInput(this.value)">
                        <button onclick="clearSearch()" id="clearSearchBtn" class="hidden absolute right-4 top-1/2 -translate-y-1/2 w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center hover:bg-gray-400 transition">
                            <i class="fas fa-times text-white text-xs"></i>
                        </button>
                    </div>
                    <button onclick="closeSearch()" class="w-12 h-12 md:hidden rounded-xl hover:bg-gray-100 flex items-center justify-center transition">
                        <i class="fas fa-times text-gray-500 text-lg"></i>
                    </button>
                    <button onclick="closeSearch()" class="hidden md:flex items-center gap-2 text-gray-500 hover:text-gray-700 transition text-sm">
                        <span>ESC</span>
                    </button>
                </div>
            </div>

            {{-- Search Content --}}
            <div class="overflow-y-auto max-h-[calc(85vh-80px)]">

                {{-- Default State (No Search) --}}
                <div id="searchDefaultState" class="p-4 md:p-5">
                    {{-- Recent Searches --}}
                    <div id="recentSearches" class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900">Recent Searches</h3>
                            <button onclick="clearRecentSearches()" class="text-xs text-gray-500 hover:text-red-500 transition">Clear All</button>
                        </div>
                        <div class="flex flex-wrap gap-2" id="recentSearchesList">
                            <button onclick="setSearchQuery('formal shirt')" class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                                <i class="fas fa-history text-gray-400 text-xs"></i>
                                formal shirt
                            </button>
                            <button onclick="setSearchQuery('panjabi')" class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                                <i class="fas fa-history text-gray-400 text-xs"></i>
                                panjabi
                            </button>
                            <button onclick="setSearchQuery('t-shirt')" class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                                <i class="fas fa-history text-gray-400 text-xs"></i>
                                t-shirt
                            </button>
                        </div>
                    </div>

                    {{-- Trending Searches --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-fire text-orange-500"></i>
                            Trending Now
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="setSearchQuery('eid collection')" class="px-4 py-2 bg-gradient-to-r from-orange-100 to-red-100 rounded-full text-sm font-medium text-orange-700 hover:from-orange-200 hover:to-red-200 transition">
                                Eid Collection
                            </button>
                            <button onclick="setSearchQuery('premium panjabi')" class="px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-full text-sm font-medium text-blue-700 hover:from-blue-200 hover:to-indigo-200 transition">
                                Premium Panjabi
                            </button>
                            <button onclick="setSearchQuery('cotton shirt')" class="px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 rounded-full text-sm font-medium text-green-700 hover:from-green-200 hover:to-emerald-200 transition">
                                Cotton Shirt
                            </button>
                            <button onclick="setSearchQuery('polo t-shirt')" class="px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-full text-sm font-medium text-purple-700 hover:from-purple-200 hover:to-pink-200 transition">
                                Polo T-Shirt
                            </button>
                        </div>
                    </div>

                    {{-- Popular Categories --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Popular Categories</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <a href="{{ route('products.index') }}?category=mens" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-brand-blue/5 hover:border-brand-blue border border-transparent transition group">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-brand-blue/20 transition">
                                    <i class="fas fa-male text-brand-blue text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-brand-blue transition">Men's Wear</span>
                            </a>
                            <a href="{{ route('products.index') }}?category=womens" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-brand-blue/5 hover:border-brand-blue border border-transparent transition group">
                                <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-brand-blue/20 transition">
                                    <i class="fas fa-female text-pink-500 text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-brand-blue transition">Women's Wear</span>
                            </a>
                            <a href="{{ route('products.index') }}?category=panjabi" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-brand-blue/5 hover:border-brand-blue border border-transparent transition group">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-brand-blue/20 transition">
                                    <i class="fas fa-vest text-orange-500 text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-brand-blue transition">Panjabi</span>
                            </a>
                            <a href="{{ route('products.index') }}?category=kids" class="flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-brand-blue/5 hover:border-brand-blue border border-transparent transition group">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-brand-blue/20 transition">
                                    <i class="fas fa-child text-green-500 text-xl"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-brand-blue transition">Kids</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Search Results State --}}
                <div id="searchResultsState" class="hidden">
                    {{-- Search Suggestions --}}
                    <div id="searchSuggestions" class="border-b border-gray-100 py-2">
                        {{-- Suggestions will be populated by JS --}}
                    </div>

                    {{-- Product Results --}}
                    <div class="p-4 md:p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Products <span id="resultCount" class="text-gray-400 font-normal">(0 results)</span>
                            </h3>
                            <a href="#" id="viewAllResults" class="text-sm text-brand-blue font-medium hover:underline">View All</a>
                        </div>

                        <div id="productResults" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-4">
                            {{-- Products will be populated by JS --}}
                        </div>

                        {{-- No Results State --}}
                        <div id="noResultsState" class="hidden py-10 text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-3xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No products found</h3>
                            <p class="text-sm text-gray-500 mb-4">Try searching with different keywords</p>
                            <a href="{{ route('products.index') }}" onclick="closeSearch()" class="inline-flex items-center gap-2 text-brand-blue font-medium text-sm hover:underline">
                                Browse All Products
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Search Scripts --}}
<script>
    // Static product data
    const searchProducts = [{
            id: 1,
            name: 'Premium Cotton Formal Shirt',
            slug: 'premium-cotton-formal-shirt',
            price: 1250,
            originalPrice: 1500,
            image: 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=300&h=400&fit=crop',
            category: 'Shirts',
            tags: ['formal', 'shirt', 'cotton', 'premium', 'office']
        },
        {
            id: 2,
            name: 'Printed Casual Shirt',
            slug: 'printed-casual-shirt',
            price: 950,
            originalPrice: 1200,
            image: 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=300&h=400&fit=crop',
            category: 'Shirts',
            tags: ['casual', 'shirt', 'printed', 'colorful']
        },
        {
            id: 3,
            name: 'Classic Round Neck T-Shirt',
            slug: 'classic-round-neck-tshirt',
            price: 650,
            originalPrice: 800,
            image: 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=300&h=400&fit=crop',
            category: 'T-Shirts',
            tags: ['t-shirt', 'tshirt', 'casual', 'round neck', 'basic']
        },
        {
            id: 4,
            name: 'Premium Striped Polo',
            slug: 'premium-striped-polo',
            price: 990,
            originalPrice: null,
            image: 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=300&h=400&fit=crop',
            category: 'Polo',
            tags: ['polo', 't-shirt', 'striped', 'premium', 'casual']
        },
        {
            id: 5,
            name: 'Denim Casual Jacket',
            slug: 'denim-casual-jacket',
            price: 2450,
            originalPrice: 2800,
            image: 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=300&h=400&fit=crop',
            category: 'Jackets',
            tags: ['jacket', 'denim', 'casual', 'winter']
        },
        {
            id: 6,
            name: 'Traditional Panjabi - Navy Blue',
            slug: 'traditional-panjabi-navy',
            price: 1850,
            originalPrice: 2200,
            image: 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=300&h=400&fit=crop',
            category: 'Panjabi',
            tags: ['panjabi', 'traditional', 'eid', 'festive', 'navy']
        },
        {
            id: 7,
            name: 'Designer Panjabi - White',
            slug: 'designer-panjabi-white',
            price: 2200,
            originalPrice: null,
            image: 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=300&h=400&fit=crop',
            category: 'Panjabi',
            tags: ['panjabi', 'designer', 'white', 'eid', 'premium']
        },
        {
            id: 8,
            name: 'Cotton Chinos - Beige',
            slug: 'cotton-chinos-beige',
            price: 1450,
            originalPrice: 1700,
            image: 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=300&h=400&fit=crop',
            category: 'Pants',
            tags: ['pants', 'chinos', 'cotton', 'beige', 'casual']
        },
        {
            id: 9,
            name: 'Ladies Kameez - Floral Print',
            slug: 'ladies-kameez-floral',
            price: 1350,
            originalPrice: 1600,
            image: 'https://images.unsplash.com/photo-1583391733956-6c78276477e2?w=300&h=400&fit=crop',
            category: 'Ladies',
            tags: ['ladies', 'women', 'kameez', 'floral', 'traditional']
        },
        {
            id: 10,
            name: 'Kids Panjabi Set',
            slug: 'kids-panjabi-set',
            price: 950,
            originalPrice: 1100,
            image: 'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?w=300&h=400&fit=crop',
            category: 'Kids',
            tags: ['kids', 'children', 'panjabi', 'eid', 'festive']
        }
    ];

    // Search suggestions based on query
    const searchSuggestionKeywords = [
        'formal shirt', 'casual shirt', 'cotton shirt', 'printed shirt',
        't-shirt', 'polo t-shirt', 'round neck t-shirt',
        'panjabi', 'premium panjabi', 'eid panjabi', 'designer panjabi',
        'jacket', 'denim jacket', 'winter jacket',
        'pants', 'chinos', 'formal pants',
        'ladies wear', 'kameez', 'saree',
        'kids wear', 'kids panjabi'
    ];

    let searchTimeout;

    function openSearch() {
        const modal = document.getElementById('searchModal');
        const overlay = document.getElementById('searchOverlay');
        const input = document.getElementById('searchInput');

        modal.classList.remove('-translate-y-full');
        overlay.classList.remove('opacity-0', 'invisible');
        overlay.classList.add('opacity-100', 'visible');
        document.body.style.overflow = 'hidden';

        setTimeout(() => input.focus(), 100);
    }

    function closeSearch() {
        const modal = document.getElementById('searchModal');
        const overlay = document.getElementById('searchOverlay');

        modal.classList.add('-translate-y-full');
        overlay.classList.add('opacity-0', 'invisible');
        overlay.classList.remove('opacity-100', 'visible');
        document.body.style.overflow = '';
    }

    function clearSearch() {
        const input = document.getElementById('searchInput');
        input.value = '';
        input.focus();
        handleSearchInput('');
    }

    function setSearchQuery(query) {
        const input = document.getElementById('searchInput');
        input.value = query;
        handleSearchInput(query);
    }

    function handleSearchInput(query) {
        const clearBtn = document.getElementById('clearSearchBtn');
        const defaultState = document.getElementById('searchDefaultState');
        const resultsState = document.getElementById('searchResultsState');

        if (query.length === 0) {
            clearBtn.classList.add('hidden');
            defaultState.classList.remove('hidden');
            resultsState.classList.add('hidden');
            return;
        }

        clearBtn.classList.remove('hidden');
        defaultState.classList.add('hidden');
        resultsState.classList.remove('hidden');

        // Debounce search
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performSearch(query), 150);
    }

    function performSearch(query) {
        const lowerQuery = query.toLowerCase();

        // Find matching suggestions
        const matchingSuggestions = searchSuggestionKeywords.filter(keyword =>
            keyword.toLowerCase().includes(lowerQuery)
        ).slice(0, 5);

        // Find matching products
        const matchingProducts = searchProducts.filter(product => {
            const searchText = `${product.name} ${product.category} ${product.tags.join(' ')}`.toLowerCase();
            return searchText.includes(lowerQuery);
        });

        // Render suggestions
        renderSuggestions(matchingSuggestions, query);

        // Render products
        renderProducts(matchingProducts);
    }

    function renderSuggestions(suggestions, query) {
        const container = document.getElementById('searchSuggestions');

        if (suggestions.length === 0) {
            container.innerHTML = '';
            return;
        }

        const html = suggestions.map(suggestion => {
            const highlighted = suggestion.replace(
                new RegExp(`(${query})`, 'gi'),
                '<strong class="text-brand-blue">$1</strong>'
            );
            return `
                <button onclick="setSearchQuery('${suggestion}')" class="flex items-center gap-3 w-full px-4 md:px-5 py-3 hover:bg-gray-50 transition text-left">
                    <i class="fas fa-search text-gray-300 text-sm"></i>
                    <span class="text-sm text-gray-600">${highlighted}</span>
                    <i class="fas fa-arrow-up-right text-gray-300 text-xs ml-auto rotate-45"></i>
                </button>
            `;
        }).join('');

        container.innerHTML = html;
    }

    function renderProducts(products) {
        const container = document.getElementById('productResults');
        const noResults = document.getElementById('noResultsState');
        const resultCount = document.getElementById('resultCount');
        const viewAllLink = document.getElementById('viewAllResults');

        resultCount.textContent = `(${products.length} results)`;

        if (products.length === 0) {
            container.classList.add('hidden');
            noResults.classList.remove('hidden');
            viewAllLink.classList.add('hidden');
            return;
        }

        container.classList.remove('hidden');
        noResults.classList.add('hidden');
        viewAllLink.classList.remove('hidden');

        const query = document.getElementById('searchInput').value;
        viewAllLink.href = `{{ route('products.index') }}?search=${encodeURIComponent(query)}`;

        const html = products.slice(0, 8).map(product => `
            <a href="{{ url('/products') }}/${product.slug}" onclick="closeSearch()" class="bg-white rounded-xl overflow-hidden border border-gray-100 hover:border-brand-blue hover:shadow-lg transition group">
                <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    ${product.originalPrice ? `<span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-semibold px-2 py-1 rounded-full">-${Math.round((1 - product.price / product.originalPrice) * 100)}%</span>` : ''}
                </div>
                <div class="p-3">
                    <span class="text-[10px] text-gray-400 uppercase tracking-wide">${product.category}</span>
                    <h4 class="text-xs sm:text-sm font-medium text-gray-900 line-clamp-2 mt-1 group-hover:text-brand-blue transition">${product.name}</h4>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-brand-blue font-bold text-sm">৳${product.price.toLocaleString()}</span>
                        ${product.originalPrice ? `<span class="text-gray-400 text-xs line-through">৳${product.originalPrice.toLocaleString()}</span>` : ''}
                    </div>
                </div>
            </a>
        `).join('');

        container.innerHTML = html;
    }

    function clearRecentSearches() {
        document.getElementById('recentSearchesList').innerHTML = `
            <p class="text-sm text-gray-400">No recent searches</p>
        `;
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSearch();
        }
        // Open search with Ctrl/Cmd + K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearch();
        }
    });
</script>