/**
 * Blood Donation System - Form Validation
 * Client-side validation for all forms
 */

document.addEventListener('DOMContentLoaded', function() {

    // ── Registration Form Validation ──
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            clearErrors();
            let isValid = true;

            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const bloodGroup = document.getElementById('blood_group');
            const phone = document.getElementById('phone');
            const district = document.getElementById('district');
            const age = document.getElementById('age');

            // Name validation
            if (!name.value.trim()) {
                showError(name, 'Name is required');
                isValid = false;
            } else if (name.value.trim().length < 3) {
                showError(name, 'Name must be at least 3 characters');
                isValid = false;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!emailRegex.test(email.value)) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            }

            // Password validation
            if (!password.value) {
                showError(password, 'Password is required');
                isValid = false;
            } else if (password.value.length < 6) {
                showError(password, 'Password must be at least 6 characters');
                isValid = false;
            }

            // Confirm password
            if (password.value !== confirmPassword.value) {
                showError(confirmPassword, 'Passwords do not match');
                isValid = false;
            }

            // Blood group
            if (!bloodGroup.value) {
                showError(bloodGroup, 'Please select your blood group');
                isValid = false;
            }

            // Phone validation
            const phoneRegex = /^01[3-9]\d{8}$/;
            if (!phone.value.trim()) {
                showError(phone, 'Phone number is required');
                isValid = false;
            } else if (!phoneRegex.test(phone.value.replace(/[-\s]/g, ''))) {
                showError(phone, 'Please enter a valid Bangladesh phone number');
                isValid = false;
            }

            // District
            if (!district.value) {
                showError(district, 'Please select your district');
                isValid = false;
            }

            // Age validation
            const ageVal = parseInt(age.value);
            if (!age.value) {
                showError(age, 'Age is required');
                isValid = false;
            } else if (ageVal < 18) {
                showError(age, 'You must be at least 18 years old to donate blood');
                isValid = false;
            } else if (ageVal > 65) {
                showError(age, 'Age must be 65 or below');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.form-control.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // ── Login Form Validation ──
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            clearErrors();
            let isValid = true;

            const email = document.getElementById('email');
            const password = document.getElementById('password');

            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            }

            if (!password.value) {
                showError(password, 'Password is required');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // ── Blood Request Form Validation ──
    const requestForm = document.getElementById('requestBloodForm');
    if (requestForm) {
        requestForm.addEventListener('submit', function(e) {
            clearErrors();
            let isValid = true;

            const patientName = document.getElementById('patient_name');
            const hospitalAddress = document.getElementById('hospital_address');
            const bloodGroup = document.getElementById('required_blood_group');
            const units = document.getElementById('units_needed');
            const dateNeeded = document.getElementById('date_needed');

            if (!patientName.value.trim()) {
                showError(patientName, 'Patient name is required');
                isValid = false;
            }

            if (!hospitalAddress.value.trim()) {
                showError(hospitalAddress, 'Hospital address is required');
                isValid = false;
            }

            if (!bloodGroup.value) {
                showError(bloodGroup, 'Please select required blood group');
                isValid = false;
            }

            if (!units.value || parseInt(units.value) < 1) {
                showError(units, 'At least 1 unit is required');
                isValid = false;
            }

            if (!dateNeeded.value) {
                showError(dateNeeded, 'Date needed is required');
                isValid = false;
            } else {
                const selectedDate = new Date(dateNeeded.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    showError(dateNeeded, 'Date must be today or in the future');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
                const firstError = document.querySelector('.form-control.error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // ── Contact Form Validation ──
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            clearErrors();
            let isValid = true;

            const name = document.getElementById('contact_name');
            const email = document.getElementById('contact_email');
            const message = document.getElementById('contact_message');

            if (!name.value.trim()) {
                showError(name, 'Name is required');
                isValid = false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                isValid = false;
            } else if (!emailRegex.test(email.value)) {
                showError(email, 'Please enter a valid email');
                isValid = false;
            }

            if (!message.value.trim()) {
                showError(message, 'Message is required');
                isValid = false;
            } else if (message.value.trim().length < 10) {
                showError(message, 'Message must be at least 10 characters');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // ── Profile Form Validation ──
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            clearErrors();
            let isValid = true;

            const name = document.getElementById('name');
            const phone = document.getElementById('phone');

            if (!name.value.trim() || name.value.trim().length < 3) {
                showError(name, 'Name must be at least 3 characters');
                isValid = false;
            }

            const phoneRegex = /^01[3-9]\d{8}$/;
            if (!phoneRegex.test(phone.value.replace(/[-\s]/g, ''))) {
                showError(phone, 'Please enter a valid phone number');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    }

    // ── Password Toggle ──
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = '🙈';
            } else {
                input.type = 'password';
                this.textContent = '👁️';
            }
        });
    });

    // ── Helper Functions ──
    function showError(element, message) {
        element.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
    }

    function clearErrors() {
        document.querySelectorAll('.form-control.error').forEach(el => {
            el.classList.remove('error');
        });
        document.querySelectorAll('.form-error').forEach(el => el.remove());
    }

    // ── Real-time password match ──
    const confirmPwd = document.getElementById('confirm_password');
    const pwd = document.getElementById('password');
    if (confirmPwd && pwd) {
        confirmPwd.addEventListener('input', function() {
            if (this.value && this.value !== pwd.value) {
                this.style.borderColor = 'var(--status-rejected)';
            } else if (this.value && this.value === pwd.value) {
                this.style.borderColor = 'var(--accent-green)';
            } else {
                this.style.borderColor = '';
            }
        });
    }

});
