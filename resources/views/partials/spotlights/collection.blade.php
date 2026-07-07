<div class="container py-4">
    <div class="text-center mb-4">
        <span class="badge bg-warning text-dark mb-2">@lang('Collection')</span>
        <h2 class="fw-bold">{{ $spotlight->title }}</h2>
        @if($spotlight->subtitle)
            <p class="text-muted">{{ $spotlight->subtitle }}</p>
        @endif
    </div>
    <div class="row justify-content-center">
        @if($spotlight->image_path)
            <div class="col-md-6">
                <a href="{{ $spotlight->link_url }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm">
                        <img src="{{ getFile($spotlight->image_path) }}"
                             class="card-img-top object-fit-cover"
                             style="height: 250px;"
                             alt="{{ $spotlight->title }}">
                        <div class="card-body text-center bg-warning rounded-bottom">
                            <h5 class="fw-bold mb-0">{{ $spotlight->link_text ?? __('Browse Collection') }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        @else
            <div class="col-md-6 text-center">
                <a href="{{ $spotlight->link_url }}" class="btn btn-warning btn-lg">
                    {{ $spotlight->link_text ?? __('Browse Collection') }}
                </a>
            </div>
        @endif
    </div>
</div>
