# Dynamic Cart Implementation

## Overview

This implementation provides a fully functional shopping cart that works for both logged-in users and guests. Guest carts are stored in sessions, while authenticated user carts are stored in the database.

## Features

- ✅ Add to cart (with/without login)
- ✅ Update cart item quantities
- ✅ Remove items from cart
- ✅ Real-time cart updates
- ✅ Cart persistence (session for guests, database for users)
- ✅ Free shipping progress indicator
- ✅ Dynamic cart drawer
- ✅ Product variant support (size, color)

## Files Modified/Created

### 1. CartController.php

**Location:** `app/Http/Controllers/CartController.php`
**Purpose:** Handles all cart operations via API endpoints

**Methods:**

- `getCart()` - Get cart data with items
- `addToCart()` - Add product to cart
- `updateQuantity()` - Update item quantity
- `removeItem()` - Remove item from cart
- `clearCart()` - Clear entire cart

### 2. API Routes

**Location:** `routes/api.php`
**Endpoints:**

- `GET /api/cart` - Get cart
- `POST /api/cart/add` - Add to cart
- `PUT /api/cart/update/{itemId}` - Update quantity
- `DELETE /api/cart/remove/{itemId}` - Remove item
- `DELETE /api/cart/clear` - Clear cart

### 3. Cart JavaScript

**Location:** `resources/js/cart.js`
**Purpose:** Frontend cart management class

**Main Class:** `CartManager`
**Methods:**

- `loadCart()` - Load cart from API
- `addToCart(productId, variantId, quantity)` - Add product
- `updateQuantity(itemId, quantity)` - Update quantity
- `removeItem(itemId)` - Remove item
- `updateCartUI(cart)` - Update cart display

### 4. Cart Drawer

**Location:** `resources/views/partials/cart-drawer.blade.php`
**Purpose:** Dynamic cart sidebar component

## How to Use

### Adding Product to Cart (Simple)

Add these data attributes to any button:

```html
<button data-add-to-cart data-product-id="123" data-quantity="1">
    Add to Cart
</button>
```

### Adding Product with Variant

```html
<button
    data-add-to-cart
    data-product-id="123"
    data-variant-id="456"
    data-quantity="2"
>
    Add to Cart
</button>
```

### Using JavaScript Directly

```javascript
// Add to cart
cartManager.addToCart(productId, variantId, quantity);

// Update quantity
cartManager.updateQuantity(itemId, newQuantity);

// Remove item
cartManager.removeItem(itemId);

// Load cart
cartManager.loadCart();
```

### Opening Cart Drawer

```javascript
openCartDrawer();
```

## Setup Instructions

### 1. Compile Assets

```bash
npm run dev
```

Or for production:

```bash
npm run build
```

### 2. Database Migration

Make sure your cart and cart_items tables are migrated:

```bash
php artisan migrate
```

### 3. Test the Implementation

1. Visit any product page
2. Click "Add to Cart" button
3. Cart drawer should open showing the product
4. Try updating quantities
5. Try removing items

## Cart Session Handling

### For Guest Users

- Cart is stored in session
- Session ID is used to identify cart
- Cart persists across page loads
- Session expires after configured time

### For Logged-In Users

- Cart is stored in database linked to user_id
- Cart persists permanently
- Cart syncs across devices
- Previous session cart can be merged on login (optional)

## API Response Format

### Get Cart Response

```json
{
    "success": true,
    "cart": {
        "id": 1,
        "items": [
            {
                "id": 1,
                "product_id": 123,
                "product_name": "Product Name",
                "product_image": "image_url",
                "variant_id": 456,
                "size": "M",
                "color": "Blue",
                "quantity": 2,
                "unit_price": 100.0,
                "total_price": 200.0
            }
        ],
        "subtotal": 200.0,
        "items_count": 2,
        "total_items": 1
    }
}
```

### Add to Cart Response

```json
{
    "success": true,
    "message": "Product added to cart successfully",
    "cart": {
        "items_count": 3,
        "subtotal": 300.0
    }
}
```

## Customization

### Change Free Shipping Threshold

Edit in `resources/js/cart.js`:

```javascript
const freeShippingThreshold = 1500; // Change this value
```

### Customize Cart Item Template

Edit the `getCartItemHTML()` method in `resources/js/cart.js`

### Add Custom Notifications

Replace the `showNotification()` method with your toast/notification system

## Troubleshooting

### Cart not updating

1. Check browser console for errors
2. Verify CSRF token is present: `<meta name="csrf-token" content="{{ csrf_token() }}">`
3. Make sure assets are compiled: `npm run dev`
4. Check API routes are accessible: Visit `/api/cart` in browser

### Items not showing in drawer

1. Open cart drawer
2. Check browser console for API errors
3. Verify database has cart_items table
4. Check if cart session/user_id is set correctly

### Session cart not persisting

1. Check `.env` SESSION_DRIVER setting
2. Clear cache: `php artisan cache:clear`
3. Clear session: `php artisan session:flush`

## Next Steps / Enhancements

1. **Cart Merge on Login** - Merge guest cart with user cart on authentication
2. **Product Stock Validation** - Check stock before adding to cart
3. **Toast Notifications** - Implement better notification system
4. **Coupon System** - Add coupon code validation
5. **Cart Summary Widget** - Add mini cart widget to header
6. **Cart Analytics** - Track add-to-cart events
7. **Wishlist Integration** - Move wishlist items to cart
8. **Abandoned Cart Recovery** - Email reminders for incomplete orders

## Support

For issues or questions, refer to:

- Laravel Session Documentation
- Laravel API Resources
- Fetch API Documentation
