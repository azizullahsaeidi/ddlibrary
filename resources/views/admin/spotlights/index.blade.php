@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ URL::to('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Spotlights</li>
            </ol>
            @include('layouts.messages')
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-star"></i> Spotlights</div>
                    <a href="{{ route('admin.spotlights.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Spotlight
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Active</th>
                                <th>Starts At</th>
                                <th>Ends At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spotlights as $spotlight)
                            <tr>
                                <td>{{ $spotlight->sort_order }}</td>
                                <td>{{ $spotlight->title }}</td>
                                <td><span class="badge bg-light">{{ $spotlight->type }}</span></td>
                                <td>
                                    @if($spotlight->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $spotlight->starts_at?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $spotlight->ends_at?->format('Y-m-d') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.spotlights.edit', $spotlight) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.spotlights.destroy', $spotlight) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No spotlights yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
