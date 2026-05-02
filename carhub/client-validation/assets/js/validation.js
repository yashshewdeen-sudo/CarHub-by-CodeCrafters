// assets/js/validation.js
document.addEventListener("DOMContentLoaded", () => {

    function fail(form, msg) {
        alert(msg);
        return false;
    }

    // Register
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", (e) => {
            const email = registerForm.email.value.trim();
            const phone = registerForm.phone.value.trim();
            const pw    = registerForm.password.value.trim();
            const pw2   = registerForm.confirm_password ? registerForm.confirm_password.value.trim() : pw;

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
                e.preventDefault(); return fail(registerForm, "Enter a valid email address (e.g. name@example.com).");
            }

            if (!/^\+?[0-9]{8,15}$/.test(phone.replace(/[\s\-]/g, ''))) {
                e.preventDefault(); return fail(registerForm, "Phone must be 8-15 digits (numbers only, e.g. 57123456).");
            }

            if (pw.length < 8 || !/[A-Za-z]/.test(pw) || !/[0-9]/.test(pw)) {
                e.preventDefault(); return fail(registerForm, "Password: min 8 chars with letters and numbers.");
            }
            if (pw !== pw2) {
                e.preventDefault(); return fail(registerForm, "Passwords do not match.");
            }
        });
    }

    // Login
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            const email = loginForm.email.value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
                e.preventDefault(); return fail(loginForm, "Enter a valid email address.");
            }
        });
    }

    // Listing
    const listingForm = document.getElementById("listingForm");
    if (listingForm) {
        listingForm.addEventListener("submit", (e) => {
            const year = parseInt(listingForm.year.value, 10);
            const yearMax = new Date().getFullYear() + 1;
            if (isNaN(year) || year < 1900 || year > yearMax) {
                e.preventDefault(); return fail(listingForm, `Year must be 1900-${yearMax}.`);
            }
            if (parseFloat(listingForm.price.value) <= 0) {
                e.preventDefault(); return fail(listingForm, "Price must be greater than 0.");
            }
            if (parseInt(listingForm.mileage.value, 10) < 0) {
                e.preventDefault(); return fail(listingForm, "Mileage cannot be negative.");
            }
            const files = listingForm["images[]"].files;
            if (!files.length) {
                e.preventDefault(); return fail(listingForm, "At least one image required.");
            }
            if (files.length > 5) {
                e.preventDefault(); return fail(listingForm, "Max 5 images.");
            }
            for (const f of files) {
                if (!f.type.startsWith("image/")) {
                    e.preventDefault(); return fail(listingForm, "Only image files allowed.");
                }
                if (f.size > 5 * 1024 * 1024) {
                    e.preventDefault(); return fail(listingForm, "Each image must be under 5MB.");
                }
            }
        });
    }
});