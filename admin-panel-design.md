# Admin Panel

## Overview

A modern, beautiful, and fully responsive admin dashboard for managing the e-commerce store.

> **Design Update Note:** This revision only updates the visual design system — colors, typography, and icons. All existing logic, routes, controllers, data flow, and the responsive layout structure remain untouched.

## Features

### 🎨 Beautiful Design

- **Modern UI**: Clean, professional design with Tailwind CSS
- **Responsive Layout**: Works perfectly on desktop, tablet, and mobile devices (unchanged from current implementation)
- **Smooth Animations**: Elegant transitions and hover effects
- **Color-coded Elements**: Refreshed, accessible color palette for different sections
- **Consistent Iconography**: Clean, uniform stroke-based icon set across the whole panel

### 📊 Dashboard Components

#### Statistics Cards

- **Total Revenue**: Monthly revenue with percentage growth indicator
- **Total Orders**: Order count with comparison to previous month
- **Total Customers**: Customer count with growth tracking
- **Pending Orders**: Real-time pending orders requiring attention

#### Revenue Chart

- Interactive line chart showing monthly revenue trends
- 6-month historical data visualization
- Responsive and mobile-friendly
- Built with Chart.js (restyled with new palette, gradients, and tooltips)

#### Recent Orders Table

- Real-time order list with status indicators
- Customer information with avatars
- Quick action buttons
- Sortable and filterable

#### Top Products Widget

- Best-selling products with images
- Sales count and revenue tracking
- Quick navigation to product details

#### Quick Actions Panel

- Add new products
- View all orders
- Create discount coupons
- One-click shortcuts

#### Low Stock Alert

- Visual warning for products running low
- Product count display
- Direct link to inventory management

### 🎯 Navigation Features

#### Sidebar Navigation

- **Dashboard**: Main overview page
- **Sales Section**: Orders management
- **Catalog Section**: Products, Categories, Reviews
- **Customers**: User management
- **Marketing**: Coupons, Banners
- **System**: Settings

#### Header Features

- Global search functionality
- Notifications bell with badge
- Quick link to view live site
- User dropdown menu with profile and logout

### 🔐 Authentication

- Protected admin routes
- User profile display
- Secure logout functionality

## File Structure

```
resources/views/admin/
├── layouts/
│   └── app.blade.php            # Main admin layout (fonts, sidebar, header)
├── dashboard.blade.php          # Dashboard page
├── orders/
│   └── index.blade.php          # Orders management (placeholder)
├── products/
│   ├── index.blade.php          # Products list (placeholder)
│   └── create.blade.php         # Add new product (placeholder)
├── categories/
│   └── index.blade.php          # Categories management (placeholder)
├── reviews/
│   └── index.blade.php          # Reviews moderation (placeholder)
├── customers/
│   └── index.blade.php          # Customer management (placeholder)
├── coupons/
│   └── index.blade.php          # Coupons system (placeholder)
├── banners/
│   └── index.blade.php          # Banner management (placeholder)
└── settings/
    └── index.blade.php          # System settings (placeholder)

app/Http/Controllers/Admin/
└── DashboardController.php      # Dashboard logic
```

> No files are renamed, moved, or restructured, and no new routes/controllers are added. Every existing and placeholder page under `resources/views/admin/` — Orders, Products, Categories, Reviews, Customers, Coupons, Banners, Settings — inherits the same layout (`layouts/app.blade.php`), so updating fonts, colors, and icons there automatically restyles all of them consistently. Only the CSS classes, font references, and icon markup inside these Blade files change.

## Access

Visit the admin dashboard at: `/admin/dashboard`

## Technologies Used

- **Laravel Blade**: Template engine
- **Tailwind CSS v4**: Styling framework (CSS-first config via `@theme`, no `tailwind.config.js` required)
- **Alpine.js**: Lightweight JavaScript framework
- **Chart.js**: Data visualization
- **Lucide Icons**: Modern, clean, consistent stroke-based icon library (replaces Font Awesome)
- **Plus Jakarta Sans**: Headings & UI labels — modern, geometric, distinctive
- **Inter**: Body text & tables — highly legible at small sizes

## 🎨 New Design System

### Typography

| Use Case | Font | Weight | Notes |
|---|---|---|---|
| Headings / Page Titles | Plus Jakarta Sans | 600–700 | Distinctive, modern, great for dashboards |
| Sidebar / Nav Labels | Plus Jakarta Sans | 500 | Slightly tighter letter spacing |
| Body / Table Text | Inter | 400–500 | Excellent readability at 13–14px |
| Numbers / Stats | Inter | 600–700 (tabular-nums) | Keeps stat cards aligned |

