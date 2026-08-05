<!-- Reusable PDF preview modal. Trigger from anywhere with openPdfPreview(url, title). -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="pdfPreviewModalLabel">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="height:75vh;min-height:420px;">
                <iframe id="pdfPreviewFrame" src="" title="Pratinjau PDF" style="width:100%;height:100%;border:0;"></iframe>
                <img id="pdfPreviewImage" src="" alt="Pratinjau dokumen" class="d-none" style="max-width:100%;max-height:100%;object-fit:contain;">
            </div>
        </div>
    </div>
</div>
