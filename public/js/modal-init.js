document.addEventListener("DOMContentLoaded", function () {
    // Initialize all modals
    const modals = {
        detail: new bootstrap.Modal(
            document.getElementById("detailRecordModal")
        ),
        edit: new bootstrap.Modal(document.getElementById("editRecordModal")),
        delete: new bootstrap.Modal(
            document.getElementById("deleteRecordModal")
        ),
        add: new bootstrap.Modal(document.getElementById("addRecordModal")),
    };

    // Show modal event listener
    Livewire.on("showModal", ({ id }) => {
        const modalName = id.replace("RecordModal", "");
        if (modals[modalName]) {
            modals[modalName].show();
        }
    });

    // Close modal event listener
    Livewire.on("closeModal", ({ id }) => {
        const modalName = id.replace("RecordModal", "");
        if (modals[modalName]) {
            modals[modalName].hide();
        }
    });

    // Add cleanup listeners
    Object.keys(modals).forEach((modalName) => {
        const modalElement = document.getElementById(`${modalName}RecordModal`);
        if (modalElement) {
            modalElement.addEventListener("hidden.bs.modal", function () {
                Livewire.dispatch("modalClosed", {
                    modalId: `${modalName}RecordModal`,
                });
            });
        }
    });
});
