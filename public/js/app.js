function showSpinnerOnButton(button) {
    if (!button) return;
    
    const $btn = $(button);
    const spinner = $btn.find('.spinner-border');
    const text = $btn.find('.btn-text');
    const icon = $btn.find('i:not(.spinner-border)');
    const loadingText = $btn.attr('data-loading-text') || 'Processing...';
    
    $btn.prop('disabled', true);
    if (spinner.length) spinner.removeClass('d-none');
    if (text.length) text.text(loadingText);
    if (icon.length) icon.addClass('d-none');
}

function hideSpinnerOnButton(button) {
    if (!button) return;
    
    const $btn = $(button);
    const spinner = $btn.find('.spinner-border');
    const text = $btn.find('.btn-text');
    const icon = $btn.find('i:not(.spinner-border)');
    const originalText = $btn.attr('data-original-text') || 'Submit';
    
    $btn.prop('disabled', false);
    if (spinner.length) spinner.addClass('d-none');
    if (text.length) text.text(originalText);
    if (icon.length) icon.removeClass('d-none');
}

function resetFormSpinner(form) {
    const button = $(form).find('.btn-submit');
    if (button.length) {
        hideSpinnerOnButton(button[0]);
    }
}

function handleAjaxWithSpinner(url, method, data, button, successCallback, errorCallback) {
    showSpinnerOnButton(button);
    
    $.ajax({
        url: url,
        method: method,
        data: data,
        success: function(response) {
            if (successCallback) successCallback(response);
        },
        error: function(xhr) {
            let errorMsg = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMsg = Object.values(errors).flat().join('<br>');
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            
            if (errorCallback) {
                errorCallback(errorMsg);
            } else {
                alertDialog('error', 'Error', errorMsg);
            }
        },
        complete: function() {
            hideSpinnerOnButton(button);
        }
    });
}

function showSpinnerOnEditButton(button) {
    if (!button) return;
    
    const $btn = $(button);
    const originalHtml = $btn.html();
    
    $btn.attr('data-original-html', originalHtml);
    
    $btn.prop('disabled', true);
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
}

function restoreEditButton(button) {
    if (!button) return;
    
    const $btn = $(button);
    const originalHtml = $btn.attr('data-original-html');
    
    $btn.prop('disabled', false);
    if (originalHtml) {
        $btn.html(originalHtml);
    }
}

function confirmDeleteWithSpinner(button, deleteUrl, itemName, successCallback, errorCallback) {
    if (!button) return;
    
    const $btn = $(button);
    const originalHtml = $btn.html();
   
    $btn.attr('data-original-html', originalHtml);   
    $btn.prop('disabled', true);
    $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    
    const restoreButton = function() {
        $btn.prop('disabled', false);
        if (originalHtml) {
            $btn.html(originalHtml);
        }
    };
    
    const title = 'Delete Confirmation';
    let text = 'Are you sure you want to delete this item?';
    let confirmButtonText = 'Yes, Delete';
    
    if (itemName) {
        text = `Are you sure you want to delete "${itemName}"? This action cannot be undone.`;
        confirmButtonText = `Yes, Delete!`;
    }
    
    confirmDialog(
        title,
        text,
        confirmButtonText,
        'Cancel'
    ).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                data: { _token: csrfToken },
                success: function(response) {
                    if (successCallback) successCallback(response);
                },
                error: function(xhr) {
                    let errorMsg = 'Error deleting item';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    
                    if (errorCallback) {
                        errorCallback(errorMsg);
                    } else {
                        alertDialog('error', 'Error', errorMsg);
                    }
                },
                complete: function() {
                    restoreButton();
                }
            });
        } else {
            restoreButton();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    document
        .querySelectorAll("form.form-spinner")
        .forEach((form) => {
            form.addEventListener("submit", function () {
                const btn = form.querySelector(".btn-submit");
                if (btn) {
                    const $btn = $(btn);
                    const spinner = $btn.find(".spinner-border");
                    const text = $btn.find(".btn-text");
                    const icon = $btn.find('i:not(.spinner-border)');
                    const loadingText = $btn.attr("data-loading-text") || "Processing...";
                    
                    $btn.prop('disabled', true);
                    if (spinner.length) spinner.removeClass("d-none");
                    if (text.length) text.text(loadingText);
                    if (icon.length) icon.addClass('d-none');
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
            "Are you sure you want to log out from this account?",
            "Yes, Logout",
            "Cancel"
        ).then((result) => {
            if (result.isConfirmed) {
                logoutForm.submit();
            }
        });
    });
}