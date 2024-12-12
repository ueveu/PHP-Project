// Main JavaScript file for enhanced user experience

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', 
                navLinks.classList.contains('active'));
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (navLinks && navLinks.classList.contains('active') &&
            !e.target.closest('.nav-links') && 
            !e.target.closest('.menu-toggle')) {
            navLinks.classList.remove('active');
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Form validation enhancement
document.querySelectorAll('form').forEach(form => {
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        // Show validation message on blur
        input.addEventListener('blur', () => {
            validateInput(input);
        });

        // Live validation as user types
        input.addEventListener('input', () => {
            if (input.dataset.lastValidation === 'invalid') {
                validateInput(input);
            }
        });
    });

    // Prevent multiple form submissions
    form.addEventListener('submit', function(e) {
        if (this.submitting) {
            e.preventDefault();
            return;
        }

        this.submitting = true;
        const submitBtn = this.querySelector('[type="submit"]');
        if (submitBtn) {
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Wird gesendet...';
            submitBtn.disabled = true;

            // Reset button after submission (success or failure)
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                this.submitting = false;
            }, 5000);
        }
    });
});

// Input validation helper
function validateInput(input) {
    const value = input.value.trim();
    let isValid = true;
    let message = '';

    // Required field validation
    if (input.required && !value) {
        isValid = false;
        message = 'Dieses Feld ist erforderlich.';
    }
    // Email validation
    else if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
        }
    }
    // Password validation
    else if (input.type === 'password' && value) {
        if (value.length < 6) {
            isValid = false;
            message = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
        }
    }

    // Update input styling and show message
    updateInputValidation(input, isValid, message);
    input.dataset.lastValidation = isValid ? 'valid' : 'invalid';
}

// Update input validation UI
function updateInputValidation(input, isValid, message) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;

    let messageElement = formGroup.querySelector('.validation-message');
    if (!messageElement) {
        messageElement = document.createElement('span');
        messageElement.className = 'validation-message';
        input.insertAdjacentElement('afterend', messageElement);
    }

    if (!isValid) {
        formGroup.classList.add('has-error');
        messageElement.textContent = message;
        messageElement.style.display = 'block';
    } else {
        formGroup.classList.remove('has-error');
        messageElement.style.display = 'none';
    }
}

// Gallery image preview
document.querySelectorAll('.gallery-item img').forEach(img => {
    img.addEventListener('click', () => {
        const modal = createImageModal(img.src);
        document.body.appendChild(modal);
        
        // Remove modal when clicking outside the image
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        // Remove modal on escape key
        document.addEventListener('keydown', function closeModal(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', closeModal);
            }
        });
    });
});

// Create image preview modal
function createImageModal(src) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <img src="${src}" alt="Vergrößerte Ansicht">
            <button class="close-modal" aria-label="Schließen">×</button>
        </div>
    `;

    // Add modal styles
    const style = document.createElement('style');
    style.textContent = `
        .image-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 2rem;
            cursor: pointer;
        }
        .modal-content {
            max-width: 90%;
            max-height: 90vh;
            position: relative;
        }
        .modal-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            cursor: default;
        }
        .close-modal {
            position: absolute;
            top: -2rem;
            right: -2rem;
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            padding: 0.5rem;
        }
    `;
    document.head.appendChild(style);

    return modal;
}

// Add loading indicator for AJAX requests
const loadingSpinner = document.createElement('div');
loadingSpinner.className = 'spinner';
loadingSpinner.style.display = 'none';
document.body.appendChild(loadingSpinner);

// Show/hide loading spinner for AJAX requests
let activeRequests = 0;
function updateSpinner() {
    loadingSpinner.style.display = activeRequests > 0 ? 'block' : 'none';
}

// Intercept AJAX requests to show loading spinner
const originalFetch = window.fetch;
window.fetch = function(...args) {
    activeRequests++;
    updateSpinner();
    
    return originalFetch.apply(this, args)
        .finally(() => {
            activeRequests--;
            updateSpinner();
        });
}; 