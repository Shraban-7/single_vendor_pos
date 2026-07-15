<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error — LaraPOS</title>
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
                        'danger-dim': '#DC2626',
                        success: '#22C55E',
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
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(239,68,68,0.18), transparent 70%);
            top: -10%; right: 5%;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(168,85,247,0.1), transparent 70%);
            bottom: -8%; left: 5%;
            animation-delay: -6s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(25px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(239,68,68,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(239,68,68,0.015) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .icon-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .icon-card:hover {
            transform: rotate(0deg) !important;
            box-shadow: 0 0 40px rgba(239,68,68,0.1);
        }

        .quick-link {
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .quick-link:hover {
            background: rgba(239,68,68,0.06);
            border-color: rgba(239,68,68,0.15);
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #EF4444, #DC2626);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before { left: 100%; }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(239,68,68,0.25);
        }
        .btn-primary:active { transform: translateY(0) scale(0.98); }

        .btn-secondary {
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            border-color: rgba(239,68,68,0.3);
            color: #EDEDF0;
            background: rgba(239,68,68,0.05);
        }

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
            0%, 100% { transform: rotate(-3deg) translateY(0); }
            50% { transform: rotate(-3deg) translateY(-8px); }
        }

        .particle {
            position: absolute;
            width: 2px; height: 2px;
            background: rgba(239,68,68,0.3);
            border-radius: 50%;
            animation: drift linear infinite;
        }
        @keyframes drift {
            0% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-350px) translateX(-25px); opacity: 0; }
        }

        /* Server crash glitch flicker */
        .glitch-icon {
            animation: glitchFlicker 4s ease-in-out infinite;
        }
        @keyframes glitchFlicker {
            0%, 95%, 100% { opacity: 1; }
            96% { opacity: 0.4; transform: rotate(-3deg) translateX(-2px); }
            97% { opacity: 1; }
            98% { opacity: 0.5; transform: rotate(-3deg) translateX(2px); }
            99% { opacity: 1; }
        }

        /* Error pulse ring */
        .error-pulse {
            animation: errorPulse 2.5s ease-in-out infinite;
        }
        @keyframes errorPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.15); }
            50% { box-shadow: 0 0 0 12px rgba(239,68,68,0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .orb, .particle, .float-icon, .page-enter, .error-pulse, .glitch-icon { animation: none !important; opacity: 1 !important; }
            .btn-primary::before { display: none; }
        }
    </style>
</head>

<body class="h-screen w-screen flex items-center justify-center relative overflow-hidden grid-pattern">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div id="particles" class="absolute inset-0 pointer-events-none"></div>

    <div class="page-enter relative z-10 max-w-md w-full px-5 text-center">

        <!-- 500 Number + Icon -->
        <div class="relative inline-flex items-center justify-center mb-8">
            <span class="text-[9rem] sm:text-[11rem] font-bold leading-none tracking-tighter text-danger/[0.06] select-none font-display">500</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="icon-card error-pulse bg-card border border-border rounded-2xl p-4 shadow-2xl -rotate-3 float-icon glitch-icon">
                    <i data-lucide="server-crash" class="w-11 h-11 sm:w-13 sm:h-13 text-danger"></i>
                </div>
            </div>
        </div>

        <!-- Message -->
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-fg mb-2">Server Error</h2>
        <p class="text-sm text-muted mb-3 max-w-xs mx-auto leading-relaxed">
            Something went wrong on our end. Our team has been notified and is working on a fix.
        </p>
        <p class="text-xs text-muted/60 mb-8 max-w-[280px] mx-auto">
            Please try again in a few moments. If the problem persists, contact your system administrator.
        </p>

        <!-- Quick Actions -->
        <div class="bg-card/50 border border-border/60 rounded-xl p-4 mb-6 backdrop-blur-sm">
            <div class="grid grid-cols-2 gap-2">
                <a href="/admin/dashboard" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-danger/50 group-hover:text-danger transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Dashboard</span>
                </a>
                <button onclick="location.reload()" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left w-full">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 text-danger/50 group-hover:text-danger transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Retry Page</span>
                </button>
                <a href="/admin/sales" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="receipt" class="w-4 h-4 text-danger/50 group-hover:text-danger transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Sales</span>
                </a>
                <a href="/admin/settings" class="quick-link flex items-center gap-2.5 px-3 py-2.5 rounded-lg border border-transparent group text-left">
                    <i data-lucide="settings" class="w-4 h-4 text-danger/50 group-hover:text-danger transition-colors flex-shrink-0"></i>
                    <span class="text-sm text-muted group-hover:text-fg transition-colors">Settings</span>
                </a>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-center gap-3">
            <button onclick="location.reload()"
                class="btn-primary inline-flex items-center gap-2 text-white text-sm font-semibold py-2.5 px-6 rounded-xl">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                Try Again
            </button>
            <a href="/admin/dashboard"
                class="btn-secondary inline-flex items-center gap-2 border border-border text-muted text-sm font-medium py-2.5 px-6 rounded-xl">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                Dashboard
            </a>
        </div>

        <p class="text-muted/40 text-xs mt-10">LaraPOS &middot; Admin Panel</p>
    </div>

    <script>
        lucide.createIcons();
        (function () {
            const field = document.getElementById('particles');
            for (let i = 0; i < 14; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.bottom = '-5px';
                p.style.animationDuration = (8 + Math.random() * 11) + 's';
                p.style.animationDelay = (Math.random() * 12) + 's';
                const s = (1.5 + Math.random() * 2) + 'px';
                p.style.width = s; p.style.height = s;
                p.style.opacity = 0.08 + Math.random() * 0.25;
                field.appendChild(p);
            }
        })();
    </script>
</body>
</html>
