// Product Edit Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productUpdateForm');
    const submitBtn = document.getElementById('updateBtn');

    if (!form || !submitBtn) {
        console.error('Form or submit button not found');
        return;
    }

    // Form submission handler
    form.addEventListener('submit', function(e) {
        console.log('Form submission started...');

        // Prevent double submission
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }

        // Show loading state
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;

        // Log form data for debugging
        const formData = new FormData(form);
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Validate required fields
        const requiredFields = ['name', 'price', 'stock', 'category_id'];
        let hasErrors = false;

        requiredFields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input && !input.value.trim()) {
                console.error(`Required field missing: ${field}`);
                hasErrors = true;
            }
        });

        if (hasErrors) {
            alert('Please fill in all required fields');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            e.preventDefault();
            return;
        }

        // Reset button after timeout (in case of no response)
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                console.warn('Form submission timeout - button reset');
            }
        }, 15000);
    });

    // Handle form response (if using AJAX in the future)
    window.handleFormResponse = function(response) {
        if (response.success) {
            console.log('Form submitted successfully');
            if (response.redirect) {
                window.location.href = response.redirect;
            }
        } else {
            console.error('Form submission failed:', response.message);
            alert('Error: ' + response.message);

            // Reset button
            submitBtn.innerHTML = '<i class="fa fa-save"></i> Update Product';
            submitBtn.disabled = false;
        }
    };

    // Validate form before submission
    window.validateForm = function() {
        const name = form.querySelector('[name="name"]').value.trim();
        const price = form.querySelector('[name="price"]').value;
        const stock = form.querySelector('[name="stock"]').value;
        const categoryId = form.querySelector('[name="category_id"]').value;

        if (!name) {
            alert('Product name is required');
            return false;
        }

        if (!price || price <= 0) {
            alert('Valid price is required');
            return false;
        }

        if (!stock || stock < 0) {
            alert('Valid stock quantity is required');
            return false;
        }

        if (!categoryId) {
            alert('Category selection is required');
            return false;
        }

        return true;
    };

    console.log('Product edit form handler initialized');
});
