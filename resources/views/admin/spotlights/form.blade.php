@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ URL::to('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.spotlights.index') }}">Spotlights</a></li>
                <li class="breadcrumb-item active">{{ $spotlight->id ? 'Edit' : 'Create' }}</li>
            </ol>
            @include('layouts.messages')
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-star"></i>
                    {{ $spotlight->id ? 'Edit Spotlight' : 'Create Spotlight' }}
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="{{ $spotlight->id ? route('admin.spotlights.update', $spotlight) : route('admin.spotlights.store') }}">
                        @csrf
                        @if($spotlight->id)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $spotlight->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $spotlight->subtitle) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image Path</label>
                            <input type="text" name="image_path" class="form-control" value="{{ old('image_path', $spotlight->image_path) }}"
                                   placeholder="e.g. public/img/my-image.png">
                            <div class="form-text">S3 path to the image.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link URL <span class="text-danger">*</span></label>
                            <input type="text" name="link_url" class="form-control" value="{{ old('link_url', $spotlight->link_url) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Text</label>
                            <input type="text" name="link_text" class="form-control" value="{{ old('link_text', $spotlight->link_text) }}"
                                   placeholder="e.g. Read more, View resource">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                @foreach(['news', 'resource', 'external', 'collection'] as $type)
                                    <option value="{{ $type }}" {{ old('type', $spotlight->type) === $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Starts At</label>
                                <input type="date" name="starts_at" class="form-control"
                                       value="{{ old('starts_at', $spotlight->starts_at?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ends At</label>
                                <input type="date" name="ends_at" class="form-control"
                                       value="{{ old('ends_at', $spotlight->ends_at?->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', $spotlight->sort_order ?? 0) }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
                                        {{ old('active', $spotlight->active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $spotlight->id ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('admin.spotlights.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
