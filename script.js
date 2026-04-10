// ===================================
// Navigation Scroll Effect
// ===================================
const navbar = document.getElementById('navbar');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// ===================================
// Mobile Menu Toggle
// ===================================
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');

hamburger.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    hamburger.classList.toggle('active');
});

// Close menu when clicking on a link
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        hamburger.classList.remove('active');
    });
});

// ===================================
// Smooth Scroll & Active Link
// ===================================
const sections = document.querySelectorAll('section[id]');

function updateActiveLink() {
    const scrollY = window.pageYOffset;

    sections.forEach(section => {
        const sectionHeight = section.offsetHeight;
        const sectionTop = section.offsetTop - 100;
        const sectionId = section.getAttribute('id');
        const navLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);

        if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
            navLinks.forEach(link => link.classList.remove('active'));
            if (navLink) {
                navLink.classList.add('active');
            }
        }
    });
}

window.addEventListener('scroll', updateActiveLink);

// ===================================
// Form Handling
// ===================================
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validate all inputs
        let isValid = true;
        formInputs.forEach(input => {
            if (!validateInput(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            showNotification('Controleer de formuliervelden en probeer opnieuw.', 'error');
            return;
        }

        // Get form data
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);

        // Add timestamp and ID for localStorage fallback
        data.timestamp = new Date().toISOString();
        data.id = Date.now().toString();

        try {
            // Try to submit to API
            const response = await fetch('api/submit.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                showNotification(result.message, 'success');

                // Reset form
                contactForm.reset();

                console.log('Form submitted to database:', result);
            } else {
                throw new Error(result.error || 'Submission failed');
            }
        } catch (error) {
            console.error('API submission failed, falling back to localStorage:', error);

            // Fallback to localStorage if API fails
            saveFormSubmission(data);

            // Show success message
            showNotification('Bedankt voor uw bericht! We nemen binnen 24 uur contact met u op.', 'success');

            // Reset form
            contactForm.reset();
        }
    });
}

// ===================================
// Save Form Submissions to localStorage (Fallback)
// ===================================
function saveFormSubmission(data) {
    // Get existing submissions
    let submissions = JSON.parse(localStorage.getItem('formSubmissions') || '[]');

    // Add new submission
    submissions.push(data);

    // Save back to localStorage
    localStorage.setItem('formSubmissions', JSON.stringify(submissions));

    console.log('Form submission saved to localStorage. Total submissions:', submissions.length);
}

// ===================================
// Notification System
// ===================================
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">${type === 'success' ? '✓' : '!'}</span>
            <span class="notification-message">${message}</span>
        </div>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 24px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' : 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        animation: slideInRight 0.4s ease, slideOutRight 0.4s ease 3.6s;
        font-weight: 600;
    `;

    // Add to page
    document.body.appendChild(notification);

    // Remove after 4 seconds
    setTimeout(() => {
        notification.remove();
    }, 4000);
}

// Add notification animations to document
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
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .notification-icon {
        width: 24px;
        height: 24px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
`;
document.head.appendChild(style);

// ===================================
// Intersection Observer for Animations
// ===================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all cards and sections
const animatedElements = document.querySelectorAll('.service-card, .result-card, .benefit-item, .floating-card');
animatedElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// ===================================
// Dynamic Stats Counter
// ===================================
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = formatNumber(target);
            clearInterval(timer);
        } else {
            element.textContent = formatNumber(Math.floor(current));
        }
    }, 16);
}

function formatNumber(num) {
    if (num >= 1000000) {
        return '€' + (num / 1000000).toFixed(1) + 'M+';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'x';
    }
    return num + '+';
}

// Animate stats when they come into view
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
            entry.target.classList.add('animated');
            const statNumbers = entry.target.querySelectorAll('.stat-number');

            // Animate each stat
            statNumbers.forEach((stat, index) => {
                const text = stat.textContent.trim();

                if (text.includes('+') && !text.includes('€') && !text.includes('M') && !text.includes('x')) {
                    // Simple number with + (e.g., "250+")
                    const num = parseInt(text.replace('+', ''));
                    if (!isNaN(num)) {
                        setTimeout(() => {
                            let count = 0;
                            const interval = setInterval(() => {
                                count += Math.ceil(num / 50);
                                if (count >= num) {
                                    stat.textContent = num + '+';
                                    clearInterval(interval);
                                } else {
                                    stat.textContent = count + '+';
                                }
                            }, 30);
                        }, index * 200);
                    }
                } else if (text.includes('€') && text.includes('M')) {
                    // Money with M (e.g., "€5M+")
                    const numStr = text.replace('€', '').replace('M+', '').replace('M', '').trim();
                    const num = parseFloat(numStr);
                    if (!isNaN(num)) {
                        setTimeout(() => {
                            let count = 0;
                            const interval = setInterval(() => {
                                count += num / 50;
                                if (count >= num) {
                                    stat.textContent = '€' + num + 'M+';
                                    clearInterval(interval);
                                } else {
                                    stat.textContent = '€' + count.toFixed(1) + 'M+';
                                }
                            }, 30);
                        }, index * 200);
                    }
                } else if (text.includes('x')) {
                    // Multiplier (e.g., "4.2x")
                    const num = parseFloat(text.replace('x', ''));
                    if (!isNaN(num)) {
                        setTimeout(() => {
                            let count = 0;
                            const interval = setInterval(() => {
                                count += num / 50;
                                if (count >= num) {
                                    stat.textContent = num + 'x';
                                    clearInterval(interval);
                                } else {
                                    stat.textContent = count.toFixed(1) + 'x';
                                }
                            }, 30);
                        }, index * 200);
                    }
                }
            });
        }
    });
}, { threshold: 0.5 });

