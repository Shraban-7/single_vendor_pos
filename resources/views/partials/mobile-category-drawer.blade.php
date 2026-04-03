<!-- ==================== MOBILE MENU OVERLAY & DRAWER ==================== -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 z-[200] hidden opacity-0"></div>

<div id="mobileMenuDrawer"
    class="fixed top-0 left-0 w-[85%] max-w-[300px] h-full bg-white z-[210] transform -translate-x-full shadow-2xl flex flex-col">

    <!-- Drawer Header -->
    <div class="p-4 bg-blue-500 text-white flex justify-between items-center">
        <span class="font-bold text-lg">Categories</span>
        <button id="closeMobileMenu" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Drawer Content (scrollable) -->
    <div class="p-4 overflow-y-auto flex-1">
        <ul class="space-y-1">

            <!-- Home -->
            <li>
                <a href="#" class="block py-2.5 px-2 text-gray-700 font-medium hover:bg-gray-50 rounded">
                    Home
                </a>
            </li>

            <!-- Dynamic Categories -->
            @foreach ($allMenuCategories as $cat)
            <li>
                <button type="button"
                    class="mob-acc-btn w-full flex justify-between items-center py-2.5 px-2 
                               text-gray-700 font-medium hover:bg-gray-50 rounded"
                    data-target="#mob-{{ $cat->slug }}">

                    <span>{{ $cat->name }}</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                </button>

                <!-- Submenu -->
                <div id="mob-{{ $cat->slug }}" class="mob-acc-panel pl-4 bg-gray-50 rounded-lg">
                    <ul class="py-2 space-y-2 text-sm text-gray-600">

                        <!-- Category link -->
                        <li>
                            <a class="font-semibold text-blue-600"
                                href="{{ route('products.index',['category'=>$cat->slug]) }}">
                                {{ $cat->name }}
                            </a>
                        </li>

                        <!-- Subcategories -->
                        @foreach ($cat->children as $sub)
                        <li>
                            <a class="hover:text-blue-600"
                                href="{{ route('products.index',['subcategory'=>$sub->slug]) }}">
                                {{ $sub->name }}
                            </a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </li>
            @endforeach

            <!-- Other Static Links -->
            <li>
                <a href="#" class="block py-2.5 px-2 text-gray-700 font-medium hover:bg-gray-50 rounded">
                    Shop
                </a>
            </li>
            <li>
                <a href="{{ route('track-order.index') }}" class="block py-2.5 px-2 text-gray-700 font-medium hover:bg-gray-50 rounded">
                    Track Order
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Styles -->
<style>
    .mob-acc-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .mob-acc-panel.open {
        max-height: 1000px;
    }

    .mob-acc-btn i.rot {
        transform: rotate(180deg);
    }
</style>

<!-- Accordion Logic -->
<script>
    // --- Mobile Menu Logic ---
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuDrawer = document.getElementById('mobileMenuDrawer');

    function toggleMobileMenu(show) {
        if (show) {
            mobileMenuOverlay.classList.remove('hidden');
            setTimeout(() => mobileMenuOverlay.classList.remove('opacity-0'), 10);
            mobileMenuDrawer.classList.remove('-translate-x-full');
            document.body.style.overflow = 'hidden';
        } else {
            mobileMenuOverlay.classList.add('opacity-0');
            mobileMenuDrawer.classList.add('-translate-x-full');
            setTimeout(() => {
                mobileMenuOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => toggleMobileMenu(true));
    }

    if (closeMobileMenu) {
        closeMobileMenu.addEventListener('click', () => toggleMobileMenu(false));
    }

    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', () => toggleMobileMenu(false));
    }

    // --- Mobile Accordion Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const accordionButtons = document.querySelectorAll('.mob-acc-btn');

        accordionButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const panel = document.querySelector(targetId);
                const icon = this.querySelector('i');

                // Close other panels
                document.querySelectorAll('.mob-acc-panel').forEach(function(otherPanel) {
                    if (otherPanel !== panel && otherPanel.classList.contains('open')) {
                        otherPanel.classList.remove('open');
                        const otherButton = document.querySelector(`[data-target="#${otherPanel.id}"]`);
                        if (otherButton) {
                            const otherIcon = otherButton.querySelector('i');
                            if (otherIcon) otherIcon.classList.remove('rot');
                        }
                    }
                });

                // Toggle current panel
                if (panel) {
                    panel.classList.toggle('open');
                    if (icon) icon.classList.toggle('rot');
                }
            });
        });
    });
</script>