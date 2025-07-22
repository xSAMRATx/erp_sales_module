import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

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
