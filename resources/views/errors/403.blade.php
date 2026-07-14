<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden — LaraPOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#09090B',
                        surface: '#131316',
                        card: '#1A1A1F',
                        border: '#27272F',
                        fg: '#EDEDF0',
                        muted: '#6E6E7A',
                        accent: '#A855F7',
                        'accent-dim': '#7C3AED',
                        danger: '#EF4444',
                        success: '#22C55E',
                        warn: '#F59E0B',
                    },
                    fontFamily: {
                        display: ['"Space Grotesk"', 'sans-serif'],
                        body: ['"DM Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #09090B; }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.35;
            animation: orbFloat 14s ease-in-out infinite;
        }
        .orb-1 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(245,158,11,0.2), transparent 70%);
            top: -10%; left: 5%;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(168,85,247,0.12), transparent 70%);
            bottom: -8%; right: 5%;
            animation-delay: -5s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(25px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(245,158,11,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(245,158,11,0.015) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .icon-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .icon-card:hover {
            transform: rotate(0deg) !important;
            box-shadow: 0 0 40px rgba(245,158,11,0.1);
        }

        .quick-link {
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .quick-link:hover {
            background: rgba(245,158,11,0.06);
            border-color: rgba(245,158,11,0.15);
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #F59E0B, #D97706);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before { left: 100%; }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(245,158,11,0.25);
        }
        .btn-primary:active { transform: translateY(0) scale(0.98); }

        .page-enter {
            animation: pageIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }
        @keyframes pageIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .float-icon {
            animation: floatY 3s ease-in-out infinite;
        }
        @keyframes floatY {
            0%, 100% { transform: rotate(3deg) translateY(0); }
            50% { transform: rotate(3deg) translateY(-8px); }
        }

        .particle {
            position: absolute;
            width: 2px; height: 2px;
            background: rgba(245,158,11,0.3);
            border-radius: 50%;
            animation: drift linear infinite;
        }
        @keyframes drift {
            0% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-350px) translateX(25px); opacity: 0; }
        }

        /* Shield pulse ring */
        .shield-pulse {
            animation: shieldPulse 2.5s ease-in-out infinite;
        }
        @keyframes shieldPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.15); }
            50% { box-shadow: 0 0 0 12px rgba(245,158,11,0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .orb, .particle, .float-icon, .page-enter, .shield-pulse { animation: none !important; opacity: 1 !important; }
            .btn-primary::before { display: none; }
        }
    </style>
</head>

<body class="h-screen w-screen flex items-center justify-center relative overflow-hidden grid-pattern">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div id="particles" class="absolute inset-0 pointer-events-none"></div>

    <div class="page-enter relative z-10 max-w-md w-full px-5 text-center">

        <!-- 403 Number + Icon -->
        <div class="relative inline-flex items-center justify-center mb-8">
            <span class="text-[9rem] sm:text-[11rem] font-bold leading-none tracking-tighter text-warn/[0.06] select-none font-display">403</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="icon-card shield-pulse bg-card border border-border rounded-2xl p-4 shadow-2xl rotate-3 float-icon">
                    <i data-lucide="shield-alert" class="w-11 h-11 sm:w-13 sm:h-13 text-warn"></i>
                </div>
            </div>
        </div>

        <!-- Message -->
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-fg mb-2">Access Denied</h2>
        <p class="text-sm text-muted mb-8 max-w-xs mx-auto leading-relaxed">
            You don't have permission to access this page. Contact your administrator if you believe this is a mistake.
        </p>

        <!-- Quick Actions -->
        <div class="bg-card/50 border border-border/60 rounded-xl p-4 mb-6 backdrop-blur-sm">
            <div class="grid grid-cols-2 gap-2">
                <a href="/admin/dashboard" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-warn/50 group-hover:text-warn transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Dashboard</span>
                </a>
                <a href="/admin/products" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="package" class="w-4 h-4 text-warn/50 group-hover:text-warn transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Products</span>
                </a>
                <a href="/admin/orders" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="receipt" class="w-4 h-4 text-warn/50 group-hover:text-warn transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Orders</span>
                </a>
                <a href="/admin/profile" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="user-cog" class="w-4 h-4 text-warn/50 group-hover:text-warn transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">My Profile</span>
                </a>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-center gap-3">
            <a href="/admin/dashboard"
                class="btn-primary inline-flex items-center gap-2 text-bg text-sm font-semibold py-2.5 px-6 rounded-xl">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>
            <button onclick="history.back()"
                class="inline-flex items-center gap-2 border border-border hover:border-warn/40 text-muted hover:text-fg text-sm font-medium py-2.5 px-6 rounded-xl transition-all hover:bg-warn/5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Go Back
            </button>
        </div>

        <p class="text-muted/40 text-xs mt-10">LaraPOS &middot; Admin Panel</p>
    </div>

    <script>
        lucide.createIcons();
        (function () {
            const field = document.getElementById('particles');
            for (let i = 0; i < 12; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.bottom = '-5px';
                p.style.animationDuration = (9 + Math.random() * 10) + 's';
                p.style.animationDelay = (Math.random() * 12) + 's';
                const s = (1.5 + Math.random() * 2) + 'px';
                p.style.width = s; p.style.height = s;
                p.style.opacity = 0.1 + Math.random() * 0.25;
                field.appendChild(p);
            }
        })();
    </script>
</body>
</html>
