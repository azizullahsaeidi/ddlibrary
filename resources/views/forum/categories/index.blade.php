@extends('layouts.main')

@section('title', __('Manage Categories'))

@section('content')
    <div class="container py-4" style="max-width: 860px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">@lang('Forum Categories')</h1>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">@lang('New Category')</a>
        </div>

        @if($categories->isEmpty())
            <div class="alert alert-light">@lang('No categories yet.')</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Description')</th>
                        <th>@lang('Parent')</th>
                        <th>@lang('Order')</th>
                        <th>@lang('Threads')</th>
                        <th>@lang('Private')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="text-muted small">{{ $category->description ?? '—' }}</td>
                            <td>{{ $category->parent?->name ?? '—' }}</td>
                            <td>{{ $category->order }}</td>
                            <td>{{ $category->threads_count }}</td>
                            <td>
                                @if($category->is_private)
                                    <span class="badge bg-warning text-dark">@lang('Yes')</span>
                                @else
                                    <span class="badge bg-light text-muted">@lang('No')</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">@lang('Edit')</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('{{ __('Delete this category?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">@lang('Delete')</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
