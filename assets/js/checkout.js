// Checkout Form JavaScript

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('bankSlip');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const removeFileBtn = document.getElementById('removeFile');
    const checkoutForm = document.getElementById('checkoutForm');

    // File upload click handler
    fileUploadArea.addEventListener('click', function () {
        fileInput.click();
    });

    // File input change handler
    fileInput.addEventListener('change', function (e) {
        handleFileSelect(e.target.files[0]);
    });

    // Drag and drop handlers
    fileUploadArea.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.style.borderColor = 'var(--color-primary)';
        fileUploadArea.style.backgroundColor = 'rgba(26, 60, 52, 0.05)';
    });

    fileUploadArea.addEventListener('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.style.borderColor = '#d0d0d0';
        fileUploadArea.style.backgroundColor = '#fafafa';
    });

    fileUploadArea.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.style.borderColor = '#d0d0d0';
        fileUploadArea.style.backgroundColor = '#fafafa';

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    // Handle file selection
    function handleFileSelect(file) {
        if (!file) return;

        // Validate file type
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            alert('Please upload a PNG or JPG image file.');
            return;
        }

        // Validate file size (5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            alert('File size must be less than 5MB.');
            return;
        }

        // Read and display the file
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            fileUploadArea.style.display = 'none';
            filePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Remove file handler
    removeFileBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        fileInput.value = '';
        previewImage.src = '';
        fileUploadArea.style.display = 'block';
        filePreview.style.display = 'none';
    });

    // Form submission handler
    checkoutForm.addEventListener('submit', function (e) {
        // Get form data
        const formData = new FormData(checkoutForm);

        // Basic validation
        const userName = formData.get('userName');
        const accountNumber = formData.get('accountNumber');
        const bank = formData.get('bank');
        const branch = formData.get('branch');
        const amount = formData.get('amount');
        const confirmationStatus = formData.get('confirmationStatus');

        if (!userName || !accountNumber || !bank || !branch || !amount || !confirmationStatus) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return;
        }

        // Validate amount is a number
        if (isNaN(amount) || parseFloat(amount) <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount.');
            return;
        }

        // Form is valid, allow it to submit to process_checkout.php
        // The form will submit normally to the action URL
        console.log('Form is valid, submitting to server...');
    });
});
