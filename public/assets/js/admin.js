const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const mainContent = document.getElementById('mainContent');
const tooltip = document.getElementById('sidebarTooltip');

let isDesktop = window.innerWidth >= 1024;
let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

// ── Collapse / Expand ────────────────────────────────────────
function toggleSidebar() {
    if (isDesktop) {
        sidebarCollapsed = !sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
        applySidebarState();
    } else {
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        isOpen ? closeMobileSidebar() : openMobileSidebar();
    }
}

function applySidebarState() {
    if (sidebarCollapsed) {
        sidebar.classList.add('sidebar-collapsed');
    } else {
        sidebar.classList.remove('sidebar-collapsed');
    }
}

function openMobileSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebarOverlay.classList.remove('hidden');
}

function closeMobileSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
}

sidebarToggle?.addEventListener('click', toggleSidebar);
sidebarOverlay?.addEventListener('click', closeMobileSidebar);

// ── Resize handler ───────────────────────────────────────────
function handleResize() {
    isDesktop = window.innerWidth >= 1024;
    if (isDesktop) {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        applySidebarState();
    } else {
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }
}

handleResize();
window.addEventListener('resize', handleResize);

// ── Submenu toggle ───────────────────────────────────────────
window.toggleSidebarGroup = function(btn) {
    if (sidebarCollapsed) return; // no submenu in collapsed mode
    const group = btn.closest('.sidebar-group');
    if (!group) return;
    group.classList.toggle('open');
};

// ── Tooltip on collapsed hover ───────────────────────────────
sidebar.addEventListener('mouseover', function(e) {
    if (!sidebarCollapsed || !isDesktop) return;
    const target = e.target.closest('[data-tooltip]');
    if (!target) { hideTooltip(); return; }

    const label = target.getAttribute('data-tooltip');
    const rect = target.getBoundingClientRect();
    tooltip.textContent = label;
    tooltip.style.top = (rect.top + rect.height / 2 - 14) + 'px';
    tooltip.classList.add('visible');
});

sidebar.addEventListener('mouseout', function(e) {
    if (!e.relatedTarget || !e.relatedTarget.closest('#sidebar')) {
        hideTooltip();
    }
});

function hideTooltip() {
    tooltip?.classList.remove('visible');
}

// ── Scroll active item into view ─────────────────────────────
function scrollToActiveMenuItem() {
    const activeLink = sidebar.querySelector('.sidebar-link.active, .sidebar-group-btn.active');
    if (!activeLink) return;
    const nav = sidebar.querySelector('.sidebar-nav');
    if (!nav) return;
    const navRect = nav.getBoundingClientRect();
    const linkRect = activeLink.getBoundingClientRect();
    if (linkRect.top < navRect.top || linkRect.bottom > navRect.bottom) {
        const scrollTop = activeLink.offsetTop - nav.offsetTop - nav.clientHeight / 2 + activeLink.clientHeight / 2;
        nav.scrollTo({ top: scrollTop, behavior: 'smooth' });
    }
}

setTimeout(scrollToActiveMenuItem, 100);

// ── Modals ───────────────────────────────────────────────────
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