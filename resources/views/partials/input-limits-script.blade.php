@php
    $inputLimitsVersion = filemtime(public_path('js/input-limits.js'));
@endphp
<script>
    window.PHU_INPUT_LIMITS = @json(\App\Helpers\ValidationHelper::inputLimitConfig());
</script>
<script src="{{ asset('js/input-limits.js') }}?v={{ $inputLimitsVersion }}"></script>
