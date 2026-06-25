@extends('layouts.main')

@section('title', __('Forum'))

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="h3 mb-0">@lang('Forum')</h3>
                <p class="text-muted mb-0">@lang('Ask, share, and learn together')</p>
            </div>
            @auth
                <a href="{{ route('threads.create') }}" class="btn btn-primary me-1">@lang('New Thread')</a>
                @if(isAdmin())
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">
                        @lang('Manage Categories')
                    </a>
                @endif
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
                                @lang('in')
                                <a href="{{ route('threads.categories.show', $thread->category) }}">
                                    {{ $thread->category->name }}
                                </a>
                                · {{ $thread->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-end text-muted small">
                            <div>{{ $thread->posts_count ?? $thread->posts()->count() }} @lang('replies')</div>
                            @if($thread->latestPost)
                                <div>@lang('Last reply') {{ $thread->last_post_at->diffForHumans() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light">@lang('No threads yet. Be the first to post!')</div>
        @endforelse

        {{ $threads->links() }}
    </div>
@endsection
