<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="row g-0">
            @if($spotlight->image_path)
                <div class="col-md-4">
                    <img src="{{ getFile($spotlight->image_path) }}"
                         class="img-fluid rounded-start h-100 object-fit-cover"
                         alt="{{ $spotlight->title }}">
                </div>
            @endif
            <div class="{{ $spotlight->image_path ? 'col-md-8' : 'col-12' }}">
                <div class="card-body d-flex flex-column justify-content-center h-100 p-4">
                    <span class="badge bg-success mb-2 align-self-start">@lang('Resource')</span>
                    <h2 class="card-title fw-bold">{{ $spotlight->title }}</h2>
                    @if($spotlight->subtitle)
                        <p class="card-text text-muted">{{ $spotlight->subtitle }}</p>
                    @endif
                    <a href="{{ $spotlight->link_url }}" class="btn btn-success align-self-start mt-2">
                        {{ $spotlight->link_text ?? __('View Resource') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
