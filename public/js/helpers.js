// Preview profile image
function previewProfileImage(input, type) {
    const container = document.getElementById(`profile-image-container-${type}`);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            container.innerHTML = `<img src="${e.target.result}" class="rounded-circle border-4 border-white shadow" style="width:100px;height:100px;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview banner image
function previewBanner(input, type) {
    const container = document.getElementById(`banner-container-${type}`);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Flash message pakai SweetAlert
function flashMessage(type, message) {
    Swal.fire({
        icon: type,
        title: type === 'success' ? 'Berhasil' : 'Gagal',
        html: message,
        timer: type === 'success' ? 2000 : null,
        showConfirmButton: true
    });
}