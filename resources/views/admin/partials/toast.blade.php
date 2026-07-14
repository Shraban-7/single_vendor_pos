<div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

<style>
    .toast {
        pointer-events: auto;
        animation: slideInRight 0.3s ease-out;
    }

    .toast.removing {
        animation: slideOutRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
</style>

<script>
    // Toast Notification System
    class Toast {
        constructor() {
            this.toastId = 0;
        }

        getContainer() {
            // Lazy load container to ensure DOM is ready
            if (!this.container) {
                this.container = document.getElementById('toastContainer');
            }
            return this.container;
        }

        show(type, message, duration = 5000) {
            const container = this.getContainer();
            if (!container) {
                console.error('Toast container not found');
                return;
            }

            const id = ++this.toastId;
            const toast = this.createToast(id, type, message);
            container.appendChild(toast);

            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }

            return id;
        }

        createToast(id, type, message) {
            const toast = document.createElement('div');
            toast.id = `toast-${id}`;
            toast.className = 'toast bg-white rounded-xl shadow-2xl p-4 w-80 sm:w-96 flex items-start gap-3 border-l-4';

            const config = this.getTypeConfig(type);
            toast.classList.add(config.borderColor);

            toast.innerHTML = `
                <div class="flex-shrink-0 ${config.iconBg} w-10 h-10 rounded-full flex items-center justify-center">
                    <i class="${config.icon} ${config.iconColor}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-gray-900 text-sm mb-1">${config.title}</h4>
                    <p class="text-sm text-gray-600 break-words">${message}</p>
                </div>
                <button onclick="window.toastManager.remove(${id})" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            `;

            return toast;
        }

        getTypeConfig(type) {
            const configs = {
                success: {
                    title: 'Success',
                    icon: 'fas fa-check',
                    iconColor: 'text-green-600',
                    iconBg: 'bg-green-100',
                    borderColor: 'border-green-500'
                },
                error: {
                    title: 'Error',
                    icon: 'fas fa-exclamation-circle',
                    iconColor: 'text-red-600',
                    iconBg: 'bg-red-100',
                    borderColor: 'border-red-500'
                },
                warning: {
                    title: 'Warning',
                    icon: 'fas fa-exclamation-triangle',
                    iconColor: 'text-yellow-600',
                    iconBg: 'bg-yellow-100',
                    borderColor: 'border-yellow-500'
                },
                info: {
                    title: 'Info',
                    icon: 'fas fa-info-circle',
                    iconColor: 'text-blue-600',
                    iconBg: 'bg-blue-100',
                    borderColor: 'border-blue-500'
                }
            };

            return configs[type] || configs.info;
        }

        remove(id) {
            const toast = document.getElementById(`toast-${id}`);
            if (toast) {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }
        }

        success(message, duration) {
            return this.show('success', message, duration);
        }

        error(message, duration) {
            return this.show('error', message, duration);
        }

        warning(message, duration) {
            return this.show('warning', message, duration);
        }

        info(message, duration) {
            return this.show('info', message, duration);
        }
    }

    // Initialize toast manager immediately
    window.toastManager = new Toast();

    // Global convenience functions
    window.showToast = (type, message, duration) => window.toastManager.show(type, message, duration);
    window.showSuccess = (message, duration) => window.toastManager.success(message, duration);
    window.showError = (message, duration) => window.toastManager.error(message, duration);
    window.showWarning = (message, duration) => window.toastManager.warning(message, duration);
    window.showInfo = (message, duration) => window.toastManager.info(message, duration);

    // Show Laravel flash messages when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
        window.showSuccess("{{ session('success') }}");
        @endif

        @if(session('error'))
        window.showError("{{ session('error') }}");
        @endif

        @if(session('warning'))
        window.showWarning("{{ session('warning') }}");
        @endif

        @if(session('info'))
        window.showInfo("{{ session('info') }}");
        @endif

        @if($errors->any())
        @foreach($errors->all() as $error)
        window.showError("{{ $error }}");
        @endforeach
        @endif
    });
</script>