@extends('layouts.main')

@section('title', __('Edit Thread'))

@section('content')
    <div class="container py-4" style="max-width: 760px;">
        <h1 class="h3 mb-4">@lang('Edit Thread')</h1>

        <form action="{{ route('threads.update', $thread) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="category_id" class="form-label">@lang('Category')</label>
                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $thread->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">@lang('Title')</label>
                <input type="text" name="title" id="title"
                       value="{{ old('title', $thread->title) }}"
                       class="form-control @error('title') is-invalid @enderror">
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="body" class="form-label">@lang('Body')</label>
                <textarea name="body" id="body" rows="8"
                          class="form-control @error('body') is-invalid @enderror">{{ old('body', $thread->body) }}</textarea>
                @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">@lang('Save Changes')</button>
                <a href="{{ route('threads.show', $thread) }}" class="btn btn-outline-secondary">@lang('Cancel')</a>
            </div>
        </form>
    </div>
@endsection
