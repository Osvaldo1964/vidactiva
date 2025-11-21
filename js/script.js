// ===================================
// VidActiva - Custom JavaScript
// ===================================

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // ===================================
    // Navbar Scroll Effect
    // ===================================
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // ===================================
    // Active Navigation Link
    // ===================================
    const sections = document.querySelectorAll('section[id]');
    
    function activateNavLink() {
        const scrollY = window.pageYOffset;
        
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${sectionId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    window.addEventListener('scroll', activateNavLink);
    
    // ===================================
    // Smooth Scroll for Navigation Links
    // ===================================
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Only handle internal links
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetSection = document.getElementById(targetId);
                
                if (targetSection) {
                    const navbarHeight = navbar.offsetHeight;
                    const targetPosition = targetSection.offsetTop - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                }
            }
        });
    });
    
    // ===================================
    // Scroll to Top Button
    // ===================================
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollTopBtn.classList.add('show');
        } else {
            scrollTopBtn.classList.remove('show');
        }
    });
    
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // ===================================
    // Scroll Indicator Animation
    // ===================================
    const scrollIndicator = document.querySelector('.scroll-indicator');
    
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            const serviciosSection = document.getElementById('servicios');
            if (serviciosSection) {
                serviciosSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
    
    // ===================================
    // Form Validation & Submission
    // ===================================
    
    // Registration Form
    const registrationForm = document.getElementById('registrationForm');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (this.checkValidity()) {
                // Show success message
                showNotification('¡Gracias por registrarse! Nos pondremos en contacto con usted pronto.', 'success');
                
                // Reset form
                this.reset();
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                showNotification('Por favor, complete todos los campos requeridos.', 'error');
            }
            
            this.classList.add('was-validated');
        });
    }
    
    // PQR Form
    const pqrForm = document.getElementById('pqrForm');
    
    if (pqrForm) {
        pqrForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (this.checkValidity()) {
                // Show success message
                showNotification('Su PQR ha sido enviado exitosamente. Le responderemos en las próximas 48 horas.', 'success');
                
                // Reset form
                this.reset();
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                showNotification('Por favor, complete todos los campos requeridos.', 'error');
            }
            
            this.classList.add('was-validated');
        });
    }
    
    // ===================================
    // Notification System
    // ===================================
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} notification-toast`;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 500px;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            animation: slideInRight 0.5s ease;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'} me-2 fs-4"></i>
                <div>${message}</div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.5s ease';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, 5000);
    }
    
    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
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
    `;
    document.head.appendChild(style);
    
    // ===================================
    // Story Carousel Controls
    // ===================================
    const prevStoryBtn = document.getElementById('prevStory');
    const nextStoryBtn = document.getElementById('nextStory');
    const storyCards = document.querySelectorAll('.story-card');
    
    let currentStoryIndex = 0;
    
    if (prevStoryBtn && nextStoryBtn && storyCards.length > 0) {
        prevStoryBtn.addEventListener('click', function() {
            currentStoryIndex = (currentStoryIndex - 1 + storyCards.length) % storyCards.length;
            scrollToStory(currentStoryIndex);
        });
        
        nextStoryBtn.addEventListener('click', function() {
            currentStoryIndex = (currentStoryIndex + 1) % storyCards.length;
            scrollToStory(currentStoryIndex);
        });
    }
    
    function scrollToStory(index) {
        const storyCard = storyCards[index];
        if (storyCard) {
            storyCard.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest', 
                inline: 'center' 
            });
        }
    }
    
    // ===================================
    // Service Card Hover Effects
    // ===================================
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // ===================================
    // Treatment Items Click Effect
    // ===================================
    const treatmentItems = document.querySelectorAll('.treatment-item');
    
    treatmentItems.forEach(item => {
        item.addEventListener('click', function() {
            // Add pulse animation
            this.style.animation = 'pulse 0.5s ease';
            
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
            
            // You can add more functionality here, like opening a modal
            console.log('Treatment item clicked:', this.querySelector('h5').textContent);
        });
    });
    
    // ===================================
    // Intersection Observer for Animations
    // ===================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animatedElements = document.querySelectorAll('.service-card, .treatment-item, .story-card, .location-card');
    
    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
    
    // ===================================
    // File Input Custom Label
    // ===================================
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
            const label = this.nextElementSibling;
            if (label && label.tagName === 'SMALL') {
                label.textContent = `Archivo seleccionado: ${fileName}`;
            }
        });
    });
    
    // ===================================
    // Loading State for Forms
    // ===================================
    function setFormLoading(form, isLoading) {
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (isLoading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtn.dataset.originalText || 'Enviar';
        }
    }
    
    // ===================================
    // Console Welcome Message
    // ===================================
    console.log('%c¡Bienvenido a VidActiva!', 'color: #667eea; font-size: 24px; font-weight: bold;');
    console.log('%cTu salud, nuestra prioridad', 'color: #764ba2; font-size: 16px;');
    
    // ===================================
    // Performance Monitoring
    // ===================================
    window.addEventListener('load', function() {
        const loadTime = window.performance.timing.domContentLoadedEventEnd - window.performance.timing.navigationStart;
        console.log(`Página cargada en ${loadTime}ms`);
    });
    
    // ===================================
    // Prevent Form Resubmission
    // ===================================
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
    // ===================================
    // Initialize Tooltips (Bootstrap 5)
    // ===================================
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // ===================================
    // Mobile Menu Auto-Close on Scroll
    // ===================================
    let lastScrollTop = 0;
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && navbarCollapse.classList.contains('show')) {
            // Scrolling down - close menu
            const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            bsCollapse.hide();
        }
        
        lastScrollTop = scrollTop;
    });
    
    // ===================================
    // Add Current Year to Footer
    // ===================================
    const currentYear = new Date().getFullYear();
    const yearElements = document.querySelectorAll('.current-year');
    yearElements.forEach(element => {
        element.textContent = currentYear;
    });
    
});

// ===================================
// External Link Handler
// ===================================
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'A' && e.target.hostname !== window.location.hostname) {
        e.target.setAttribute('rel', 'noopener noreferrer');
    }
});

// ===================================
// Lazy Loading Images (Native)
// ===================================
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}
