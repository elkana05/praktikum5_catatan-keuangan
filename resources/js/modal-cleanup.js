document.addEventListener("DOMContentLoaded", function () {
    const modals = [
        "editRecordModal",
        "detailRecordModal",
        "deleteRecordModal",
    ];

    modals.forEach((modalId) => {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modalElement.addEventListener("hidden.bs.modal", function () {
                Livewire.dispatch("modalClosed", { modalId: modalId });
            });
        }
    });
});
