class ProductVariantManager {
    constructor() {
        this.selectedColor = null;
        this.selectedSize = null;
        this.currentProduct = null;
        this.variants = [];

        this.init();
    }

    init() {
        document.querySelectorAll(".quick-view-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                const productId = e.currentTarget.dataset.productId;
                if (productId) this.openQuickView(productId);
            });
        });

        document.getElementById("productQuickViewModal")
            ?.addEventListener("click", (e) => {
                if (e.target.id === "productQuickViewModal") {
                    this.closeQuickView();
                }
            });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.closeQuickView();
            }
        });
    }

    // =========================
    // OPEN QUICK VIEW
    // =========================
    async openQuickView(productId) {
        try {
            const modal = document.getElementById("productQuickViewModal");

            modal.classList.remove("hidden");
            modal.classList.add("flex");

            const res = await fetch(`/products/${productId}/quickview`, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!res.ok) throw new Error("Failed to load product");

            const data = await res.json();

            this.currentProduct = data.product;
            this.variants = data.product.variants || [];
            this.buildVariantMap();

            this.populateQuickView(data.product);
        } catch (err) {
            console.error(err);
            this.closeQuickView();
        }
    }

    // =========================
    // POPULATE UI
    // =========================
    populateQuickView(product) {

        document.getElementById("quickViewName").textContent = product.name;
        document.getElementById("quickViewBrand").textContent = formatBrandCategory(product);

        const desc = document.getElementById('quickViewDescription');

        if (product.short_description) {
            desc.innerHTML = product.short_description;
            desc.classList.remove('hidden');
        } else {
            desc.innerHTML = '';
            desc.classList.add('hidden');
        }

        document.getElementById("quickViewImage").src =
            product.image || product.thumbnail || "/assets/images/default.png";

        this.setPrice(product.price, product.compare_price);
        this.setStock(product.stock ?? 0);


        if (this.variants.length > 0) {
            this.renderVariants();
        } else {
            document.getElementById('variantsSection').classList.add('hidden');
        }

        const nameLink = document.getElementById("quickViewNameLink");
        if (nameLink) {
            nameLink.href = `/products/${product.slug}`;
        }

        this.resetSelections();
    }

    // =========================
    // PRICE
    // =========================
    setPrice(price, comparePrice) {
        const priceEl = document.getElementById("quickViewPrice");
        const compareEl = document.getElementById("quickViewComparePrice");
        const discountEl = document.getElementById("quickViewDiscount");

        priceEl.textContent = `৳${Number(price).toLocaleString()}`;

        if (comparePrice && comparePrice > price) {
            compareEl.textContent = `৳${Number(comparePrice).toLocaleString()}`;
            compareEl.classList.remove("hidden");

            const discount = Math.round(((comparePrice - price) / comparePrice) * 100);
            discountEl.textContent = `-${discount}%`;
            discountEl.classList.remove("hidden");
        } else {
            compareEl.classList.add("hidden");
            discountEl.classList.add("hidden");
        }
    }

    // =========================
    // STOCK
    // =========================
    setStock(stock) {
        const el = document.getElementById("quickViewStock");

        const hasColors = this.getColors().length > 0;
        const hasSizes = this.getSizes().length > 0;

        const colorSelected = this.selectedColor;
        const sizeSelected = this.selectedSize;

        if (hasColors && !hasSizes && !colorSelected) {
            el.innerHTML = `
            <span class="text-gray-500 font-medium">
                Please select color
            </span>
        `;
            document.getElementById("quickViewQuantity").max = 1;
            return;
        }

        if (!hasColors && hasSizes && !sizeSelected) {
            el.innerHTML = `
            <span class="text-gray-500 font-medium">
                Please select size
            </span>
        `;
            document.getElementById("quickViewQuantity").max = 1;
            return;
        }

        if (hasColors && hasSizes && (!colorSelected || !sizeSelected)) {
            el.innerHTML = `
            <span class="text-gray-500 font-medium">
                Please select ${!colorSelected ? "color" : ""} 
                ${(!colorSelected && !sizeSelected) ? "and" : ""} 
                ${!sizeSelected ? "size" : ""}
            </span>
        `;
            document.getElementById("quickViewQuantity").max = 1;
            return;
        }

        if (stock <= 0) {
            el.innerHTML = `<span class="text-red-600 font-medium">Out of stock</span>`;
        } else if (stock <= 5) {
            el.innerHTML = `<span class="text-orange-500 font-medium">Only ${stock} left</span>`;
        } else {
            el.innerHTML = `<span class="text-green-600 font-medium">${stock} in stock</span>`;
        }

        document.getElementById("quickViewQuantity").max = stock || 1;
    }

    // =========================
    // VARIANTS
    // =========================

    buildVariantMap() {
        this.variantMap = new Map();

        this.variants.forEach(v => {
            const key = `${v.color_id ?? 'x'}_${v.size_id ?? 'x'}`;
            this.variantMap.set(key, v);
        });
    }

    renderVariants() {
        const colors = this.getColors();
        const sizes = this.getSizes();

        const colorBox = document.getElementById("colorOptions");
        const sizeBox = document.getElementById("sizeOptions");

        document.getElementById("variantsSection").classList.remove("hidden");

        // COLORS
        if (colors.length > 0) {
            document.getElementById("colorSection").classList.remove("hidden");
            colorBox.innerHTML = "";

            colors.forEach((c) => {

                const btn = document.createElement("button");

                const hasValidVariant = this.variants.some(v =>
                    v.color_id == c.id &&
                    (!this.selectedSize || v.size_id == this.selectedSize)
                );

                const inStock = this.variants.some(v =>
                    v.color_id == c.id &&
                    (!this.selectedSize || v.size_id == this.selectedSize) &&
                    (v.stock) > 0
                );

                btn.dataset.id = c.id;
                btn.dataset.name = c.name;
                btn.title = c.name;

                if (c.hex_code) {

                    btn.className =
                        "color-btn w-11 h-8 rounded-md border-2 transition p-0.5 shadow-sm";

                    btn.style.backgroundColor = c.hex_code;

                    if (!hasValidVariant || !inStock) {
                        btn.disabled = true;

                        btn.classList.add(
                            "opacity-30",
                            "cursor-not-allowed",
                            "border-gray-200"
                        );
                    } else {
                        btn.classList.add(
                            "border-gray-300",
                            "hover:border-primary"
                        );
                    }

                } else {

                    btn.className =
                        "color-btn w-11 h-8 rounded-md border-2 text-[9px] font-semibold flex items-center justify-center uppercase transition shadow-sm";

                    btn.textContent = (c.name || "")
                        .substring(0, 3)
                        .toUpperCase();

                    if (!hasValidVariant || !inStock) {
                        btn.disabled = true;

                        btn.classList.add(
                            "bg-gray-100",
                            "text-gray-400",
                            "border-gray-200",
                            "cursor-not-allowed",
                            "opacity-50"
                        );
                    } else {
                        btn.classList.add(
                            "bg-gray-50",
                            "text-gray-700",
                            "border-gray-300",
                            "hover:border-primary",
                            "hover:text-primary"
                        );
                    }
                }

                btn.onclick = () => {
                    if (btn.disabled) return;
                    this.selectColor(c, btn);
                };

                colorBox.appendChild(btn);
            });
        }

        // SIZES
        if (sizes.length > 0) {
            document.getElementById("sizeSection").classList.remove("hidden");
            sizeBox.innerHTML = "";

            sizes.forEach((s) => {
                const btn = document.createElement("button");

                const hasValidVariant = this.variants.some(v =>
                    v.size_id == s.id &&
                    (!this.selectedColor || v.color_id == this.selectedColor)
                );

                const inStock = this.variants.some(v =>
                    v.size_id == s.id &&
                    (!this.selectedColor || v.color_id == this.selectedColor) &&
                    (v.stock) > 0
                );

                btn.className =
                    "product-size-btn w-11 h-8 border rounded-md text-xs font-medium transition flex items-center justify-center";

                btn.dataset.id = s.id;
                btn.textContent = s.name;

                if (!hasValidVariant || !inStock) {
                    btn.disabled = true;
                    btn.classList.add("opacity-30", "cursor-not-allowed", "border-gray-200", "text-gray-400");
                } else {
                    btn.classList.add("border-gray-300", "text-gray-700", "hover:border-primary");
                }

                btn.onclick = () => {
                    if (btn.disabled) return;
                    this.selectSize(s, btn);
                };

                sizeBox.appendChild(btn);
            });
        }
    }

    getColors() {
        const map = new Map();
        this.variants.forEach(v => {
            if (v.color && !map.has(v.color.id)) {
                map.set(v.color.id, v.color);
            }
        });
        return [...map.values()];
    }

    getSizes() {
        const map = new Map();
        this.variants.forEach(v => {
            if (v.size && !map.has(v.size.id)) {
                map.set(v.size.id, v.size);
            }
        });
        return [...map.values()];
    }

    selectColor(color, btn) {
        document.querySelectorAll("#colorOptions button")
            .forEach(b => b.classList.remove("border-primary"));

        btn.classList.add("border-primary");
        this.selectedColor = color.id;

        const label = document.getElementById("selectedColorName");
        if (label) label.textContent = color.name;

        this.updateVariant();
    }

    selectSize(size, btn) {
        document.querySelectorAll("#sizeOptions button")
            .forEach(b => b.classList.remove("border-primary"));

        btn.classList.add("border-primary");
        this.selectedSize = size.id;

        const label = document.getElementById("selectedSizeName");
        if (label) label.textContent = size.name;

        this.updateVariant();
    }

    updateVariant() {
        const variant = this.variants.find(v => {
            return (!this.selectedColor || v.color_id == this.selectedColor) &&
                (!this.selectedSize || v.size_id == this.selectedSize);
        });

        if (!variant) return;

        this.setPrice(variant.price, variant.compare_price);
        this.setStock(variant.stock);
    }

    // =========================
    // QUANTITY
    // =========================
    updateQty(change) {
        const input = document.getElementById("quickViewQuantity");
        let val = (parseInt(input.value) || 1) + change;
        val = Math.max(1, Math.min(val, parseInt(input.max || 999)));
        input.value = val;
    }

    // =========================
    // RESET
    // =========================
    resetSelections() {
        this.selectedColor = null;
        this.selectedSize = null;

        document.getElementById("quickViewQuantity").value = 1;

        const colorLabel = document.getElementById("selectedColorName");
        if (colorLabel) colorLabel.textContent = "";

        const sizeLabel = document.getElementById("selectedSizeName");
        if (sizeLabel) sizeLabel.textContent = "";
    }

    // =========================
    // CLOSE
    // =========================
    closeQuickView() {
        const modal = document.getElementById("productQuickViewModal");

        modal.classList.add("hidden");
        modal.classList.remove("flex");

        this.resetSelections();

        this.currentProduct = null;
        this.variants = [];
    }
}

