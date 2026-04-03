/**
 * Product Variant Manager
 * Handles product variant selection and quick view functionality
 */
class ProductVariantManager {
    constructor() {
        this.selectedColor = null;
        this.selectedSize = null;
        this.currentProduct = null;
        this.variants = [];
        this.init();
    }

    init() {
        // Setup quick view buttons on product cards
        //this.setupQuickViewButtons();

        // Close modal on outside click
        document
            .getElementById("productQuickViewModal")
            ?.addEventListener("click", (e) => {
                if (e.target.id === "productQuickViewModal") {
                    this.closeQuickView();
                }
            });

        document.querySelectorAll(".quick-view-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const productId = e.currentTarget.dataset.productId;

                if (productId) {
                    this.openQuickView(productId);
                }
            });
        });

        // Close modal on ESC key
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.closeQuickView();
            }
        });
    }

    setupQuickViewButtons() {
        // Add quick view icon to all product cards
        const productCards = document.querySelectorAll(".product-card");
        productCards.forEach((card) => {
            const productId =
                card.querySelector("[data-product-id]")?.dataset.productId;
            if (!productId) return;

            // Check if quick view button already exists
            if (card.querySelector(".quick-view-btn")) return;

            // Create quick view button
            const imageContainer = card.querySelector(".relative");
            if (imageContainer) {
                const quickViewBtn = document.createElement("button");
                quickViewBtn.className =
                    "quick-view-btn absolute bottom-2 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur px-4 py-2 rounded-full shadow-lg hover:bg-white transition opacity-0 group-hover:opacity-100 text-sm font-medium flex items-center gap-2";
                quickViewBtn.innerHTML =
                    '<i class="fas fa-eye"></i> Quick View';
                quickViewBtn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.openQuickView(productId);
                };
                imageContainer.appendChild(quickViewBtn);

                // Add group class to card for hover effect
                card.classList.add("group");
            }
        });
    }

    async openQuickView(productId) {
        try {
            // Show loading state
            const modal = document.getElementById("productQuickViewModal");
            modal.classList.add("show");

            // Fetch product data
            const response = await fetch(`/products/${productId}/quickview`, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) throw new Error("Failed to fetch product");

            const data = await response.json();
            this.currentProduct = data.product;
            this.variants = data.product.variants || [];

            // Populate modal
            this.populateQuickView(data.product);
        } catch (error) {
            console.error("Error loading quick view:", error);
            if (window.showError) {
                window.showError("Failed to load product details");
            }
            this.closeQuickView();
        }
    }

    populateQuickView(product) {
        // Set image
        const imgElement = document.getElementById("quickViewImage");
        imgElement.src = product.image || "/assets/images/default.png";
        imgElement.alt = product.name;

        // Set thumbnails
        const thumbnailsContainer = document.getElementById(
            "quickViewThumbnails",
        );
        thumbnailsContainer.innerHTML = "";
        if (product.images && product.images.length > 1) {
            product.images.forEach((image, index) => {
                const thumb = document.createElement("button");
                thumb.className = `flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 ${index === 0 ? "border-brand-blue" : "border-transparent hover:border-brand-blue"} transition`;
                thumb.innerHTML = `<img src="${image.image_url}" alt="${product.name}" class="w-full h-full object-cover">`;
                thumb.onclick = () => {
                    imgElement.src = image.image_url;
                    thumbnailsContainer
                        .querySelectorAll("button")
                        .forEach((b) =>
                            b.classList.remove("border-brand-blue"),
                        );
                    thumb.classList.add("border-brand-blue");
                };
                thumbnailsContainer.appendChild(thumb);
            });
        }

        // Set badges
        const badgesContainer = document.getElementById("quickViewBadges");
        badgesContainer.innerHTML = "";
        if (product.is_new_arrival) {
            badgesContainer.innerHTML +=
                '<span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-full">NEW</span>';
        }
        if (product.is_best_seller) {
            badgesContainer.innerHTML +=
                '<span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full">HOT</span>';
        }
        if (product.is_on_sale) {
            badgesContainer.innerHTML +=
                '<span class="bg-orange-500 text-white text-xs font-semibold px-2 py-1 rounded-full">SALE</span>';
        }

        // Set product info
        document.getElementById("quickViewBrand").textContent =
            product.brand || "Spinner Fashion";
        document.getElementById("quickViewName").textContent = product.name;

        // Set rating
        const ratingContainer = document.getElementById("quickViewRating");
        if (product.review_count > 0) {
            let stars = "";
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(product.average_rating)) {
                    stars +=
                        '<i class="fas fa-star text-yellow-400 text-sm"></i>';
                } else if (i - 0.5 <= product.average_rating) {
                    stars +=
                        '<i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>';
                } else {
                    stars +=
                        '<i class="far fa-star text-gray-300 text-sm"></i>';
                }
            }
            ratingContainer.innerHTML = `
                <div class="flex gap-0.5">${stars}</div>
                <span class="text-xs text-gray-500">(${product.review_count} reviews)</span>
            `;
        } else {
            ratingContainer.innerHTML =
                '<span class="text-xs text-gray-500">No reviews yet</span>';
        }

        // Set price
        document.getElementById("quickViewPrice").textContent =
            `৳${Number(product.price).toLocaleString()}`;

        const comparePrice = document.getElementById("quickViewComparePrice");
        const discount = document.getElementById("quickViewDiscount");

        if (product.compare_price && product.compare_price > product.price) {
            comparePrice.textContent = `৳${Number(product.compare_price).toLocaleString()}`;
            comparePrice.classList.remove("hidden");

            const discountPercent = Math.round(
                ((product.compare_price - product.price) /
                    product.compare_price) *
                    100,
            );
            discount.textContent = `-${discountPercent}%`;
            discount.classList.remove("hidden");
        } else {
            comparePrice.classList.add("hidden");
            discount.classList.add("hidden");
        }

        // Set stock status
        // const stockContainer = document.getElementById("quickViewStock");
        // if (product.stock_in > 0) {
        //     stockContainer.innerHTML =
        //         '<i class="fas fa-check-circle text-green-600"></i><span class="text-sm text-green-600 font-medium">In Stock</span>';
        // } else {
        //     stockContainer.innerHTML =
        //         '<i class="fas fa-times-circle text-red-600"></i><span class="text-sm text-red-600 font-medium">Out of Stock</span>';
        // }

        // Set description
        document.getElementById("quickViewDescription").textContent =
            product.short_description || "";

        // Setup variants
        this.setupVariants(product);

        // Set view details link
        document.getElementById("quickViewDetails").onclick = () => {
            window.location.href = `/products/${product.slug}`;
        };

        // Reset selections
        this.selectedColor = null;
        this.selectedSize = null;
        document.getElementById("quickViewQuantity").value = 1;
        document.getElementById("variantError").classList.add("hidden");
    }

    setupVariants(product) {
        const variantsSection = document.getElementById("variantsSection");
        const colorSection = document.getElementById("colorSection");
        const sizeSection = document.getElementById("sizeSection");
        const colorOptions = document.getElementById("colorOptions");
        const sizeOptions = document.getElementById("sizeOptions");

        // Check if product has variants
        if (!this.variants || this.variants.length === 0) {
            variantsSection.classList.add("hidden");
            return;
        }

        variantsSection.classList.remove("hidden");

        // Get unique colors and sizes from variants
        const colors = this.getUniqueColors();
        const sizes = this.getUniqueSizes();

        // Setup colors
        if (colors.length > 0) {
            colorSection.classList.remove("hidden");
            colorOptions.innerHTML = "";
            colors.forEach((color) => {
                const btn = document.createElement("button");
                if(color.hex_code) {
                    btn.style.backgroundColor = color.hex_code;
                    btn.className =
                    "color-btn w-11 h-11 rounded-full border-2 border-gray-300 hover:border-brand-blue transition-all p-1 shadow-sm";
                } else {
                    //if no hex code, then use color name inside div
                    btn.style.backgroundColor = "#f0f0f0";
                    btn.textContent = color.name;
                    btn.className =
                        "size-btn min-w-[56px] h-9 px-3 border border-gray-300 rounded-lg text-sm font-medium transition hover:border-brand-blue hover:text-brand-blue hover:bg-brand-blue/5";
                }                
               
                btn.title = color.name;
                btn.dataset.colorId = color.id;
                btn.dataset.colorName = color.name;
                btn.onclick = () => this.selectColor(btn, color);

                colorOptions.appendChild(btn);
            });
        } else {
            colorSection.classList.add("hidden");
        }

        // Setup sizes
        if (sizes.length > 0) {
            sizeSection.classList.remove("hidden");
            sizeOptions.innerHTML = "";
            sizes.forEach((size) => {
                const btn = document.createElement("button");
                btn.className =
                    "size-btn min-w-[56px] h-9 px-3 border border-gray-300 rounded-lg text-sm font-medium transition hover:border-brand-blue hover:text-brand-blue hover:bg-brand-blue/5";
                btn.textContent = size.name;
                btn.dataset.sizeId = size.id;
                btn.dataset.sizeName = size.name;
                btn.onclick = () => this.selectSize(btn, size);

                sizeOptions.appendChild(btn);
            });
        } else {
            sizeSection.classList.add("hidden");
        }
    }

    getUniqueColors() {
        const colorMap = new Map();
        this.variants.forEach((variant) => {
            if (variant.color && !colorMap.has(variant.color.id)) {
                colorMap.set(variant.color.id, variant.color);
            }
        });
        return Array.from(colorMap.values());
    }

    getUniqueSizes() {
        const sizeMap = new Map();
        this.variants.forEach((variant) => {
            if (variant.size && !sizeMap.has(variant.size.id)) {
                sizeMap.set(variant.size.id, variant.size);
            }
        });
        return Array.from(sizeMap.values());
    }

    selectColor(btn, color) {
        // Remove previous selection
        document.querySelectorAll("#colorOptions .color-btn").forEach((b) => {
            b.classList.remove("border-brand-blue");
            b.classList.add("border-gray-300");
        });

        // Add selection
        btn.classList.remove("border-gray-300");
        btn.classList.add("border-brand-blue");

        this.selectedColor = color.id;
        document.getElementById("selectedColorName").textContent = color.name;
        document.getElementById("variantError").classList.add("hidden");

        // Update available sizes based on color
        this.updateAvailableSizes();

        // Update price based on variant
        this.updateVariantPrice();
    }

    selectSize(btn, size) {
        // Remove previous selection
        document.querySelectorAll("#sizeOptions .size-btn").forEach((b) => {
            b.classList.remove(
                "border-brand-blue",
                "bg-brand-blue/5",
                "text-brand-blue",
                "font-semibold",
            );
            b.classList.add("border-gray-300", "text-gray-700", "font-medium");
        });

        // Add selection
        btn.classList.remove("border-gray-300", "text-gray-700", "font-medium");
        btn.classList.add(
            "border-brand-blue",
            "bg-brand-blue/5",
            "text-brand-blue",
            "font-semibold",
        );

        this.selectedSize = size.id;
        document.getElementById("selectedSizeName").textContent = size.name;
        document.getElementById("variantError").classList.add("hidden");

        // Update available colors based on size
        this.updateAvailableColors();

        // Update price based on variant
        this.updateVariantPrice();
    }

    updateVariantPrice() {
        if (
            !this.currentProduct ||
            !this.variants ||
            this.variants.length === 0
        ) {
            return;
        }

        // Get unique colors and sizes
        const colors = this.getUniqueColors();
        const sizes = this.getUniqueSizes();
        const hasColors = colors.length > 0;
        const hasSizes = sizes.length > 0;

        // Find matching variant
        const variant = this.variants.find((v) => {
            const colorMatch = !hasColors || v.color_id === this.selectedColor;
            const sizeMatch = !hasSizes || v.size_id === this.selectedSize;
            return colorMatch && sizeMatch;
        });

        const priceElement = document.getElementById("quickViewPrice");
        const comparePriceElement = document.getElementById(
            "quickViewComparePrice",
        );
        const discountElement = document.getElementById("quickViewDiscount");

        // Use variant price if available and not zero
        if (variant && variant.price && variant.price > 0) {
            priceElement.textContent = `৳${Number(variant.price).toLocaleString()}`;

            if (
                variant.compare_price &&
                variant.compare_price > variant.price
            ) {
                comparePriceElement.textContent = `৳${Number(variant.compare_price).toLocaleString()}`;
                comparePriceElement.classList.remove("hidden");

                const discountPercent = Math.round(
                    ((variant.compare_price - variant.price) /
                        variant.compare_price) *
                        100,
                );
                discountElement.textContent = `-${discountPercent}%`;
                discountElement.classList.remove("hidden");
            } else {
                comparePriceElement.classList.add("hidden");
                discountElement.classList.add("hidden");
            }
        } else {
            // Fallback to base price
            priceElement.textContent = `৳${Number(this.currentProduct.price).toLocaleString()}`;

            if (
                this.currentProduct.compare_price &&
                this.currentProduct.compare_price > this.currentProduct.price
            ) {
                comparePriceElement.textContent = `৳${Number(this.currentProduct.compare_price).toLocaleString()}`;
                comparePriceElement.classList.remove("hidden");

                const discountPercent = Math.round(
                    ((this.currentProduct.compare_price -
                        this.currentProduct.price) /
                        this.currentProduct.compare_price) *
                        100,
                );
                discountElement.textContent = `-${discountPercent}%`;
                discountElement.classList.remove("hidden");
            } else {
                comparePriceElement.classList.add("hidden");
                discountElement.classList.add("hidden");
            }
        }
    }

    updateAvailableSizes() {
        if (!this.selectedColor) return;

        const sizeButtons = document.querySelectorAll("#sizeOptions .size-btn");
        sizeButtons.forEach((btn) => {
            const sizeId = parseInt(btn.dataset.sizeId);
            const variant = this.variants.find(
                (v) =>
                    v.color_id === this.selectedColor && v.size_id === sizeId,
            );

            if (variant && variant.stock_in > 0) {
                btn.disabled = false;
                btn.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                btn.disabled = true;
                btn.classList.add("opacity-50", "cursor-not-allowed");
            }
        });
    }

    updateAvailableColors() {
        if (!this.selectedSize) return;

        const colorButtons = document.querySelectorAll(
            "#colorOptions .color-btn",
        );
        colorButtons.forEach((btn) => {
            const colorId = parseInt(btn.dataset.colorId);
            const variant = this.variants.find(
                (v) =>
                    v.size_id === this.selectedSize && v.color_id === colorId,
            );

            if (variant && variant.stock_in > 0) {
                btn.disabled = false;
                btn.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                btn.disabled = true;
                btn.classList.add("opacity-50", "cursor-not-allowed");
            }
        });
    }

    getSelectedVariant() {
        if (!this.variants || this.variants.length === 0) {
            return null;
        }

        // Check if variant selection is required
        const hasColors = this.getUniqueColors().length > 0;
        const hasSizes = this.getUniqueSizes().length > 0;

        if (hasColors && !this.selectedColor) {
            return "error_color";
        }

        if (hasSizes && !this.selectedSize) {
            return "error_size";
        }

        // Find matching variant
        const variant = this.variants.find((v) => {
            const colorMatch = !hasColors || v.color_id === this.selectedColor;
            const sizeMatch = !hasSizes || v.size_id === this.selectedSize;
            return colorMatch && sizeMatch;
        });

        return variant || "error_not_found";
    }

    closeQuickView() {
        const modal = document.getElementById("productQuickViewModal");
        modal.classList.remove("show");
        this.selectedColor = null;
        this.selectedSize = null;
        this.currentProduct = null;
        this.variants = [];
    }
}

