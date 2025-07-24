import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

import Swal from "sweetalert2";
window.Swal = Swal;

// Global loading spinner handling
window.axios.interceptors.request.use(
    (config) => {
        const loader = document.getElementById("global-loader");
        if (loader) loader.style.display = "flex";
        return config;
    },
    (error) => {
        const loader = document.getElementById("global-loader");
        if (loader) loader.style.display = "none";
        return Promise.reject(error);
    }
);

window.axios.interceptors.response.use(
    (response) => {
        const loader = document.getElementById("global-loader");
        if (loader) loader.style.display = "none";
        return response;
    },
    (error) => {
        const loader = document.getElementById("global-loader");
        if (loader) loader.style.display = "none";
        return Promise.reject(error);
    }
);

window.storeOrUpdate = async (formId, event) => {
    event.preventDefault();

    const form = document.getElementById(formId);
    const url = form.action;
    const formData = new FormData(form);

    try {
        const response = await fetch(url, {
            method: "POST",
            body: formData,
        });

        if (!response.ok) {
            if (response.status === 422) {
                const data = await response.json();
                notyf.error(data.message);
                handleValidationErrors(data.errors);
            } else {
                notyf.error(response.statusText);
            }
            return;
        }

        const data = await response.json();

        if (data.success) {
            window.location.href = data.redirect;
            localStorage.setItem("success_message", data.message);
        } else {
            notyf.error(data.error);
        }
    } catch (error) {
        console.error("Error submitting employee edit form", error);
        notyf.error(error);
    }
};

window.handleValidationErrors = (errors) => {
    document
        .querySelectorAll(".border-red-500, .text-red-500")
        .forEach((el) => {
            el.classList.remove("border-red-500", "text-red-500");
        });

    document.querySelectorAll('[id$="-error"]').forEach((el) => {
        el.textContent = "";
    });

    for (const [field, messages] of Object.entries(errors)) {
        const inputElement = document.querySelector(`#${field}`);
        const errorElement = document.querySelector(`#${field}-error`);

        if (inputElement) {
            inputElement.classList.add("border-red-500");
        }

        if (errorElement) {
            errorElement.classList.add("text-red-500");
            errorElement.textContent = messages[0];
        }
    }
};

window.simpleResourceDelete = async function (resourceName, deleteUrl) {
    console.log(deleteUrl);

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const result = await Swal.fire({
        title: `Are you sure you want to delete "${resourceName}"?`,
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(deleteUrl, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                },
            });

            if (response.ok) {
                location.reload();
            } else {
                const errorText = await response.text();
                throw new Error(errorText || "Failed to delete the resource.");
            }
        } catch (error) {
            console.error(`An error occurred: ${error.message}`);
        }
    }
};
