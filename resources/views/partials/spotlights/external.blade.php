<div class="container pt-3 text-center">
    <a href="{{ $spotlight->link_url }}" class="text-decoration-none">
        <div class="row align-items-center justify-content-between py-5">
            <div class="col-md-6">
                <h1 class="text-primary fw-bold text-start">{{ $spotlight->title }}</h1>
                @if($spotlight->subtitle)
                    <p class="text-muted text-start">{{ $spotlight->subtitle }}</p>
                @endif
            </div>
            <div class="col-md-5">
                <div class="card border-0">
                    @if($spotlight->image_path)
                        <img src="{{ getFile($spotlight->image_path) }}"
                             class="card-img-top rounded-0 w-100 object-fit-cover"
                             alt="{{ $spotlight->title }}"
                             style="height: 300px;">
                    @endif
                    @if($spotlight->link_text)
                        <div class="card-body rounded-bottom-4 bg-secondary text-center fw-bold py-2">
                            <h3 class="text-white">{{ $spotlight->link_text }}</h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>