// Quantity management - exposed globally for modal
window.updateQuickViewQuantity = function updateQuickViewQuantity(change) {
    const input = document.getElementById("quickViewQuantity");
    let value = parseInt(input.value) || 1;
    value = Math.max(1, value + change);
    input.value = value;
};

// Add to cart from quick view - exposed globally for modal button
window.addToCartFromQuickView = async function addToCartFromQuickView() {
    const variantManager = window.productVariantManager;

    if (!variantManager.currentProduct) return;

    const selectedVariant = variantManager.getSelectedVariant();
    const quantity =
        parseInt(document.getElementById("quickViewQuantity").value) || 1;

    // Handle variant errors
    if (selectedVariant === "error_color") {
        document.getElementById("variantError").classList.remove("hidden");
        document.getElementById("variantErrorText").textContent =
            "Please select a color";
        return;
    }

    if (selectedVariant === "error_size") {
        document.getElementById("variantError").classList.remove("hidden");
        document.getElementById("variantErrorText").textContent =
            "Please select a size";
        return;
    }

    if (selectedVariant === "error_not_found") {
        document.getElementById("variantError").classList.remove("hidden");
        document.getElementById("variantErrorText").textContent =
            "Selected variant is not available";
        return;
    }

    // Add to cart
    const variantId = selectedVariant ? selectedVariant.id : null;

    if (window.cartManager) {
        const success = await window.cartManager.addToCart(
            variantManager.currentProduct.id,
            variantId,
            quantity,
        );

        if (success) {
            variantManager.closeQuickView();
        }
    }
};

// Close quick view - exposed globally for modal button
window.closeQuickView = function closeQuickView() {
    window.productVariantManager?.closeQuickView();
};

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
    window.productVariantManager = new ProductVariantManager();
});
/**
 * Handle add to cart from product card
 * If product has variants, open quick view modal
 * Otherwise, add directly to cart
 */
window.handleProductCardAddToCart = async function handleProductCardAddToCart(
    productId,
    variantCount,
) {
    if (variantCount > 0) {
        // Product has variants - open quick view modal
        if (window.productVariantManager) {
            window.productVariantManager.openQuickView(productId);
        }
    } else {
        // No variants - add directly to cart
        if (window.cartManager) {
            await window.cartManager.addToCart(productId, null, 1);
        }
    }
};
