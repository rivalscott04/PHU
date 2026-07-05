<footer class="footer app-layout-footer">
    <div class="container-fluid footer-bottom">
        <p class="mb-2">
            © {{ date('Y') }}
            <strong>{{ config('app.kanwil.short_name') }}</strong>. Hak cipta dilindungi.
        </p>
        <p class="mb-2 small text-muted">
            <i class="bx bx-map me-1"></i>{{ config('app.kanwil.address') }}
        </p>
        <p class="mb-0 small text-muted">
            Didesain dan dibuat dengan <i class="bx bxs-heart text-danger"></i>
            oleh <strong class="text-body">{{ config('app.kanwil.short_name') }}</strong>
        </p>
    </div>
</footer>
