# Toast Notification System

A beautiful, modern toast notification system for Spinner Fashion that works with both Laravel backend and JavaScript frontend.

## Features

- ✅ 4 notification types: Success, Error, Warning, Info
- ✅ Auto-dismiss after 5 seconds (customizable)
- ✅ Manual close button
- ✅ Smooth animations
- ✅ Laravel flash message integration
- ✅ Multiple toasts support
- ✅ Mobile responsive

## Usage

### From JavaScript (Frontend)

```javascript
// Basic usage
showToast("success", "This is a success message");
showToast("error", "This is an error message");
showToast("warning", "This is a warning message");
showToast("info", "This is an info message");

// Convenience methods
showSuccess("Product added successfully!");
showError("Failed to add product");
showWarning("Please select a size");
showInfo("Free shipping on orders over ৳1,500");

// Custom duration (in milliseconds)
showSuccess("Item added!", 3000); // Show for 3 seconds
showError("Something went wrong", 0); // Never auto-dismiss

// Using toast manager directly
window.toastManager.success("Success message");
window.toastManager.error("Error message");
window.toastManager.warning("Warning message");
window.toastManager.info("Info message");
```

### From Laravel (Backend)

```php
// Using helper functions
toast_success('Product created successfully!');
toast_error('Failed to create product');
toast_warning('Stock is running low');
toast_info('New feature available');

// In controllers
public function store(Request $request)
{
    // ... your logic

    toast_success('Product added to cart successfully!');
    return redirect()->back();
}

// With validation errors (automatically shown as toast)
$validator = Validator::make($request->all(), [
    'name' => 'required',
    'email' => 'required|email',
]);

if ($validator->fails()) {
    // Errors automatically shown as red toasts
    return redirect()->back()->withErrors($validator)->withInput();
}

// Traditional session flash (also works)
session()->flash('success', 'Operation completed!');
session()->flash('error', 'Something went wrong!');
session()->flash('warning', 'Please be careful!');
session()->flash('info', 'Did you know...');

// Or with redirect
return redirect()->route('home')
    ->with('success', 'Welcome back!');
```

### Examples in Real Use Cases

#### Cart Controller

```php
public function addToCart(Request $request)
{
    try {
        // Add item to cart logic

        toast_success('Product added to cart successfully');
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully'
        ]);
    } catch (\Exception $e) {
        toast_error('Failed to add product to cart');
        return response()->json([
            'success' => false,
            'message' => 'Failed to add product to cart'
        ], 500);
    }
}
```

#### Form Submission (JavaScript)

```javascript
document.getElementById("contactForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    try {
        const response = await fetch("/api/contact", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },
            body: JSON.stringify(formData),
        });

        const data = await response.json();

        if (data.success) {
            showSuccess("Message sent successfully!");
        } else {
            showError(data.message || "Failed to send message");
        }
    } catch (error) {
        showError("Network error. Please try again.");
    }
});
```

## Customization

### Change Duration

```javascript
// Show for 10 seconds instead of default 5
showSuccess("Message", 10000);

// Never auto-dismiss
showError("Important error", 0);
```

### Styling

The toast uses Tailwind CSS classes. To customize appearance, edit the `createToast` method in `/resources/views/partials/toast.blade.php`

### Position

Toasts appear in the top-right corner by default. To change position, modify the `#toastContainer` classes:

- Top Right: `top-4 right-4`
- Top Left: `top-4 left-4`
- Bottom Right: `bottom-4 right-4`
- Bottom Left: `bottom-4 left-4`
- Top Center: `top-4 left-1/2 -translate-x-1/2`

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## Dependencies

- Tailwind CSS (for styling)
- Font Awesome (for icons)

## Files

- `/resources/views/partials/toast.blade.php` - Toast component
- `/app/helpers.php` - Laravel helper functions
- `/resources/js/cart.js` - Updated to use toast system
