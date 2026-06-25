@extends('layouts.main')

@section('title', __('Edit Category'))

@section('content')
    <div class="container py-4" style="max-width: 620px;">
        <h1 class="h3 mb-4">@lang('Edit Category')</h1>

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">@lang('Name')</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">@lang('Description')</label>
                <textarea name="description" id="description" rows="3"
                          class="form-control">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="parent_id" class="form-label">@lang('Parent Category')</label>
                <select name="parent_id" id="parent_id" class="form-select">
                    <option value="">@lang('None (top level)')</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="order" class="form-label">@lang('Display Order')</label>
                <input type="number" name="order" id="order"
                       value="{{ old('order', $category->order) }}" class="form-control" min="0">
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="hidden" name="is_private" value="0">
                    <input class="form-check-input" type="checkbox" name="is_private" id="is_private"
                           value="1" @checked(old('is_private', $category->is_private))>
                    <label class="form-check-label" for="is_private">@lang('Private (logged-in users only)')</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">@lang('Save Changes')</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">@lang('Cancel')</a>
            </div>
        </form>
    </div>
@endsection
