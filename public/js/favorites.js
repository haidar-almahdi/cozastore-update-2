// Favorite button functionality
document.addEventListener('DOMContentLoaded', function() {
    // Function to update favorite count
    function updateFavoriteCount() {
        fetch('/shop/favorites/count')
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll('.favorite-count').forEach(element => {
                    element.textContent = data.count;
                });
            });
    }

    // Function to toggle favorite button state
    function toggleFavoriteButton(button) {
        button.classList.toggle('active');
    }

    // Handle favorite buttons
    document.querySelectorAll('.favorite-btn').forEach(button => {
        if (button.dataset.productId) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;

                fetch(`/shop/favorites/${productId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'added' || data.status === 'removed') {
                        toggleFavoriteButton(this);
                        updateFavoriteCount();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    });

    // Initial favorite count update
    updateFavoriteCount();
});
