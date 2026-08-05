function showPreviewModal(title) {
    const label = document.getElementById('pdfPreviewModalLabel');
    if (label) {
        label.textContent = title || 'Pratinjau Dokumen';
    }

    const modalEl = document.getElementById('pdfPreviewModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

function resetPreviewModal() {
    const frame = document.getElementById('pdfPreviewFrame');
    const image = document.getElementById('pdfPreviewImage');

    if (frame) {
        frame.src = '';
        frame.classList.remove('d-none');
    }

    if (image) {
        image.src = '';
        image.classList.add('d-none');
    }
}

function openPdfPreview(url, title) {
    const frame = document.getElementById('pdfPreviewFrame');
    const image = document.getElementById('pdfPreviewImage');
    if (!frame) return;

    if (image) {
        image.src = '';
        image.classList.add('d-none');
    }

    frame.classList.remove('d-none');
    frame.src = url;
    showPreviewModal(title);
}

function openImagePreview(url, title) {
    const frame = document.getElementById('pdfPreviewFrame');
    const image = document.getElementById('pdfPreviewImage');
    if (!image) return;

    if (frame) {
        frame.src = '';
        frame.classList.add('d-none');
    }

    image.classList.remove('d-none');
    image.src = url;
    showPreviewModal(title);
}

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('pdfPreviewModal');
    if (!modalEl) return;
    modalEl.addEventListener('hidden.bs.modal', resetPreviewModal);
});
