// ===================================
// PREMIUM ANIMATIONS & EFFECTS
// ===================================

// ===================================
// 1. Animated Background Particles
// ===================================
class ParticleBackground {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.canvas.id = 'particle-canvas';
        this.canvas.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        `;
        document.body.prepend(this.canvas);

        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.particleCount = 50;
        this.mouse = { x: null, y: null, radius: 150 };

        this.init();
        this.animate();
        this.addEventListeners();
    }

    init() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;

        for (let i = 0; i < this.particleCount; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                size: Math.random() * 3 + 1,
                speedX: Math.random() * 0.5 - 0.25,
                speedY: Math.random() * 0.5 - 0.25,
                color: `rgba(37, 99, 235, ${Math.random() * 0.3 + 0.1})`
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        this.particles.forEach((particle, i) => {
            // Update position
            particle.x += particle.speedX;
            particle.y += particle.speedY;

            // Bounce off edges
            if (particle.x < 0 || particle.x > this.canvas.width) particle.speedX *= -1;
            if (particle.y < 0 || particle.y > this.canvas.height) particle.speedY *= -1;

            // Mouse interaction
            const dx = this.mouse.x - particle.x;
            const dy = this.mouse.y - particle.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            if (distance < this.mouse.radius) {
                const force = (this.mouse.radius - distance) / this.mouse.radius;
                const angle = Math.atan2(dy, dx);
                particle.x -= Math.cos(angle) * force * 2;
                particle.y -= Math.sin(angle) * force * 2;
            }

            // Draw particle
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fillStyle = particle.color;
            this.ctx.fill();

            // Connect nearby particles
            this.particles.slice(i + 1).forEach(particle2 => {
                const dx2 = particle.x - particle2.x;
                const dy2 = particle.y - particle2.y;
                const distance2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

                if (distance2 < 100) {
                    this.ctx.beginPath();
                    this.ctx.strokeStyle = `rgba(37, 99, 235, ${0.1 * (1 - distance2 / 100)})`;
                    this.ctx.lineWidth = 1;
                    this.ctx.moveTo(particle.x, particle.y);
                    this.ctx.lineTo(particle2.x, particle2.y);
                    this.ctx.stroke();
                }
            });
        });

        requestAnimationFrame(() => this.animate());
    }

    addEventListeners() {
        window.addEventListener('mousemove', (e) => {
            this.mouse.x = e.x;
            this.mouse.y = e.y;
        });

        window.addEventListener('resize', () => {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        });
    }
}

// ===================================
// 2. 3D Card Tilt Effect
// ===================================
class CardTilt {
    constructor(selector) {
        this.cards = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.cards.forEach(card => {
            card.style.transition = 'transform 0.3s ease';

            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;

                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
            });
        });
    }
}

// ===================================
// 3. Typing Effect for Hero Title
// ===================================
class TypingEffect {
    constructor(element, texts, speed = 100) {
        this.element = element;
        this.texts = texts;
        this.speed = speed;
        this.textIndex = 0;
        this.charIndex = 0;
        this.isDeleting = false;
        this.type();
    }

    type() {
        const currentText = this.texts[this.textIndex];

        if (this.isDeleting) {
            this.element.textContent = currentText.substring(0, this.charIndex - 1);
            this.charIndex--;
        } else {
            this.element.textContent = currentText.substring(0, this.charIndex + 1);
            this.charIndex++;
        }

        let typeSpeed = this.speed;

        if (this.isDeleting) {
            typeSpeed /= 2;
        }

        if (!this.isDeleting && this.charIndex === currentText.length) {
            typeSpeed = 2000;
            this.isDeleting = true;
        } else if (this.isDeleting && this.charIndex === 0) {
            this.isDeleting = false;
            this.textIndex = (this.textIndex + 1) % this.texts.length;
            typeSpeed = 500;
        }

        setTimeout(() => this.type(), typeSpeed);
    }
}

// ===================================
// 4. Mouse Follow Cursor Effect
// ===================================
class CursorEffect {
    constructor() {
        this.cursor = document.createElement('div');
        this.cursor.className = 'custom-cursor';
        this.cursor.style.cssText = `
            position: fixed;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.8) 0%, rgba(37, 99, 235, 0) 70%);
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.15s ease;
            mix-blend-mode: screen;
        `;
        document.body.appendChild(this.cursor);

        this.cursorTrail = document.createElement('div');
        this.cursorTrail.className = 'cursor-trail';
        this.cursorTrail.style.cssText = `
            position: fixed;
            width: 40px;
            height: 40px;
            border: 2px solid rgba(37, 99, 235, 0.3);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9998;
            transition: all 0.3s ease;
        `;
        document.body.appendChild(this.cursorTrail);

        this.addEventListeners();
    }

    addEventListeners() {
        document.addEventListener('mousemove', (e) => {
            this.cursor.style.left = e.clientX - 10 + 'px';
            this.cursor.style.top = e.clientY - 10 + 'px';

            setTimeout(() => {
                this.cursorTrail.style.left = e.clientX - 20 + 'px';
                this.cursorTrail.style.top = e.clientY - 20 + 'px';
            }, 50);
        });

        // Scale cursor on clickable elements
        document.querySelectorAll('a, button, .btn').forEach(el => {
            el.addEventListener('mouseenter', () => {
                this.cursor.style.transform = 'scale(1.5)';
                this.cursorTrail.style.transform = 'scale(1.3)';
            });

            el.addEventListener('mouseleave', () => {
                this.cursor.style.transform = 'scale(1)';
                this.cursorTrail.style.transform = 'scale(1)';
            });
        });
    }
}

// ===================================
// 5. Smooth Reveal on Scroll
// ===================================
class SmoothReveal {
    constructor(selector, options = {}) {
        this.elements = document.querySelectorAll(selector);
        this.options = {
            threshold: 0.15,
            rootMargin: '0px',
            delay: 100,
            ...options
        };
        this.init();
    }

    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('revealed');
                    }, index * this.options.delay);
                    observer.unobserve(entry.target);
                }
            });
        }, this.options);

        this.elements.forEach(el => {
            el.classList.add('reveal-element');
            observer.observe(el);
        });
    }
}

// ===================================
// 6. Animated Gradient Background
// ===================================
class AnimatedGradient {
    constructor(selector) {
        this.elements = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.elements.forEach(el => {
            el.style.backgroundSize = '200% 200%';
            el.style.animation = 'gradientShift 8s ease infinite';
        });
    }
}

// ===================================
// 7. Floating Animation
// ===================================
class FloatingElements {
    constructor(selector) {
        this.elements = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.elements.forEach((el, index) => {
            el.style.animation = `float 3s ease-in-out infinite ${index * 0.5}s`;
        });
    }
}

// ===================================
// 8. Number Counter Animation
// ===================================
class CounterAnimation {
    constructor(selector) {
        this.counters = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        this.counters.forEach(counter => observer.observe(counter));
    }

    animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };

        updateCounter();
    }
}

// ===================================
// 9. Parallax Scroll Effect
// ===================================
class ParallaxScroll {
    constructor(selector, speed = 0.5) {
        this.elements = document.querySelectorAll(selector);
        this.speed = speed;
        this.init();
    }

    init() {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;

            this.elements.forEach(el => {
                const offset = el.offsetTop;
                const distance = scrolled - offset;
                el.style.transform = `translateY(${distance * this.speed}px)`;
            });
        });
    }
}

// ===================================
// 10. Magnetic Button Effect
// ===================================
class MagneticButtons {
    constructor(selector) {
        this.buttons = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.buttons.forEach(button => {
            button.addEventListener('mousemove', (e) => {
                const rect = button.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;

                button.style.transform = `translate(${x * 0.3}px, ${y * 0.3}px)`;
            });

            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translate(0, 0)';
            });
        });
    }
}

// ===================================
// Initialize All Animations
// ===================================
document.addEventListener('DOMContentLoaded', () => {
    // Particle Background
    new ParticleBackground();

    // 3D Card Tilt
    new CardTilt('.service-card, .visual-card, .service-preview-card');

    // Typing Effect (if hero title exists)
    const heroGradientText = document.querySelector('.hero-title .gradient-text');
    if (heroGradientText) {
        const originalText = heroGradientText.textContent;
        new TypingEffect(heroGradientText, [
            'Slimme Google Ads',
            'Meetbare Resultaten',
            'ROI Verbetering',
            'Online Groei'
        ], 80);
    }

    // Custom Cursor Effect - DISABLED
    // if (window.innerWidth > 768) {
    //     new CursorEffect();
    // }

    // Smooth Reveal Animations
    new SmoothReveal('.service-card, .feature-item, .benefit-item, .faq-item', {
        threshold: 0.1,
        delay: 100
    });

    // Animated Gradients
    new AnimatedGradient('.hero, .cta-section');

    // Floating Elements
    new FloatingElements('.visual-card, .floating-card');

    // Magnetic Buttons
    new MagneticButtons('.btn-primary, .btn-secondary');

    // Parallax Scroll
    new ParallaxScroll('.hero-visual', 0.3);

    console.log('🎨 Premium animations loaded!');
});

// ===================================
// Global Toast System
// ===================================
function showToast(title, message, type = 'info') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <h4>${title}</h4>
            <p>${message}</p>
        </div>
    `;
    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 100);

    // Auto-remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// Add CSS for reveal animations
const revealStyles = document.createElement('style');
revealStyles.textContent = `
    .reveal-element {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease, transform 0.8s ease;
    }
    
    .reveal-element.revealed {
        opacity: 1;
        transform: translateY(0);
    }
    
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-20px);
        }
    }
`;
document.head.appendChild(revealStyles);