function formatBrandCategory(product) {
    const brand = product.brand?.name;
    const category = product.category?.name;

    return [brand, category].filter(Boolean).join(" • ");
}

// =========================
// GLOBAL INIT
// =========================
window.productVariantManager = new ProductVariantManager();

// =========================
// GLOBAL WRAPPERS (ONLY UI HOOKS)
// =========================
window.closeQuickView = () => window.productVariantManager.closeQuickView();
window.updateQuickViewQuantity = (c) =>
    window.productVariantManager.updateQty(c);

window.addToCartFromQuickView = async () => {
    const m = window.productVariantManager;

    if (!m.currentProduct) return;

    const hasColors = m.getColors().length > 0;
    const hasSizes = m.getSizes().length > 0;

    // Validate color
    if (hasColors && !m.selectedColor) {
        if (window.showError) {
            window.showError("Please select a color");
        }
        return;
    }

    // Validate size
    if (hasSizes && !m.selectedSize) {
        if (window.showError) {
            window.showError("Please select a size");
        }
        return;
    }

    // Find selected variant
    const variant = m.variants.find(v =>
        (!hasColors || v.color_id == m.selectedColor) &&
        (!hasSizes || v.size_id == m.selectedSize)
    );

    // Variant product but not found
    if ((hasColors || hasSizes) && !variant) {
        if (window.showError) {
            window.showError("Selected variant is not available");
        }
        return;
    }

    const qty = parseInt(document.getElementById("quickViewQuantity").value) || 1;

    if (!window.cartManager) return;

    const success = await window.cartManager.addToCart(
        m.currentProduct.id,
        variant ? variant.id : null,
        qty
    );

    if (success) {
        m.closeQuickView();

        if (window.openCartDrawer) {
            window.openCartDrawer();
        }
    }
};

window.handleProductCardAddToCart = (productId, variantCount) => {
    if (variantCount > 0) {
        window.productVariantManager.openQuickView(productId);
    } else {
        window.cartManager?.addToCart(productId, null, 1);
    }
};