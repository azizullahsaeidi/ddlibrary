@extends('layouts.main')

@section('title', __('New Thread'))

@section('content')
    <div class="container py-4" style="max-width: 760px;">
        <h1 class="h3 mb-4">@lang('New Thread')</h1>

        <form action="{{ route('threads.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="category_id" class="form-label">@lang('Category')</label>
                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="" disabled selected>@lang('Select a category')</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">@lang('Title')</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                       class="form-control @error('title') is-invalid @enderror" required>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="body" class="form-label">@lang('Body')</label>
                <textarea name="body" id="body" rows="8"
                          class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">@lang('Post Thread')</button>
                <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary">@lang('Cancel')</a>
            </div>
        </form>
    </div>
@endsection
