@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-sm-0">{{ $title }}</h4>
                <p class="text-muted mb-0 small">{{ $intro }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            @foreach ($links as $link)
                <a href="{{ $link['url'] }}" class="card border-0 shadow-sm mb-3 text-decoration-none text-body">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="{{ $link['icon'] }} fs-3 text-primary"></i>
                        <div>
                            <h5 class="mb-1">{{ $link['label'] }}</h5>
                            <p class="text-muted mb-0">{{ $link['hint'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
