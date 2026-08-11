@once
    @push('styles')
        {{-- Bootstrap 5.1 belum punya utilitas *-subtle, jadi chip PPIU didefinisikan sendiri. --}}
        <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
        <style>
            .badge-ppiu {
                background-color: #eaeefc;
                color: #3549a5;
                border: 1px solid #c6d0f2;
                font-weight: 600;
            }

            .select2-container .select2-selection--single {
                height: calc(1.5em + 0.75rem + 2px);
                border-color: #ced4da;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: calc(1.5em + 0.75rem);
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: calc(1.5em + 0.75rem);
            }
        </style>
    @endpush

    @push('js')
        <script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
    @endpush
@endonce
