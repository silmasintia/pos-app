document.addEventListener("DOMContentLoaded", function () {
    let profileData = {};

    loadProfileData();

    $("#save-profile-btn").on("click", updateProfile);

    $("#reset-btn").on("click", () => {
        confirmDialog("Reset Changes", "Are you sure you want to reset all changes?")
            .then((result) => {
                if (result.isConfirmed) loadProfileData();
            });
    });

    const imageInputs = ["logo", "logo_dark", "favicon", "banner", "login_background"];
    imageInputs.forEach((id) => {
        $(`#${id}`).on("change", function () {
            previewImage(this, `${id.replace("_", "-")}-new-preview`);
        });
        
        $(`#remove_${id}`).on("change", function () {
            if (this.checked) {
                $(`#${id.replace("_", "-")}-new-preview`).html('<i class="fas fa-image fa-2x text-secondary"></i>');
                $(`#${id}`).val('');
            }
        });
    });

    function loadProfileData() {
        fetch("/profile/data")
            .then(handleApiResponse)
            .then((data) => {
                profileData = data.profile || {};
                fillProfileForm(profileData);
            })
            .catch((error) => {
                console.error("Error:", error);
                flashMessage("error", "Error loading profile data. Please try again.");
            });
    }

    function fillProfileForm(profile) {
        $("#profile-id").val(profile.id || "");
        $("#profile_name").val(profile.profile_name || "");
        $("#alias").val(profile.alias || "");
        $("#identity_number").val(profile.identity_number || "");
        $("#address").val(profile.address || "");
        $("#phone_number").val(profile.phone_number || "");
        $("#whatsapp_number").val(profile.whatsapp_number || "");
        $("#email").val(profile.email || "");
        $("#website").val(profile.website || "");
        $("#description_1").val(profile.description_1 || "");
        $("#description_2").val(profile.description_2 || "");
        $("#description_3").val(profile.description_3 || "");

        $("#theme").val(profile.theme || "default");
        $("#theme_color").val(profile.theme_color || "default");
        $("#sidebar_type").val(profile.sidebar_type || "default");
        $("#direction").val(profile.direction || "ltr");
        $("#boxed_layout").prop("checked", !!profile.boxed_layout);
        $("#card_border").prop("checked", !!profile.card_border);

        renderImagePreview("logo", profile.logo);
        renderImagePreview("logo_dark", profile.logo_dark);
        renderImagePreview("favicon", profile.favicon);
        renderImagePreview("banner", profile.banner);
        renderImagePreview("login_background", profile.login_background);

        $("#keyword").val(profile.keyword || "");
        $("#keyword_description").val(profile.keyword_description || "");
        $("#embed_youtube").val(profile.embed_youtube || "");
        $("#embed_map").val(profile.embed_map || "");
    }

    function updateProfile() {
        const formData = new FormData();
        const forms = ["#profile-form", "#appearance-form", "#seo-form"];

        const btn = $("#save-profile-btn");
        const spinner = btn.find(".spinner-border");
        const btnText = btn.find(".btn-text");
        
        spinner.removeClass("d-none");
        btnText.text("Updating...");
        btn.prop("disabled", true);

        forms.forEach((selector) => {
            const form = $(selector)[0];
            if (!form) return;

            const inputs = form.querySelectorAll("input, select, textarea");
            inputs.forEach((input) => {
                if (!input.name) return;

                if (input.type === "checkbox") {
                    formData.append(input.name, input.checked ? 1 : 0);
                } else if (input.type === "file" && input.files.length > 0) {
                    formData.append(input.name, input.files[0]);
                } else {
                    formData.append(input.name, input.value);
                }
            });
        });

        const profileId = $("#profile-id").val() || 1;
        const url = profileUpdateUrl.replace(":id", profileId);

        formData.append("_method", "PUT");

        fetch(url, {
            method: "POST",
            body: formData,
            headers: { "X-CSRF-TOKEN": csrfToken },
        })
            .then(handleApiResponse)
            .then((data) => {
                if (data.success) {
                    flashMessage("success", "Profile updated successfully");
                    loadProfileData();
                } else {
                    showValidationErrors(data.errors, data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                flashMessage("error", "Error updating profile. Please try again.");
            })
            .finally(() => {
                spinner.addClass("d-none");
                btnText.text("Update Profile");
                btn.prop("disabled", false);
            });
    }

    function renderImagePreview(field, filePath) {
        const currentPreviewContainer = $(`#${field.replace("_", "-")}-current-preview`);
        const newPreviewContainer = $(`#${field.replace("_", "-")}-new-preview`);

        if (filePath) {
            const imageUrl = `${window.location.origin}/storage/${filePath}`;
            currentPreviewContainer.html(
                `<img src="${imageUrl}" class="w-100 h-100" style="object-fit:cover;">`
            );
        } else {
            currentPreviewContainer.html('<i class="fas fa-image fa-2x text-secondary"></i>');
        }
        
        newPreviewContainer.html('<i class="fas fa-image fa-2x text-secondary"></i>');
    }

    function previewImage(input, previewId) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();

        reader.onload = (e) => {
            const container = document.getElementById(previewId);
            container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;">`;
        };

        reader.readAsDataURL(input.files[0]);
    }

    function showValidationErrors(errors, fallbackMessage) {
        let errorMessage = "Please fix the following errors:<br>";
        if (errors) {
            for (const [key, value] of Object.entries(errors)) {
                errorMessage += `- ${value[0]}<br>`;
            }
        } else {
            errorMessage = fallbackMessage || "Unknown validation error occurred";
        }
        alertDialog("error", "Validation Error", errorMessage);
    }
});