const heroStats = document.querySelector('.hero-stats');
if (heroStats) {
    statsObserver.observe(heroStats);
}

// ===================================
// Parallax Effect for Hero
// ===================================
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroVisual = document.querySelector('.hero-visual');

    if (heroVisual && scrolled < window.innerHeight) {
        heroVisual.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
});

// ===================================
// Form Validation
// ===================================
const formInputs = document.querySelectorAll('.contact-form input, .contact-form textarea, .contact-form select');

formInputs.forEach(input => {
    input.addEventListener('blur', () => {
        validateInput(input);
    });

    input.addEventListener('input', () => {
        if (input.classList.contains('error')) {
            validateInput(input);
        }
    });
});

function validateInput(input) {
    const value = input.value.trim();

    // Remove previous error
    input.classList.remove('error');
    const existingError = input.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Check if required field is empty
    if (input.hasAttribute('required') && !value) {
        showError(input, 'Dit veld is verplicht');
        return false;
    }

    // Email validation
    if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showError(input, 'Voer een geldig e-mailadres in');
            return false;
        }
    }

    return true;
}

function showError(input, message) {
    input.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #f5576c;
        font-size: 13px;
        margin-top: 6px;
        font-weight: 500;
    `;
    input.parentElement.appendChild(errorDiv);

    // Add error styling to input
    input.style.borderColor = '#f5576c';
}

// Add CSS for error state
const errorStyle = document.createElement('style');
errorStyle.textContent = `
    .form-group input.error,
    .form-group textarea.error,
    .form-group select.error {
        border-color: #f5576c !important;
        animation: shake 0.3s ease;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
`;
document.head.appendChild(errorStyle);

// ===================================
// Initialize
// ===================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('AdsMarket website loaded successfully! 🚀');

    // Add loading animation complete
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);

    // Initial scroll check for header
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    }
});

// ===================================
// Preloader
// ===================================
window.addEventListener('load', () => {
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.classList.add('hidden');
            // Remove from DOM after animation completes
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        }, 600); // Show for at least 600ms
    }
});

// ===================================
// Scroll to Top Button
// ===================================
const scrollToTopBtn = document.querySelector('.scroll-to-top');

if (scrollToTopBtn) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });

    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===================================
// Cookie Consent Banner
// ===================================
const cookieConsent = document.querySelector('.cookie-consent');
const acceptCookiesBtn = document.querySelector('.cookie-consent-btn.accept');
const declineCookiesBtn = document.querySelector('.cookie-consent-btn.decline');

// Check if user has already made a choice
if (!localStorage.getItem('cookieConsent')) {
    // Show banner after 1 second
    setTimeout(() => {
        if (cookieConsent) {
            cookieConsent.classList.add('show');
        }
    }, 1000);
}

if (acceptCookiesBtn) {
    acceptCookiesBtn.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'accepted');
        cookieConsent.classList.remove('show');
        setTimeout(() => {
            cookieConsent.remove();
        }, 400);

        // Initialize analytics or other cookies here
        console.log('Cookies accepted');
    });
}

if (declineCookiesBtn) {
    declineCookiesBtn.addEventListener('click', () => {
        localStorage.setItem('cookieConsent', 'declined');
        cookieConsent.classList.remove('show');
        setTimeout(() => {
            cookieConsent.remove();
        }, 400);

        console.log('Cookies declined');
    });
}

// ===================================
// WhatsApp Floating Button
// ===================================
function openWhatsApp() {
    const phoneNumber = '31201234567'; // Replace with actual number
    const message = encodeURIComponent('Hallo! Ik wil graag meer informatie over jullie Google Ads diensten.');
    const whatsappURL = `https://wa.me/${phoneNumber}?text=${message}`;
    window.open(whatsappURL, '_blank');
}

// ===================================
// Smooth Page Transitions
// ===================================
document.addEventListener('DOMContentLoaded', () => {
    // Add fade-in animation to body
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.3s ease';
        document.body.style.opacity = '1';
    }, 100);
});

// ===================================
// Scroll Progress Bar
// ===================================
const scrollProgress = document.createElement('div');
scrollProgress.className = 'scroll-progress';
document.body.appendChild(scrollProgress);

window.addEventListener('scroll', () => {
    const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (window.scrollY / windowHeight) * 100;
    scrollProgress.style.width = scrolled + '%';
});
