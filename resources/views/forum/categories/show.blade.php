@extends('layouts.main')

@section('title', $category->name)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('threads.index') }}" class="text-muted text-decoration-none small">← @lang('Forum')</a>
                <h1 class="h3 mb-0 mt-1">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-muted mb-0">{{ $category->description }}</p>
                @endif
            </div>
            @auth
                <a href="{{ route('threads.create') }}" class="btn btn-primary">@lang('New Thread')</a>
            @endauth
        </div>

        @forelse($threads as $thread)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @if($thread->is_pinned)
                                <span class="badge bg-warning text-dark me-1">@lang('Pinned')</span>
                            @endif
                            @if($thread->is_locked)
                                <span class="badge bg-secondary me-1">@lang('Locked')</span>
                            @endif
                            <a href="{{ route('threads.show', $thread) }}" class="fw-bold text-decoration-none fs-5">
                                {{ $thread->title }}
                            </a>
                            <div class="text-muted small mt-1">
                                @lang('by') <strong>{{ $thread->user->username }}</strong>
                                · {{ $thread->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-end text-muted small">
                            <div>{{ $thread->posts()->count() }} @lang('replies')</div>
                            @if($thread->last_post_at)
                                <div>@lang('Last reply') {{ $thread->last_post_at->diffForHumans() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light">@lang('No threads in this category yet.')</div>
        @endforelse

        {{ $threads->links() }}
    </div>
@endsection
