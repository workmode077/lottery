$(function () {
    // CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    /**
     * Attach a click event handler to delete buttons
     */
    $("body").on("click", ".delete-btn", function (event) {
        event.preventDefault();

        const $button = $(this);
        const tableId = $button.closest("table").attr("id"); // Get the DataTable ID

        const deleteMessage =
            $button.data("delete-message-type") === "itemWithRelated"
                ? "This will delete the item and its related items!"
                : "This will delete the item!";

        Swal.fire({
            title: "Are you sure?",
            text: deleteMessage,
            icon: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#2ab57d",
            cancelButtonColor: "#fd625e",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $button.prop("disabled", true);

                $.ajax({
                    url: $button.closest("form").attr("action"),
                    type: "POST",
                    data: $button.closest("form").serialize(),
                    success: function (response) {
                        showToast(
                            response.message,
                            response.success ? "success" : "error"
                        );
                        if (response.success)
                            $("#" + tableId)
                                .DataTable()
                                .ajax.reload(null, false); // Preserve pagination
                    },
                    error: function (xhr) {
                        showToast(
                            "An error occurred. Please try again.",
                            "error"
                        );
                        $button.prop("disabled", false);
                        console.error(
                            "Unexpected error:",
                            xhr.responseText || xhr.statusText
                        );
                    },
                });
            }
        });
    });

    /**
     * Manages the input and change events for elements with the class `.sort-order`,
     * ensuring that only valid numeric input is processed and updating the server with
     * the new sort order value via AJAX with a debounce mechanism.
     */
    let sortOrderDebounceTimer;

    $(document).on("input change", ".sort-order", function (event) {
        const $this = $(this);
        let inputValue = $this.val().replace(/[^\d]/g, ""); // Remove non-digit characters
        let value = Math.max(
            0,
            Math.min(parseInt(inputValue, 10) || 0, 2147483647)
        ); // Validate and limit range
        $this.val(value);

        // Only trigger AJAX request on change event
        if (event.type === "change") {
            clearTimeout(sortOrderDebounceTimer);

            sortOrderDebounceTimer = setTimeout(() => {
                $.ajax({
                    url: `/${prefix}/admin-settings/update-sort-order`,
                    type: "POST",
                    data: {
                        id: $this.data("id"),
                        model: $this.data("model"),
                        value: value,
                    },
                    success(response) {
                        showToast(
                            response.success
                                ? "Sort Order Updated successfully"
                                : "Failed to Update Sort Order",
                            response.success ? "success" : "error"
                        );
                    },
                    error: function (xhr) {
                        showToast(
                            "An error occurred while updating Sort Order",
                            "error"
                        );
                        console.error(
                            "Unexpected error:",
                            xhr.responseText || xhr.statusText
                        );
                    },
                });
            }, 300); // Debounce time
        }
    });

    /**
     * Handles toggle switch changes with debouncing to prevent rapid AJAX requests.
     */
    var toggleSwitchDebounceTimer;

    $(document).on("change", ".toggle-switch", function () {
        clearTimeout(toggleSwitchDebounceTimer);

        const $this = $(this);
        const value = $this.is(":checked") ? 1 : 0;
        const { model, column, id, labels, name } = $this.data();
        const [labelSuccess, labelFailed] = labels.split(";");
        const $label = $this.siblings("label");

        $label.text(value === 1 ? labelSuccess : labelFailed);

        toggleSwitchDebounceTimer = setTimeout(() => {
            $.ajax({
                url: `/${prefix}/admin-settings/update-toggle-status`,
                type: "POST",
                data: { id, model, column, value },
                success(response) {
                    if (response.success)
                        showToast(`${name} updated successfully`, "success");
                    else
                        revertToggleSwitch(
                            $this,
                            value,
                            labelFailed,
                            labelSuccess,
                            name
                        );
                },
                error: function (xhr) {
                    revertToggleSwitch(
                        $this,
                        value,
                        labelFailed,
                        labelSuccess,
                        name
                    );
                    console.error(
                        "Unexpected error:",
                        xhr.responseText || xhr.statusText
                    );
                },
            });
        }, 300);
    });

    function revertToggleSwitch(
        $element,
        value,
        labelFailed,
        labelSuccess,
        name
    ) {
        $element.prop("checked", !value);
        $element
            .siblings("label")
            .text(value === 1 ? labelFailed : labelSuccess);
        showToast(`Failed to Update ${name}`, "error");
    }

    /**
     * Toggles 'scroll-active' class on '.fixed-action-card' based on window scroll position (80px threshold).
     */
    const actionCard = $(".fixed-action-card");
    const scrollPoint = 80;

    $(window).scroll(function () {
        if ($(this).scrollTop() > scrollPoint) {
            actionCard.addClass("scroll-active");
        } else {
            actionCard.removeClass("scroll-active");
        }
    });
});

// JavaScript to handle image input changes and toggle the 'required' attribute on the alt text input

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.img-input').forEach(function(imgInput) {
        const altInputId = imgInput.id + '_alt';
        const altInput = document.getElementById(altInputId);

        function toggleRequired() {
            if (imgInput.files.length > 0) {
                altInput?.setAttribute('required', 'required');
            } else {
                altInput?.removeAttribute('required');
            }
        }

        imgInput.addEventListener('change', toggleRequired);
        toggleRequired(); // Run on load
    });
});

/**
 * Displays a toast notification using Toastr.
 */
function showToast(message, type = "success", options = {}) {
    toastr.options = {
        closeButton: true,
        progressBar: false,
        positionClass: "toast-top-right",
        timeOut: 5000,
        extendedTimeOut: 2000,
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
        preventDuplicates: true,
        newestOnTop: true,
        escapeHtml: false,
        closeEasing: "swing",
        showEasing: "swing",
        hideEasing: "linear",
        tapToDismiss: true,
        onHidden: null,
        debug: false,
        ...options,
    };

    // Ensure a valid toast type is used
    const validTypes = ["success", "error", "warning", "info"];
    const toastType = validTypes.includes(type) ? type : "info";

    // Display the toast notification
    toastr[toastType](message);
}
