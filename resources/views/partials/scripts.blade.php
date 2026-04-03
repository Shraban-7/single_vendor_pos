{{-- JavaScript --}}
<script>
    // ===== Hero Slider =====
    (function() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const prevBtn = document.getElementById('prevSlide');
        const nextBtn = document.getElementById('nextSlide');
        let currentSlide = 0;
        const totalSlides = slides.length;
        let autoSlideInterval;
        let touchStartX = 0;
        let touchEndX = 0;

        // Show specific slide
        function showSlide(index) {
            // Handle wraparound
            if (index >= totalSlides) index = 0;
            if (index < 0) index = totalSlides - 1;

            currentSlide = index;

            // Update slides
            slides.forEach((slide, i) => {
                slide.style.opacity = i === currentSlide ? '1' : '0';
                slide.style.zIndex = i === currentSlide ? '1' : '0';
            });

            // Update progress bar indicators
            const progressBars = document.querySelectorAll('.slider-progress-bar');
            progressBars.forEach((bar, i) => {
                if (i === currentSlide) {
                    bar.style.width = '0%';
                    bar.style.transition = 'none';
                    setTimeout(() => {
                        bar.style.transition = 'width 5s linear';
                        bar.style.width = '100%';
                    }, 50);
                } else {
                    bar.style.transition = 'none';
                    bar.style.width = '0%';
                }
            });

            // Update slide number
            const slideNumEl = document.getElementById('currentSlideNum');
            if (slideNumEl) {
                slideNumEl.textContent = String(currentSlide + 1).padStart(2, '0');
            }
        }

        // Next slide
        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        // Previous slide
        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        // Start auto-sliding
        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, 5000);
        }

        // Stop auto-sliding
        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        // Event listeners for buttons
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                stopAutoSlide();
                nextSlide();
                startAutoSlide();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                stopAutoSlide();
                prevSlide();
                startAutoSlide();
            });
        }

        // Event listeners for dots
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => {
                stopAutoSlide();
                showSlide(i);
                startAutoSlide();
            });
        });

        // Touch events for mobile swipe
        const sliderContainer = document.querySelector('.slider-container');

        if (sliderContainer) {
            sliderContainer.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, {
                passive: true
            });

            sliderContainer.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, {
                passive: true
            });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                stopAutoSlide();
                if (diff > 0) {
                    nextSlide(); // Swipe left - next
                } else {
                    prevSlide(); // Swipe right - prev
                }
                startAutoSlide();
            }
        }

        // Initialize
        if (slides.length > 0) {
            showSlide(0);
            startAutoSlide();
        }

        // Pause on hover (desktop)
        const slider = document.getElementById('slider');
        if (slider) {
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);
        }
    })();

    // ===== Smooth scroll for anchor links =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // ===== Add to Cart Animation =====
    // document.querySelectorAll('button').forEach(btn => {
    //     if (btn.textContent.includes('Add to Cart')) {
    //         btn.addEventListener('click', function() {
    //             const originalText = this.textContent;
    //             this.textContent = 'Added! ✓';
    //             this.classList.add('bg-green-500');
    //             this.classList.remove('bg-brand-blue');

    //             setTimeout(() => {
    //                 this.textContent = originalText;
    //                 this.classList.remove('bg-green-500');
    //                 this.classList.add('bg-brand-blue');
    //             }, 1500);
    //         });
    //     }
    // });

    // ===== Wishlist Toggle =====
    // document.querySelectorAll('.product-card button[aria-label], .product-card .absolute.top-2.right-2').forEach(btn => {
    //     btn.addEventListener('click', function(e) {
    //         e.preventDefault();
    //         const icon = this.querySelector('i');

    //         if (icon) {
    //             if (icon.classList.contains('far')) {
    //                 // Switch to filled heart
    //                 icon.classList.remove('far', 'text-gray-600');
    //                 icon.classList.add('fas', 'text-red-500');
    //             } else {
    //                 // Switch to outline heart
    //                 icon.classList.remove('fas', 'text-red-500');
    //                 icon.classList.add('far', 'text-gray-600');
    //             }
    //         }
    //     });
    // });

    // ===== Header shadow on scroll =====
    const header = document.querySelector('header');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 10) {
            header.classList.add('header-scrolled');
        } else {
            header.classList.remove('header-scrolled');
        }

        lastScroll = currentScroll;
    }, {
        passive: true
    });

    // ===== Newsletter form =====
    // const newsletterForm = document.querySelector('form');
    // if (newsletterForm) {
    //     newsletterForm.addEventListener('submit', function(e) {
    //         e.preventDefault();
    //         const input = this.querySelector('input[type="email"]');
    //         const btn = this.querySelector('button');

    //         if (input.value) {
    //             btn.textContent = 'Subscribed! ✓';
    //             btn.classList.add('bg-green-600');
    //             input.value = '';

    //             setTimeout(() => {
    //                 btn.textContent = 'Subscribe';
    //                 btn.classList.remove('bg-green-600');
    //             }, 3000);
    //         }
    //     });
    // }

    // ===== Lazy loading images =====
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ===== Console branding =====
    console.log('%c Spinner Fashion ', 'background: #228bcc; color: white; font-size: 20px; font-weight: bold; padding: 10px 20px; border-radius: 5px;');
    console.log('%c Premium Bangladeshi Clothing Brand ', 'color: #6b7280; font-size: 12px;');
</script>