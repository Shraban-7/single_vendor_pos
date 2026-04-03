class AuthManager {
    constructor() {
        this.loginApiUrl = "/auth/login";
        this.registerApiUrl = "/auth/register";
        this.csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        this.init();
    }

    init() {
        this.setupLoginForm();
        this.setupRegisterForm();
    }

    getHeaders() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": this.csrfToken,
            "X-Requested-With": "XMLHttpRequest",
        };
    }

    setupLoginForm() {
        const loginForm = document.getElementById("loginFormElement");
        if (!loginForm) return;

        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await this.handleLogin(loginForm);
        });
    }

    setupRegisterForm() {
        const registerForm = document.getElementById("signupFormElement");
        if (!registerForm) return;

        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            await this.handleRegister(registerForm);
        });
    }

    async handleLogin(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            // Disable submit button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';

            const formData = new FormData(form);
            const data = {
                phone: formData.get("phone"),
                password: formData.get("password"),
                remember: formData.get("remember") ? true : false,
            };

            const response = await fetch(this.loginApiUrl, {
                method: "POST",
                headers: this.getHeaders(),
                credentials: "same-origin",
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                if (window.showSuccess) {
                    window.showSuccess(result.message || "Login successful!");
                }

                // Close modal
                if (typeof closeAuthModal === "function") {
                    closeAuthModal();
                }

                // Redirect or reload page
                setTimeout(() => {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                if (window.showError) {
                    window.showError(result.message || "Login failed!");
                }

                // Show field-specific errors if available
                if (result.errors) {
                    this.displayFormErrors(form, result.errors);
                }
            }
        } catch (error) {
            console.error("Login error:", error);
            if (window.showError) {
                window.showError(
                    "An error occurred during login. Please try again.",
                );
            }
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    async handleRegister(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            // Disable submit button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...';

            const formData = new FormData(form);

            // Check if passwords match
            const password = formData.get("password");
            const passwordConfirmation = formData.get("password_confirmation");

            if (password !== passwordConfirmation) {
                if (window.showError) {
                    window.showError("Passwords do not match!");
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                return;
            }

            const data = {
                name: formData.get("name"),
                phone: formData.get("phone"),
                password: password,
                password_confirmation: passwordConfirmation,
                terms: formData.get("terms") ? 1 : 0,
            };

            const response = await fetch(this.registerApiUrl, {
                method: "POST",
                headers: this.getHeaders(),
                credentials: "same-origin",
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                if (window.showSuccess) {
                    window.showSuccess(
                        result.message || "Account created successfully!",
                    );
                }

                // Close modal
                if (typeof closeAuthModal === "function") {
                    closeAuthModal();
                }

                // Redirect or reload page
                setTimeout(() => {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                if (window.showError) {
                    window.showError(result.message || "Registration failed!");
                }

                // Show field-specific errors if available
                if (result.errors) {
                    this.displayFormErrors(form, result.errors);
                }
            }
        } catch (error) {
            console.error("Registration error:", error);
            if (window.showError) {
                window.showError(
                    "An error occurred during registration. Please try again.",
                );
            }
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    displayFormErrors(form, errors) {
        // Clear previous error messages
        form.querySelectorAll(".error-message").forEach((el) => el.remove());

        // Display new error messages
        Object.keys(errors).forEach((fieldName) => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const errorDiv = document.createElement("div");
                errorDiv.className = "error-message text-xs text-red-500 mt-1";
                errorDiv.textContent = errors[fieldName][0];
                field.parentElement.appendChild(errorDiv);

                // Add error styling to input
                field.classList.add("border-red-500", "focus:ring-red-500");
            }
        });
    }

    clearFormErrors(form) {
        form.querySelectorAll(".error-message").forEach((el) => el.remove());
        form.querySelectorAll("input").forEach((input) => {
            input.classList.remove("border-red-500", "focus:ring-red-500");
        });
    }
}

// Initialize AuthManager when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        window.authManager = new AuthManager();
    });
} else {
    window.authManager = new AuthManager();
}
