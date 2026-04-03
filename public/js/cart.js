class CartManager {
    constructor() {
        this.apiUrl = "/cart";
        this.csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        this.init();
    }

    init() {
        this.loadCart();
        this.setupAddToCartListeners();
    }

    getHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": this.csrfToken,
            "X-Requested-With": "XMLHttpRequest",
        };
    }

    async loadCart() {
        try {
            const response = await fetch(this.apiUrl, {
                method: "GET",
                headers: this.getHeaders(),
                credentials: "same-origin",
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartUI(data.cart);
            }
        } catch (error) {
            console.error("Failed to load cart:", error);
        }
    }

    async addToCart(productId, variantId = null, quantity = 1) {
        try {
            const response = await fetch(`${this.apiUrl}/add`, {
                method: "POST",
                headers: this.getHeaders(),
                credentials: "same-origin",
                body: JSON.stringify({
                    product_id: productId,
                    product_variant_id: variantId,
                    quantity: quantity,
                }),
            });

            const data = await response.json();

            if (data.success) {
                if (window.showSuccess) {
                    window.showSuccess(
                        data.message || "Product added to cart successfully",
                    );
                }
                this.updateCartCount(data.cart.items_count);
                this.loadCart(); // Reload full cart
                return true;
            } else {
                if (window.showError) {
                    window.showError(
                        data.message || "Failed to add item to cart",
                    );
                }
                return false;
            }
        } catch (error) {
            console.error("Failed to add to cart:", error);
            if (window.showError) {
                window.showError("Failed to add item to cart");
            }
            return false;
        }
    }

    async updateQuantity(itemId, quantity) {
        try {
            const response = await fetch(`${this.apiUrl}/update/${itemId}`, {
                method: "PUT",
                headers: this.getHeaders(),
                credentials: "same-origin",
                body: JSON.stringify({ quantity: quantity }),
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartCount(data.cart.items_count);
                this.loadCart(); // Reload full cart
                return true;
            } else {
                if (window.showError) {
                    window.showError(data.message || "Failed to update cart");
                }
                return false;
            }
        } catch (error) {
            console.error("Failed to update quantity:", error);
            if (window.showError) {
                window.showError("Failed to update cart");
            }
            return false;
        }
    }

    async removeItem(itemId) {
        try {
            const response = await fetch(`${this.apiUrl}/remove/${itemId}`, {
                method: "DELETE",
                headers: this.getHeaders(),
                credentials: "same-origin",
            });

            const data = await response.json();

            if (data.success) {
                if (window.showSuccess) {
                    window.showSuccess(
                        data.message || "Item removed from cart",
                    );
                }
                this.updateCartCount(data.cart.items_count);
                this.loadCart(); // Reload full cart
                return true;
            } else {
                if (window.showError) {
                    window.showError(data.message || "Failed to remove item");
                }
                return false;
            }
        } catch (error) {
            console.error("Failed to remove item:", error);
            if (window.showError) {
                window.showError("Failed to remove item");
            }
            return false;
        }
    }

    async clearCart() {
        try {
            const response = await fetch(`${this.apiUrl}/clear`, {
                method: "DELETE",
                headers: this.getHeaders(),
                credentials: "same-origin",
            });

            const data = await response.json();

            if (data.success) {
                if (window.showSuccess) {
                    window.showSuccess(
                        data.message || "Cart cleared successfully",
                    );
                }
                this.loadCart();
                return true;
            }
        } catch (error) {
            console.error("Failed to clear cart:", error);
            if (window.showError) {
                window.showError("Failed to clear cart");
            }
            return false;
        }
    }

    updateCartUI(cart) {
        this.updateCartCount(cart.items_count);

        const container = document.getElementById("cartItemsContainer");
        if (!container) return;

        if (cart.items.length === 0) {
            container.innerHTML = this.getEmptyCartHTML();
            this.hideCartFooter();
        } else {
            container.innerHTML = cart.items
                .map((item) => this.getCartItemHTML(item))
                .join("");
            this.showCartFooter();
        }

        const subtotalElement = document.getElementById("cartSubtotal");
        if (subtotalElement) {
            subtotalElement.textContent = `৳${cart.subtotal.toLocaleString()}`;
        }

        const shippingElement = document.getElementById("cartShipping");
        if (shippingElement) {
            if (cart.shipping === 0) {
                shippingElement.innerHTML =
                    '<span class="text-green-600">Free</span>';
            } else {
                shippingElement.textContent = `৳${cart.shipping.toLocaleString()}`;
            }
        }

        const discountElement = document.getElementById("cartDiscount");
        if (discountElement) {
            discountElement.textContent = `-৳${cart.discount.toLocaleString()}`;
        }

        const totalElement = document.getElementById("cartTotal");
        if (totalElement) {
            totalElement.textContent = `৳${cart.total.toLocaleString()}`;
        }

        const headerCartTotal = document.getElementById("headerCartTotal");
        if (headerCartTotal) {
            headerCartTotal.textContent = `৳${cart.total.toLocaleString()}`;
        }

        this.updateShippingProgress(cart.subtotal);
    }

    showCartFooter() {
        const footer = document.getElementById("cartFooter");
        const coupon = document.getElementById("couponSection");
        if (footer) footer.classList.remove("hidden");
        if (coupon) coupon.classList.remove("hidden");
    }

    hideCartFooter() {
        const footer = document.getElementById("cartFooter");
        const coupon = document.getElementById("couponSection");
        if (footer) footer.classList.add("hidden");
        if (coupon) coupon.classList.add("hidden");
    }

    updateCartCount(count) {
        const badges = document.querySelectorAll(
            "#cartCountBadge, #cartItemCount, #headerCartCount",
        );
        badges.forEach((badge) => {
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    updateShippingProgress(subtotal) {
        const freeShippingThreshold = 1500; // You can adjust this
        const remaining = Math.max(0, freeShippingThreshold - subtotal);
        const percentage = Math.min(
            100,
            (subtotal / freeShippingThreshold) * 100,
        );

        const remainingElement = document.getElementById("shippingRemaining");
        const progressBar = document.getElementById("shippingProgressBar");

        if (remainingElement) {
            if (remaining > 0) {
                remainingElement.innerHTML = `Add <span class="font-semibold text-green-600">৳${remaining.toLocaleString()}</span> more for free shipping!`;
            } else {
                remainingElement.innerHTML = `<span class="font-semibold text-green-600">🎉 You qualify for free shipping!</span>`;
            }
        }

        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }
    }

    getCartItemHTML(item) {
        return `
            <div class="flex gap-4 p-3 bg-gray-50 rounded-2xl relative group" data-item-id="${item.id}">
                <div class="w-20 h-24 flex-shrink-0 rounded-xl overflow-hidden bg-white">
                    <img src="${item.product_image}" alt="${item.product_name}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 line-clamp-2 pr-6">${item.product_name}</h4>
                    <p class="text-xs text-gray-500 mt-1">Size: ${item.size} | Color: ${item.color}</p>
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex items-center border border-gray-200 rounded-lg bg-white overflow-hidden">
                            <button onclick="window.cartManager.decreaseQuantity(${item.id}, ${item.quantity})" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-12 text-center font-semibold text-gray-700">${item.quantity}</span>
                            <button onclick="window.cartManager.increaseQuantity(${item.id}, ${item.quantity})" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        <span class="text-brand-blue font-bold">৳${item.total_price.toLocaleString()}</span>
                    </div>
                </div>
                <button onclick="window.cartManager.removeItem(${item.id})" class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white shadow flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition opacity-0 group-hover:opacity-100">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
        `;
    }

    getEmptyCartHTML() {
        return `
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-shopping-bag text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Your cart is empty</h3>
                <p class="text-sm text-gray-500 mb-6">Add some products to get started!</p>
                <button onclick="closeCartDrawer()" class="px-6 py-2 bg-brand-blue text-white rounded-lg hover:bg-blue-600 transition">
                    Continue Shopping
                </button>
            </div>
        `;
    }

    setupAddToCartListeners() {
        document.addEventListener("click", (e) => {
            const button = e.target.closest("[data-add-to-cart]");
            if (button) {
                e.preventDefault();
                const productId = button.dataset.productId;
                const variantId = button.dataset.variantId || null;
                const quantity = parseInt(button.dataset.quantity || 1);

                this.addToCart(productId, variantId, quantity);
            }
        });
    }

    async increaseQuantity(itemId, currentQuantity) {
        await this.updateQuantity(itemId, currentQuantity + 1);
    }

    async decreaseQuantity(itemId, currentQuantity) {
        if (currentQuantity > 1) {
            await this.updateQuantity(itemId, currentQuantity - 1);
        } else {
            await this.removeItem(itemId);
        }
    }
}

// Initialize CartManager and expose globally
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        window.cartManager = new CartManager();
    });
} else {
    window.cartManager = new CartManager();
}

// Expose cart functions globally
window.openCartDrawer = function openCartDrawer() {
    const drawer = document.getElementById("cartDrawer");
    const overlay = document.getElementById("cartOverlay");

    if (drawer && overlay) {
        if (window.cartManager) {
            window.cartManager.loadCart();
        }

        drawer.classList.remove("translate-x-full");
        overlay.classList.remove("invisible", "opacity-0");
        document.body.style.overflow = "hidden";
    }
};

window.closeCartDrawer = function closeCartDrawer() {
    const drawer = document.getElementById("cartDrawer");
    const overlay = document.getElementById("cartOverlay");

    if (drawer && overlay) {
        drawer.classList.add("translate-x-full");
        overlay.classList.add("invisible", "opacity-0");
        document.body.style.overflow = "";
    }
};
