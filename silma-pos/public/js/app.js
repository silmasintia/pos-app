document.addEventListener("DOMContentLoaded", function () {
    document
        .querySelectorAll("form:not(#addUserForm):not(#editUserForm)")
        .forEach((form) => {
            form.addEventListener("submit", function () {
                const btn = form.querySelector(".btn-submit");
                if (btn) {
                    btn.disabled = true;
                    const spinner = btn.querySelector(".spinner-border");
                    const text = btn.querySelector(".btn-text");
                    const loadingText =
                        btn.getAttribute("data-loading-text") ||
                        "Processing...";
                    if (spinner) spinner.classList.remove("d-none");
                    if (text) text.textContent = loadingText;
                }
            });
        });
});

function previewImage(input, containerId, customClass = "") {
    const container = document.getElementById(containerId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const imgClass = customClass || "w-100 h-100";
            container.innerHTML = `<img src="${e.target.result}" class="${imgClass}" style="object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewProfileImage(input, type) {
    previewImage(
        input,
        `profile-image-container-${type}`,
        "rounded-circle border-4 border-white shadow"
    );
}

function previewBanner(input, type) {
    previewImage(input, `banner-container-${type}`);
}

function flashMessage(type, message) {
    if (typeof Swal === "undefined") {
        console.error("SweetAlert2 is not loaded");
        alert(message);
        return;
    }

    Swal.fire({
        icon: type,
        title:
            type === "success"
                ? "Success"
                : type === "error"
                ? "Error"
                : "Info",
        text: message,
        timer: type === "success" ? 2000 : null,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK",
    });
}

function confirmDialog(
    title,
    text,
    confirmButtonText = "Yes",
    cancelButtonText = "Cancel"
) {
    if (typeof Swal === "undefined") {
        console.error("SweetAlert2 is not loaded");
        return new Promise((resolve) => {
            const result = confirm(text);
            resolve({ isConfirmed: result });
        });
    }

    return Swal.fire({
        title: title,
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    });
}

function alertDialog(type, title, html = null) {
    if (typeof Swal === "undefined") {
        console.error("SweetAlert2 is not loaded");
        alert(title);
        return Promise.resolve();
    }

    return Swal.fire({
        icon: type,
        title: title,
        html: html,
        timer: type === "success" ? 2000 : null,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK",
    });
}

function handleApiResponse(response) {
    return response.text().then((text) => {
        let data = {};
        try {
            data = text ? JSON.parse(text) : {};
        } catch (e) {
            console.error("Server returned non-JSON:", text);
            throw new Error("Server returned invalid response format.");
        }

        if (response.ok) {
            return data;
        } else {
            let message = data.message || "An error occurred";

            if (data.errors) {
                message = Object.values(data.errors).flat().join("<br>");
            }

            if (response.status === 422) {
                throw new Error(message || "Validation failed.");
            }

            throw new Error(message);
        }
    });
}
const logoutLink = document.getElementById("logout-link");
const logoutForm = document.getElementById("logout-form-sidebar");

if (logoutLink && logoutForm) {
    logoutLink.addEventListener("click", function (e) {
        e.preventDefault();

        confirmDialog(
            "Logout",
            "Apakah kamu yakin ingin keluar dari akun ini?",
            "Ya, Logout",
            "Batal"
        ).then((result) => {
            if (result.isConfirmed) {
                logoutForm.submit();
            }
        });
    });
}
