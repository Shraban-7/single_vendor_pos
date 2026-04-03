const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarClose = document.getElementById('sidebarClose');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const mainContent = document.getElementById('mainContent');

let isDesktop = window.innerWidth >= 1024;
let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

function toggleSidebar() {
    if (isDesktop) {
        // Desktop toggle: collapse/expand
        sidebarCollapsed = !sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed);

        if (sidebarCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            mainContent.classList.add('content-expanded');
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            mainContent.classList.remove('content-expanded');
        }
    } else {
        // Mobile toggle: show/hide
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
}

function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebarOverlay.classList.remove('hidden');
}

function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
}

sidebarToggle?.addEventListener('click', toggleSidebar);
sidebarOverlay?.addEventListener('click', closeSidebar);

// Handle sidebar state on window resize
function handleResize() {
    const wasDesktop = isDesktop;
    isDesktop = window.innerWidth >= 1024;

    if (isDesktop) {
        // Desktop: remove mobile classes and apply collapse state
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.add('hidden');

        // Apply saved collapse state
        if (sidebarCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            mainContent.classList.add('content-expanded');
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            mainContent.classList.remove('content-expanded');
        }
    } else {
        // Mobile: remove desktop classes and hide sidebar
        sidebar.classList.remove('sidebar-collapsed');
        mainContent.classList.remove('content-expanded');
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }
}

// Initialize sidebar state on page load
handleResize();

// Handle resize events
window.addEventListener('resize', handleResize);

// Scroll active menu item into view
function scrollToActiveMenuItem() {
    const activeLink = sidebar.querySelector('.sidebar-link.active');
    if (activeLink) {
        const sidebarNav = sidebar.querySelector('nav');
        if (sidebarNav) {
            // Get positions
            const navRect = sidebarNav.getBoundingClientRect();
            const linkRect = activeLink.getBoundingClientRect();
            
            // Calculate if link is out of view
            const isAboveView = linkRect.top < navRect.top;
            const isBelowView = linkRect.bottom > navRect.bottom;
            
            if (isAboveView || isBelowView) {
                // Scroll to center the active link
                const scrollTop = activeLink.offsetTop - sidebarNav.offsetTop - (sidebarNav.clientHeight / 2) + (activeLink.clientHeight / 2);
                sidebarNav.scrollTo({
                    top: scrollTop,
                    behavior: 'smooth'
                });
            }
        }
    }
}

// Scroll to active menu on page load (after a small delay to ensure CSS is applied)
setTimeout(scrollToActiveMenuItem, 100);

window.toggleModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    if (modal.classList.contains('hidden-modal')) {
        modal.classList.remove('hidden-modal');
        document.body.classList.add('modal-active');
    } else {
        modal.classList.add('hidden-modal');
        document.body.classList.remove('modal-active');
    }
};

document.addEventListener('click', function(e) {
    const closeBtn = e.target.closest('.modal-overlay .close');
    if (!closeBtn) return;

    const modal = closeBtn.closest('.modal-overlay');
    if (!modal) return;

    modal.classList.add('hidden-modal');
    document.body.classList.remove('modal-active');
});