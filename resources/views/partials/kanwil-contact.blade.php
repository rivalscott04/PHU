@php
    use App\Support\KanwilContact;

    $kanwil = config('app.kanwil');
    $variant = $variant ?? 'footer-full';
@endphp

@if ($variant === 'footer-full')
    <footer id="footer" class="footer dark-background">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="footer-contact">
                        <h5 class="mb-4 text-white">{{ $kanwil['office_name'] }}</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <p class="mb-2 text-white-50">
                                    <i class="bi bi-geo-alt me-2"></i>{{ $kanwil['address'] }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2 text-white-50">
                                    <i class="bi bi-telephone me-2"></i>{{ $kanwil['phone'] }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2 text-white-50">
                                    <i class="bi bi-envelope me-2"></i>{{ $kanwil['email'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container text-center py-3 border-top border-secondary">
            <div class="row">
                <div class="col-12">
                    <p class="mb-2 text-white-50">
                        © <script>document.write(new Date().getFullYear())</script>
                        <strong class="text-white">{{ $kanwil['short_name'] }}</strong>. Hak cipta dilindungi.
                    </p>
                    <p class="mb-0 text-white-50 small">
                        Didesain dan dibuat dengan <i class="bi bi-heart-fill text-danger"></i>
                        oleh <strong class="text-white">{{ $kanwil['short_name'] }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </footer>
@elseif ($variant === 'footer-compact')
    <footer class="footer dark-background">
        <div class="container text-center py-3">
            <p class="mb-1 text-white-50">
                <i class="bi bi-geo-alt me-1"></i>{{ $kanwil['address'] }}
            </p>
            <p class="mb-1 text-white-50">
                <i class="bi bi-telephone me-1"></i>{{ $kanwil['phone'] }}
                <span class="mx-2">·</span>
                <i class="bi bi-envelope me-1"></i>{{ $kanwil['email'] }}
            </p>
            <p class="mb-0 text-white-50">
                © {{ date('Y') }}
                <strong class="text-white">{{ $kanwil['short_name'] }}</strong>
            </p>
        </div>
    </footer>
@elseif ($variant === 'form-sidebar')
    <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
        <i class="bi bi-geo-alt flex-shrink-0"></i>
        <div>
            <h3>Alamat Kantor</h3>
            <p>{{ $kanwil['address'] }}</p>
        </div>
    </div>

    <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
        <i class="bi bi-telephone flex-shrink-0"></i>
        <div>
            <h3>Telephone</h3>
            <p>{{ $kanwil['phone'] }}</p>
        </div>
    </div>

    <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
        <i class="bi bi-envelope flex-shrink-0"></i>
        <div>
            <h3>Email</h3>
            <p>{{ $kanwil['email'] }}</p>
        </div>
    </div>
@elseif ($variant === 'inline')
    <i class="bi bi-telephone me-1"></i>{{ $kanwil['phone'] }}<br>
    <i class="bi bi-envelope me-1"></i>{{ $kanwil['email'] }}<br>
    <i class="bi bi-geo-alt me-1"></i>{{ $kanwil['address'] }}
@elseif ($variant === 'support')
    @php
        $supportStyle = $supportStyle ?? 'inline';
        $supportPhone = KanwilContact::supportPhone();
        $supportEmail = KanwilContact::supportEmail();
        $phoneHref = 'tel:'.preg_replace('/\D+/', '', $supportPhone);
    @endphp

    @if ($supportStyle === 'card')
        <div class="mt-4 p-3 bg-light rounded text-center">
            <p class="text-muted mb-2 small">
                <i class="bx bx-support me-1"></i>Kendala login atau sistem?
            </p>
            <p class="mb-0 small">
                <a href="{{ $phoneHref }}" class="text-primary">{{ $supportPhone }}</a>
                <span class="text-muted mx-1">·</span>
                <a href="mailto:{{ $supportEmail }}" class="text-primary">{{ $supportEmail }}</a>
            </p>
        </div>
    @else
        <p class="mb-2 small text-muted">
            <i class="bx bx-support me-1"></i>Kendala sistem?
            <a href="{{ $phoneHref }}" class="text-muted">{{ $supportPhone }}</a>
            <span class="mx-1">·</span>
            <a href="mailto:{{ $supportEmail }}" class="text-muted">{{ $supportEmail }}</a>
        </p>
    @endif
@endif
