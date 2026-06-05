// assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {

    // ===== NAVBAR SCROLL =====
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // ===== HAMBURGER MENU =====
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('show');
            const icon = hamburger.querySelector('i');
            if (navMenu.classList.contains('show')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        });
    }

    // ===== HERO PARTICLES =====
    const particleContainer = document.querySelector('.hero-particles');
    if (particleContainer) {
        for (let i = 0; i < 20; i++) {
            const p = document.createElement('div');
            p.className = 'hero-particle';
            p.style.left = Math.random() * 100 + '%';
            p.style.animationDuration = (4 + Math.random() * 6) + 's';
            p.style.animationDelay = Math.random() * 5 + 's';
            p.style.width = (2 + Math.random() * 4) + 'px';
            p.style.height = p.style.width;
            particleContainer.appendChild(p);
        }
    }

    // ===== NOTIFICATION DROPDOWN =====
    const notifBtn = document.querySelector('.notif-btn');
    const notifDropdown = document.querySelector('.notif-dropdown');
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });
        document.addEventListener('click', () => {
            notifDropdown.classList.remove('show');
        });
    }

    // ===== RATING STARS =====
    const ratingContainers = document.querySelectorAll('.rating-input');
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('i');
        const input = container.querySelector('input');
        stars.forEach((star, idx) => {
            star.addEventListener('click', () => {
                const val = idx + 1;
                input.value = val;
                stars.forEach((s, i) => {
                    s.classList.toggle('active', i < val);
                });
            });
            star.addEventListener('mouseenter', () => {
                stars.forEach((s, i) => {
                    s.style.color = i <= idx ? '#fbbf24' : '';
                });
            });
        });
        container.addEventListener('mouseleave', () => {
            const val = parseInt(input.value) || 0;
            stars.forEach((s, i) => {
                s.style.color = '';
                s.classList.toggle('active', i < val);
            });
        });
    });

    // ===== FILE UPLOAD PREVIEW =====
    const fileInputs = document.querySelectorAll('.file-upload input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const wrapper = this.closest('.file-upload');
            const preview = wrapper.querySelector('.file-preview');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview) {
                        preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ===== FORM VALIDATION =====
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            this.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'var(--danger)';
                    field.addEventListener('input', function() {
                        this.style.borderColor = '';
                    }, { once: true });
                }
            });
            // Email validation
            this.querySelectorAll('input[type="email"]').forEach(field => {
                if (field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                    valid = false;
                    field.style.borderColor = 'var(--danger)';
                }
            });
            // Password min length
            this.querySelectorAll('input[minlength]').forEach(field => {
                if (field.value.length < parseInt(field.getAttribute('minlength'))) {
                    valid = false;
                    field.style.borderColor = 'var(--danger)';
                }
            });
            if (!valid) {
                e.preventDefault();
                showToast('Harap lengkapi semua field dengan benar', 'danger');
            }
        });
    });

    // ===== MODAL =====
    window.openModal = function(id) {
        document.getElementById(id).classList.add('show');
        document.body.style.overflow = 'hidden';
    };
    window.closeModal = function(id) {
        document.getElementById(id).classList.remove('show');
        document.body.style.overflow = '';
    };

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    // ===== TOAST =====
    window.showToast = function(message, type = 'success') {
        const container = document.querySelector('.toast-container') || (() => {
            const c = document.createElement('div');
            c.className = 'toast-container';
            document.body.appendChild(c);
            return c;
        })();
        const toast = document.createElement('div');
        toast.className = `toast alert-${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(40px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // ===== ADMIN SIDEBAR TOGGLE =====
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', () => {
            adminSidebar.classList.toggle('show');
        });
    }

    // ===== SCROLL ANIMATIONS =====
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -40px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card, .feature-card, .stat-card, .review-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });

    // ===== ALERT AUTO DISMISS =====
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 4000);
    });

    // ===== COUNTER ANIMATION =====
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.getAttribute('data-count'));
        let current = 0;
        const increment = target / 60;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(current).toLocaleString();
        }, 16);
    });

});