**CDN import (add to `<head>` in `app.blade.php`):**
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
```

```css
body { font-family: 'Inter', sans-serif; }
.font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
```

### Icons — Lucide

Swap Font Awesome (`<i class="fa-...">`) for Lucide (`<i data-lucide="...">`), a cleaner stroke-based set that pairs better with a modern flat/soft-UI look.

```html
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>lucide.createIcons();</script>
```

| Section | Old (Font Awesome) | New (Lucide) |
|---|---|---|
| Dashboard | `fa-home` | `layout-dashboard` |
| Orders | `fa-shopping-cart` | `shopping-cart` |
| Products | `fa-box` | `package` |
| Categories | `fa-tags` | `tag` |
| Reviews | `fa-star` | `star` |
| Customers | `fa-users` | `users` |
| Coupons | `fa-ticket` | `ticket-percent` |
| Banners | `fa-image` | `image` |
| Settings | `fa-cog` | `settings` |
| Notifications | `fa-bell` | `bell` |
| Search | `fa-search` | `search` |
| Logout | `fa-sign-out` | `log-out` |
| Revenue (up) | `fa-arrow-up` | `trending-up` |
| Low Stock | `fa-exclamation-triangle` | `alert-triangle` |

### Color Palette

A refreshed, more contemporary palette — Indigo as the primary brand color (replacing plain blue), softer neutrals, and slightly desaturated status colors for a calmer, premium feel while staying fully accessible (WCAG AA on white backgrounds).

| Role | Color | Hex | Tailwind Equivalent |
|---|---|---|---|
| **Primary (Brand)** | Indigo | `#6366f1` | `indigo-500` |
| **Primary Dark** (hover/active) | Indigo Deep | `#4f46e5` | `indigo-600` |
| **Success** | Emerald | `#10b981` | `emerald-500` |
| **Warning** | Amber | `#f59e0b` | `amber-500` |
| **Danger** | Rose | `#f43f5e` | `rose-500` |
| **Info / Accent** | Sky | `#0ea5e9` | `sky-500` |
| **Secondary Accent** | Violet | `#8b5cf6` | `violet-500` |
| **Sidebar Background** | Slate 900 | `#0f172a` | `slate-900` |
| **Sidebar Active Item** | Indigo (10% tint on slate) | `#6366f1` @ 15% opacity | `indigo-500/15` |
| **Page Background** | Slate 50 | `#f8fafc` | `slate-50` |
| **Card Background** | White | `#ffffff` | `white` |
| **Border / Divider** | Slate 200 | `#e2e8f0` | `slate-200` |
| **Muted Text** | Slate 500 | `#64748b` | `slate-500` |
| **Heading Text** | Slate 900 | `#0f172a` | `slate-900` |

**Tailwind v4 setup (CSS-first — no `tailwind.config.js` needed):**

In your main CSS file (e.g. `resources/css/app.css`):
```css
@import "tailwindcss";

@theme {
  --font-sans: 'Inter', sans-serif;
  --font-heading: 'Plus Jakarta Sans', sans-serif;

  --color-primary: #6366f1;
  --color-primary-dark: #4f46e5;
  --color-primary-light: #818cf8;

  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-danger: #f43f5e;
  --color-info: #0ea5e9;
}
```

This generates utility classes automatically: `font-sans`, `font-heading`, `bg-primary`, `text-primary`, `border-primary-dark`, `bg-success`, `text-warning`, etc. — same usage as before in your Blade files, just no JS config file to maintain.

> If your existing project still uses a `tailwind.config.js` (Tailwind v3 style) alongside v4, either migrate the `theme.extend` values above into the `@theme` block, or keep `tailwind.config.js` and wrap it with `@config "../../tailwind.config.js";` at the top of `app.css` — but the `@theme` approach above is the recommended v4-native way.

### Component Styling Notes (visual only — no logic changes)

- **Stat cards**: white background, `rounded-2xl`, soft shadow (`shadow-sm` → `shadow-md` on hover), colored icon badge in a tinted rounded square (e.g. `bg-primary/10 text-primary`) instead of solid color blocks.
- **Sidebar**: dark slate background (`slate-900`) with indigo active-state highlight instead of flat blue background — gives more contrast and a premium feel.
- **Status badges** (order status, stock status): pill-shaped (`rounded-full`), soft tinted background + matching text color (e.g. `bg-emerald-100 text-emerald-700`) rather than solid fills.
- **Buttons**: primary buttons use the new indigo, `rounded-xl`, subtle shadow on hover; keep existing sizes/click handlers as-is.
- **Chart.js**: update dataset colors to `#6366f1` (line) with a soft indigo gradient fill instead of flat blue.

### Consistency Across Placeholder Pages

Since Orders, Products, Categories, Reviews, Customers, Coupons, Banners, and Settings all extend the same `layouts/app.blade.php`, they automatically pick up:

- The new sidebar (slate-900 + indigo active state) and Lucide nav icons
- The new heading/body fonts (Plus Jakarta Sans / Inter)
- The new primary color, badges, and button styles for any buttons/tables added later

No per-page style work is needed until each placeholder gets its real content — at that point, just reuse the same card, table, and badge classes documented above so the whole panel stays visually consistent as pages are built out.

## Responsive Breakpoints

- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

*(Unchanged — breakpoints, grid structure, and mobile sidebar slide-in behavior stay exactly as implemented.)*

## Future Enhancements

The following routes are placeholders and ready for implementation:

- ✅ Dashboard (Complete)
- 🔲 Orders Management
- 🔲 Products CRUD
- 🔲 Categories Management
- 🔲 Reviews Moderation
- 🔲 Customer Management
- 🔲 Coupons System
- 🔲 Banner Management
- 🔲 System Settings

## Notes

- All placeholder routes currently redirect to the dashboard
- Statistics are calculated from actual database data
- Charts use sample data for demonstration
- Mobile sidebar has smooth slide-in animation
- All navigation links include active state indicators
- **This design refresh is purely cosmetic**: no controller logic, route definitions, or data queries are affected — only CSS classes, font references, and icon markup in the Blade views change
