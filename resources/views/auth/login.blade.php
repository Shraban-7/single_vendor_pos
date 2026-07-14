<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaraPOS — Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        'accent-glow': 'rgba(168, 85, 247, 0.15)',
                        'accent-soft': 'rgba(168, 85, 247, 0.08)',
                        danger: '#EF4444',
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #09090B;
            overflow-x: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.45;
            animation: orbFloat 14s ease-in-out infinite;
        }

        .orb-1 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.35), transparent 70%);
            top: -12%;
            left: -8%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.25), transparent 70%);
            bottom: -18%;
            right: -8%;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(192, 132, 252, 0.15), transparent 70%);
            top: 45%;
            left: 55%;
            animation-delay: -9s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(35px, -45px) scale(1.06); }
            66% { transform: translate(-25px, 25px) scale(0.94); }
        }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(168, 85, 247, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168, 85, 247, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .input-wrapper:focus-within {
            border-color: #A855F7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.12), 0 0 24px rgba(168, 85, 247, 0.06);
        }

        .input-wrapper {
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .input-wrapper.has-error {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .btn-primary {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #A855F7, #7C3AED);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before { left: 100%; }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 32px rgba(168, 85, 247, 0.35);
        }

        .btn-primary:active { transform: translateY(0) scale(0.98); }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-primary:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .toggle-pw { transition: color 0.2s ease, background 0.2s ease; }
        .toggle-pw:hover { color: #A855F7; background: rgba(168, 85, 247, 0.08); }

        .custom-check {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #27272F;
            border-radius: 4px;
            background: #131316;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            flex-shrink: 0;
        }

        .custom-check:checked { background: #A855F7; border-color: #A855F7; }

        .custom-check:checked::after {
            content: '';
            position: absolute;
            top: 1px;
            left: 5px;
            width: 5px;
            height: 9px;
            border: solid #09090B;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-check:focus-visible { outline: 2px solid #A855F7; outline-offset: 2px; }

        .toast {
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .toast.show { transform: translateX(0); }

        .login-card {
            animation: cardEnter 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }

        @keyframes cardEnter {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .side-content {
            animation: sideEnter 0.8s cubic-bezier(0.22, 1, 0.36, 1) 0.2s forwards;
            opacity: 0;
        }

        @keyframes sideEnter {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .status-dot { animation: pulse 2s ease-in-out infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            50% { opacity: 0.8; box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(168, 85, 247, 0.45);
            border-radius: 50%;
            animation: particleDrift linear infinite;
        }

        @keyframes particleDrift {
            0% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-420px) translateX(35px); opacity: 0; }
        }

        .spinner {
            border: 2px solid rgba(9, 9, 11, 0.3);
            border-top: 2px solid #09090B;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }

        /* Phone prefix divider */
        .phone-divider {
            width: 1px;
            height: 20px;
            background: #27272F;
            flex-shrink: 0;
        }

        /* Mobile scroll fix */
        @media (max-height: 680px) {
            .login-card { transform: scale(0.92); transform-origin: center center; }
        }

        @media (max-height: 580px) {
            .login-card { transform: scale(0.82); transform-origin: center center; }
        }

        /* Tighter spacing on xl+ screens */
        @media (min-width: 1280px) {
            .login-card { max-width: 380px; }
        }

        @media (min-width: 1536px) {
            .login-card { max-width: 360px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .orb, .particle, .status-dot, .login-card, .side-content {
                animation: none !important;
                opacity: 1 !important;
            }
            .btn-primary::before { display: none; }
        }
    </style>
</head>

<body class="h-screen w-screen flex">

    <!-- Background orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Toast container -->
    <div id="toastContainer" class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50 flex flex-col gap-2.5"></div>

    <!-- LEFT PANEL — Branding (hidden on mobile) -->
    <aside class="hidden lg:flex w-[48%] h-full relative overflow-hidden grid-pattern flex-col justify-between p-8 xl:p-10 2xl:p-12">
        <div id="particleField" class="absolute inset-0 pointer-events-none"></div>

        <div class="side-content relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-accent flex items-center justify-center shadow-lg shadow-accent/20">
                    <i data-lucide="scan-barcode" class="w-5 h-5 text-bg"></i>
                </div>
                <span class="font-display text-2xl font-bold text-fg tracking-tight">LaraPOS</span>
            </div>
        </div>

        <div class="side-content relative z-10 max-w-md">
            <h1 class="font-display text-4xl xl:text-5xl 2xl:text-6xl font-bold text-fg leading-[1.1] mb-5">
                Run your store<br>
                <span class="text-accent">smarter</span>, not harder.
            </h1>
            <p class="text-muted text-base xl:text-lg leading-relaxed mb-8">
                Lightning-fast transactions, real-time inventory, and powerful analytics — all in one system built for
                modern retail.
            </p>

            <div class="flex flex-wrap gap-2.5">
                <div class="flex items-center gap-2 bg-card/70 backdrop-blur-sm border border-border rounded-full px-3.5 py-1.5">
                    <i data-lucide="zap" class="w-4 h-4 text-accent"></i>
                    <span class="text-fg text-sm font-medium">Fast Checkout</span>
                </div>
                <div class="flex items-center gap-2 bg-card/70 backdrop-blur-sm border border-border rounded-full px-3.5 py-1.5">
                    <i data-lucide="package" class="w-4 h-4 text-accent"></i>
                    <span class="text-fg text-sm font-medium">Inventory Sync</span>
                </div>
                <div class="flex items-center gap-2 bg-card/70 backdrop-blur-sm border border-border rounded-full px-3.5 py-1.5">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-accent"></i>
                    <span class="text-fg text-sm font-medium">Live Analytics</span>
                </div>
                <div class="flex items-center gap-2 bg-card/70 backdrop-blur-sm border border-border rounded-full px-3.5 py-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-accent"></i>
                    <span class="text-fg text-sm font-medium">Secure Payments</span>
                </div>
            </div>
        </div>

        <div class="side-content relative z-10 flex items-center gap-3">
            <div class="status-dot w-2.5 h-2.5 rounded-full bg-success"></div>
            <span class="text-muted text-sm">All systems operational</span>
            <span class="text-muted text-sm ml-auto font-mono" id="liveTime"></span>
        </div>
    </aside>

    <!-- RIGHT PANEL — Login Form -->
    <main class="w-full lg:w-[52%] h-full flex items-center justify-center p-4 sm:p-6 lg:p-6 xl:p-8 relative z-10 overflow-y-auto">
        <div class="login-card w-full max-w-[400px] py-4">

            <!-- Mobile logo -->
            <div class="flex lg:hidden items-center gap-2.5 mb-6">
                <div class="w-9 h-9 rounded-lg bg-accent flex items-center justify-center">
                    <i data-lucide="scan-barcode" class="w-4 h-4 text-bg"></i>
                </div>
                <span class="font-display text-xl font-bold text-fg tracking-tight">LaraPOS</span>
            </div>

            <!-- Header -->
            <div class="mb-5 lg:mb-6">
                <h2 class="font-display text-2xl sm:text-3xl font-bold text-fg mb-1.5">Welcome back</h2>
                <p class="text-muted text-sm">Sign in to your terminal to continue</p>
            </div>

            <!-- Form -->
            <form id="loginForm" novalidate autocomplete="off">

                <!-- Phone Number -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-fg/80 mb-1.5" for="phone">Phone Number</label>
                    <div class="input-wrapper relative border border-border rounded-xl bg-surface flex items-center">
                        <!-- Phone icon -->
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                            <i data-lucide="smartphone" class="w-[18px] h-[18px]"></i>
                        </div>
                        <!-- +88 prefix with BD flag -->
                        <div class="flex items-center gap-1.5 pl-10 pr-0 flex-shrink-0 select-none">
                            <img src="https://flagcdn.com/w20/bd.png" alt="BD" class="w-5 h-3.5 rounded-sm object-cover">
                            <span class="text-fg text-[15px] font-medium">+88</span>
                        </div>
                        <!-- Divider -->
                        <div class="phone-divider mx-2.5"></div>
                        <!-- Input -->
                        <input type="tel" id="phone" placeholder="01XXXXXXXXX"
                            class="w-full bg-transparent text-fg pr-4 py-3 focus:outline-none placeholder:text-muted/40 text-[15px] tracking-wide"
                            aria-label="Phone number" inputmode="numeric" maxlength="11" required>
                    </div>
                    <p id="phoneError" class="text-danger text-xs mt-1 hidden"></p>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-fg/80 mb-1.5" for="password">Password</label>
                    <div class="input-wrapper relative border border-border rounded-xl bg-surface">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none">
                            <i data-lucide="lock" class="w-[18px] h-[18px]"></i>
                        </div>
                        <input type="password" id="password" placeholder="Enter your password"
                            class="w-full bg-transparent text-fg pl-11 pr-12 py-3 focus:outline-none placeholder:text-muted/40 text-[15px]"
                            aria-label="Password" required>
                        <button type="button" id="togglePw"
                            class="toggle-pw absolute right-2.5 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-lg text-muted"
                            aria-label="Toggle password visibility">
                            <i data-lucide="eye-off" class="w-[18px] h-[18px]" id="eyeIcon"></i>
                        </button>
                    </div>
                    <p id="pwError" class="text-danger text-xs mt-1 hidden"></p>
                </div>

                <!-- Remember me & Forgot -->
                <div class="flex items-center justify-between mb-5 lg:mb-6">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" class="custom-check" id="rememberMe">
                        <span class="text-sm text-muted">Remember this device</span>
                    </label>
                    <button type="button" id="forgotBtn"
                        class="text-sm text-accent hover:text-accent-dim transition-colors font-medium">
                        Forgot Password?
                    </button>
                </div>

                <!-- Submit -->
                <button type="submit" id="loginBtn"
                    class="btn-primary w-full py-3 rounded-xl font-display font-semibold text-bg text-[15px] flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-accent focus-visible:outline-offset-2">
                    <span id="btnText">Sign In</span>
                    <i data-lucide="arrow-right" class="w-4 h-4" id="btnArrow"></i>
                    <div class="spinner hidden" id="btnSpinner"></div>
                </button>
            </form>

            <!-- Footer -->
            <p class="text-center text-muted/50 text-xs mt-5 lg:mt-6">
                LaraPOS v3.2.1 &middot; Licensed to Meridian Retail Group
            </p>
        </div>
    </main>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // ─── Element References ───
        const form = document.getElementById('loginForm');
        const phoneInput = document.getElementById('phone');
        const pwInput = document.getElementById('password');
        const phoneError = document.getElementById('phoneError');
        const pwError = document.getElementById('pwError');
        const loginBtn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');
        const btnArrow = document.getElementById('btnArrow');
        const btnSpinner = document.getElementById('btnSpinner');
        const togglePw = document.getElementById('togglePw');
        const forgotBtn = document.getElementById('forgotBtn');

        // ─── Live Clock ───
        function updateClock() {
            const now = new Date();
            document.getElementById('liveTime').textContent =
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0') + ':' +
                String(now.getSeconds()).padStart(2, '0');
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ─── Floating Particles ───
        (function createParticles() {
            const field = document.getElementById('particleField');
            if (!field) return;
            for (let i = 0; i < 22; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.bottom = '-10px';
                p.style.animationDuration = (7 + Math.random() * 9) + 's';
                p.style.animationDelay = (Math.random() * 12) + 's';
                const size = (2 + Math.random() * 3) + 'px';
                p.style.width = size;
                p.style.height = size;
                p.style.opacity = 0.15 + Math.random() * 0.35;
                field.appendChild(p);
            }
        })();

        // ─── Phone: digits only, max 11 chars ───
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 11);
            clearFieldError(phoneInput, phoneError);
        });

        // ─── Password: clear error on type ───
        pwInput.addEventListener('input', function () {
            clearFieldError(pwInput, pwError);
        });

        // ─── Clear a field's error state ───
        function clearFieldError(input, errorEl) {
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
            input.closest('.input-wrapper').classList.remove('has-error');
        }

        // ─── Set a field's error state ───
        function setFieldError(input, errorEl, message) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
            input.closest('.input-wrapper').classList.add('has-error');
        }

        // ─── Password Show/Hide Toggle ───
        let pwVisible = false;

        togglePw.addEventListener('click', () => {
            pwVisible = !pwVisible;
            pwInput.type = pwVisible ? 'text' : 'password';

            document.getElementById('eyeIcon').setAttribute('data-lucide', pwVisible ? 'eye' : 'eye-off');
            lucide.createIcons();

            togglePw.setAttribute('aria-label', pwVisible ? 'Hide password' : 'Show password');
            pwInput.focus();
        });

        // ─── Toast System ───
        function showToast(message, type) {
            type = type || 'error';
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');

            const iconName = type === 'success' ? 'check-circle-2' : type === 'error' ? 'alert-circle' : 'info';
            const borderColor = type === 'success' ? 'border-success' : type === 'error' ? 'border-danger' : 'border-accent';
            const iconColor = type === 'success' ? 'text-success' : type === 'error' ? 'text-danger' : 'text-accent';

            toast.className = 'toast flex items-center gap-3 bg-card border ' + borderColor + ' rounded-xl px-4 py-3 shadow-2xl shadow-black/50 max-w-[340px] sm:min-w-[300px]';
            toast.innerHTML =
                '<i data-lucide="' + iconName + '" class="w-5 h-5 ' + iconColor + ' flex-shrink-0"></i>' +
                '<span class="text-fg text-sm font-medium">' + message + '</span>';

            container.appendChild(toast);
            lucide.createIcons();

            requestAnimationFrame(function () {
                requestAnimationFrame(function () { toast.classList.add('show'); });
            });

            setTimeout(function () {
                toast.classList.remove('show');
                setTimeout(function () { toast.remove(); }, 400);
            }, 3500);
        }

        // ─── Forgot Password ───
        forgotBtn.addEventListener('click', function () {
            showToast('Contact your store manager to reset your password', 'info');
        });

        // ─── Enter key submit ───
        [phoneInput, pwInput].forEach(function (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });
        });

        // ─── Form Submit (Laravel AJAX) — validation from backend only ───
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear all previous errors
            clearFieldError(phoneInput, phoneError);
            clearFieldError(pwInput, pwError);

            // Only check if fields are empty — no format validation
            let hasEmpty = false;

            if (!phoneInput.value.trim()) {
                setFieldError(phoneInput, phoneError, 'Please enter your phone number.');
                hasEmpty = true;
            }

            if (!pwInput.value.trim()) {
                setFieldError(pwInput, pwError, 'Please enter your password.');
                hasEmpty = true;
            }

            if (hasEmpty) return;

            // Loading state
            loginBtn.disabled = true;
            btnText.textContent = 'Authenticating...';
            btnArrow.classList.add('hidden');
            btnSpinner.classList.remove('hidden');

            try {
                const response = await fetch('/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phone: phoneInput.value,
                        password: pwInput.value,
                        remember: document.getElementById('rememberMe').checked
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showToast(data.message || 'Authenticated successfully.', 'success');

                    btnText.textContent = 'Welcome!';
                    btnSpinner.classList.add('hidden');
                    btnArrow.classList.remove('hidden');
                    btnArrow.setAttribute('data-lucide', 'check');
                    lucide.createIcons();

                    setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 700);

                } else {
                    // Backend validation errors — display them directly
                    if (data.errors) {
                        if (data.errors.phone && data.errors.phone[0]) {
                            setFieldError(phoneInput, phoneError, data.errors.phone[0]);
                        }
                        if (data.errors.password && data.errors.password[0]) {
                            setFieldError(pwInput, pwError, data.errors.password[0]);
                        }
                    } else {
                        showToast(data.message || 'Invalid phone number or password.', 'error');
                    }

                    throw new Error('Auth failed');
                }

            } catch (error) {
                // Reset button only if not redirecting
                if (btnText.textContent !== 'Welcome!') {
                    loginBtn.disabled = false;
                    btnText.textContent = 'Sign In';
                    btnArrow.classList.remove('hidden');
                    btnSpinner.classList.add('hidden');
                    btnArrow.setAttribute('data-lucide', 'arrow-right');
                    lucide.createIcons();

                    loginBtn.style.animation = 'none';
                    loginBtn.offsetHeight;
                    loginBtn.style.animation = 'shake 0.4s ease';
                }
            }
        });
    </script>
</body>
</html>
