// Admin Dashboard JavaScript

// Delete confirmation
function confirmDelete(type, id, name) {
    if (confirm(`Are you sure you want to delete this ${type}?\n\n${name}\n\nThis action cannot be undone.`)) {
        return true;
    }
    return false;
}

// AJAX delete handler
function deleteItem(type, id, callback) {
    if (!confirmDelete(type, id, '')) return;
    
    const url = type === 'user' ? 'delete_user.php' : 'delete_product.php';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (callback) callback();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete item'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the item');
    });
}

// Image preview for file uploads
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }
}

// Form validation
function validateProductForm() {
    const name = document.getElementById('name').value.trim();
    const price = document.getElementById('price').value;
    const stock = document.getElementById('stock').value;
    
    if (name === '') {
        alert('Product name is required');
        return false;
    }
    
    if (price <= 0) {
        alert('Price must be greater than 0');
        return false;
    }
    
    if (stock < 0) {
        alert('Stock cannot be negative');
        return false;
    }
    
    return true;
}

// Initialize tooltips and other UI enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Export to PDF helper
function exportToPDF(type) {
    window.location.href = `export_${type}_pdf.php`;
}

// Search/Filter table
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}
