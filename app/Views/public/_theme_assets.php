<style>
    /* ====================================================================== */
    /* SHARED PUBLIC THEME & PAPER DARK MODE (SAWAMAWA MEDICAL CENTER)        */
    /* ====================================================================== */
    .btn-public-theme {
        background: rgba(13, 148, 136, 0.08);
        border: 1px solid rgba(13, 148, 136, 0.25);
        color: #0d9488;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-public-theme:hover {
        background: rgba(13, 148, 136, 0.16);
    }

    body.dark-mode {
        background-color: #14171a !important;
        color: #f0f6fc !important;
    }
    body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
    body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
    body.dark-mode .text-dark,
    body.dark-mode .navbar-brand,
    body.dark-mode .navbar-brand-title,
    body.dark-mode .article-title,
    body.dark-mode .article-content h1,
    body.dark-mode .article-content h2,
    body.dark-mode .article-content h3,
    body.dark-mode .article-content h4,
    body.dark-mode .article-content h5 {
        color: #f0f6fc !important;
    }
    body.dark-mode .text-muted,
    body.dark-mode .text-secondary,
    body.dark-mode .article-summary,
    body.dark-mode .article-content {
        color: #cbd5e1 !important;
    }
    body.dark-mode .navbar-main,
    body.dark-mode .navbar-custom {
        background: rgba(24, 27, 31, 0.94) !important;
        border-bottom: 1px solid #374151 !important;
    }
    body.dark-mode .navbar-main .nav-link {
        color: #d1d5db !important;
    }
    body.dark-mode .card,
    body.dark-mode .reg-card,
    body.dark-mode .article-card,
    body.dark-mode .recent-card,
    body.dark-mode .ticket-wrapper,
    body.dark-mode .ticket-container,
    body.dark-mode .modal-content,
    body.dark-mode .patient-type-card,
    body.dark-mode .patient-type-select-wa,
    body.dark-mode .category-pill {
        background: #1f2328 !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }
    body.dark-mode .category-pill.active,
    body.dark-mode .category-pill:hover {
        background: #0d9488 !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
    }
    body.dark-mode .bg-white,
    body.dark-mode .modal-footer.bg-white {
        background: #1f2328 !important;
        border-color: #374151 !important;
    }
    body.dark-mode .bg-light,
    body.dark-mode .modal-body.bg-light,
    body.dark-mode .queue-badge-box {
        background: #181b1f !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }
    body.dark-mode .form-control,
    body.dark-mode .custom-select {
        background: #14171a !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }
    body.dark-mode .form-control:disabled {
        background: #111316 !important;
        color: #6b7280 !important;
    }
    body.dark-mode .alert-light {
        background: #181b1f !important;
        border-color: #374151 !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .alert-warning {
        background: rgba(245, 158, 11, 0.14) !important;
        border-color: rgba(245, 158, 11, 0.35) !important;
        color: #fde68a !important;
    }
    body.dark-mode .alert-info {
        background: rgba(14, 165, 233, 0.14) !important;
        border-color: rgba(14, 165, 233, 0.35) !important;
        color: #bae6fd !important;
    }
    body.dark-mode .badge-light,
    body.dark-mode .btn-light {
        background: #181b1f !important;
        border-color: #374151 !important;
        color: #e5e7eb !important;
    }
    body.dark-mode .btn-outline-dark,
    body.dark-mode .btn-outline-secondary {
        border-color: #4b5563 !important;
        color: #e5e7eb !important;
    }
    body.dark-mode .btn-outline-dark:hover,
    body.dark-mode .btn-outline-secondary:hover {
        background: #374151 !important;
        color: #ffffff !important;
    }
    body.dark-mode .border,
    body.dark-mode .border-top,
    body.dark-mode .border-bottom,
    body.dark-mode .article-footer {
        border-color: #374151 !important;
    }
    body.dark-mode .btn-public-theme {
        background: rgba(255, 255, 255, 0.08);
        border-color: #374151;
        color: #facc15;
    }

    /* Daftar Online Specific Dark Mode Overrides (mengatasi inline background) */
    body.dark-mode .status-indicator-bar {
        background: #181b1f !important;
        border-bottom: 1px solid #374151 !important;
    }
    body.dark-mode .status-open {
        background: rgba(16, 185, 129, 0.14) !important;
        color: #6ee7b7 !important;
        border-color: rgba(16, 185, 129, 0.35) !important;
    }
    body.dark-mode .status-closed {
        background: rgba(239, 68, 68, 0.14) !important;
        color: #fca5a5 !important;
        border-color: rgba(239, 68, 68, 0.35) !important;
    }
    body.dark-mode .patient-type-card.active,
    body.dark-mode .visit-type-pill.active,
    body.dark-mode .patient-type-select-wa[style*="0d9488"] {
        background: rgba(13, 148, 136, 0.18) !important;
        border-color: #14b8a6 !important;
        color: #f0f6fc !important;
    }
    body.dark-mode .visit-type-pill,
    body.dark-mode .patient-type-card,
    body.dark-mode .patient-type-select-wa {
        background: #181b1f !important;
        border-color: #374151 !important;
        color: #d1d5db !important;
    }
    body.dark-mode .reg-card .card[style*="f0fdf4"],
    body.dark-mode .container .card[style*="f0fdf4"],
    body.dark-mode #box-old-patient-verified {
        background: #181b1f !important;
        border-color: #374151 !important;
        color: #f0f6fc !important;
    }
    body.dark-mode .form-section-title,
    body.dark-mode label {
        color: #e5e7eb !important;
    }
</style>

<script>
    (function() {
        function applySharedPublicTheme(theme) {
            var isDark = theme === 'dark';
            document.body.classList.toggle('dark-mode', isDark);
            var icon = document.getElementById('icon-public-theme');
            var label = document.getElementById('label-public-theme');
            if (icon) {
                icon.classList.toggle('fa-moon', !isDark);
                icon.classList.toggle('fa-sun', isDark);
            }
            if (label) {
                label.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var savedTheme = localStorage.getItem('public_theme') || localStorage.getItem('theme') || 'light';
            applySharedPublicTheme(savedTheme);

            var toggleBtn = document.getElementById('btn-public-theme-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    var nextTheme = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
                    localStorage.setItem('public_theme', nextTheme);
                    applySharedPublicTheme(nextTheme);
                });
            }
        });
    })();
</script>
