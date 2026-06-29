@extends('layouts.main')

@section('title', $thread->title)

@section('content')
    <div class="container py-4" style="max-width: 860px;">

        {{-- Thread header --}}
        <div class="mb-3">
            <a href="{{ route('threads.index') }}" class="text-muted text-decoration-none small">
                ← @lang('Back to Forum')
            </a>
        </div>
        @if(isAdmin())
            <div class="card mb-4 border-warning">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-bold me-2">@lang('Moderation')</span>

                        {{-- Lock/Unlock --}}
                        <form action="{{ route('threads.lock', $thread) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $thread->is_locked ? 'btn-success' : 'btn-warning' }}">
                                {{ $thread->is_locked ? __('Unlock') : __('Lock') }}
                            </button>
                        </form>

                        {{-- Pin/Unpin --}}
                        <form action="{{ route('threads.pin', $thread) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $thread->is_pinned ? 'btn-secondary' : 'btn-primary' }}">
                                {{ $thread->is_pinned ? __('Unpin') : __('Pin') }}
                            </button>
                        </form>

                        {{-- Move --}}
                        <form action="{{ route('threads.move', $thread) }}" method="POST" class="d-flex gap-1">
                            @csrf @method('PATCH')
                            <select name="category_id" class="form-select form-select-sm" style="width:auto;">
                                @foreach(\App\Models\Category::orderBy('order')->get() as $cat)
                                    <option value="{{ $cat->id }}" @selected($cat->id === $thread->category_id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-primary">@lang('Move')</button>
                        </form>

                        {{-- Delete thread --}}
                        <form action="{{ route('threads.destroy', $thread) }}" method="POST"
                              onsubmit="return confirm('{{ __('Delete this thread and all its replies?') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">@lang('Delete Thread')</button>
                        </form>

                        {{-- Ban thread author --}}
                        <form action="{{ route('forum.users.ban', $thread->user) }}" method="POST"
                              onsubmit="return confirm('{{ __('Toggle forum ban for this user?') }}')">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $thread->user->isForumBanned() ? 'btn-outline-success' : 'btn-outline-danger' }}">
                                {{ $thread->user->isForumBanned() ? __('Unban User') : __('Ban User') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        @if($thread->is_pinned)
                            <span class="badge bg-warning text-dark me-1">@lang('Pinned')</span>
                        @endif
                        @if($thread->is_locked)
                            <span class="badge bg-secondary me-1">@lang('Locked')</span>
                        @endif
                        <h1 class="h4">{{ $thread->title }}</h1>
                        <div class="text-muted small">
                            @lang('by') <strong>{{ $thread->user->username }}</strong>
                            · {{ $thread->created_at->diffForHumans() }}
                            · <a href="{{ route('threads.categories.show', $thread->category) }}">{{ $thread->category->name }}</a>
                        </div>
                    </div>
                    @can('update', $thread)
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                @lang('Manage')
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('threads.edit', $thread) }}">@lang('Edit')</a>
                                </li>
                                <li>
                                    <form action="{{ route('threads.destroy', $thread) }}" method="POST"
                                          onsubmit="return confirm('{{ __('Delete this thread?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger">@lang('Delete')</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endcan
                </div>
                <hr>
                <div class="mt-2">{!! nl2br(e($thread->body)) !!}</div>
            </div>
        </div>

        {{-- Posts / replies --}}
        <h5 class="mb-3">@lang('Replies')</h5>

        @forelse($posts as $post)
            <div class="card mb-3" id="post-{{ $post->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-muted small">
                            <strong>{{ $post->user->username }}</strong> · {{ $post->created_at->diffForHumans() }}
                        </div>
                        @can('delete', $post)
                            <form action="{{ route('threads.posts.destroy', [$thread, $post]) }}" method="POST"
                                  onsubmit="return confirm('{{ __('Delete this reply?') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-link text-danger p-0">@lang('Delete')</button>
                            </form>
                        @endcan
                    </div>
                    <div class="mt-2">{!! nl2br(e($post->body)) !!}</div>
                </div>
            </div>
        @empty
            <div class="alert alert-light">@lang('No replies yet.')</div>
        @endforelse

        {{ $posts->links() }}

        {{-- Reply form --}}
        @auth
            @if(!$thread->is_locked)
                <div class="card mt-4" id="posts">
                    <div class="card-body">
                        <h6 class="mb-3">@lang('Leave a Reply')</h6>
                        <form action="{{ route('threads.posts.store', $thread) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                            <textarea name="body" rows="5"
                                      class="form-control @error('body') is-invalid @enderror"
                                      placeholder="{{ __('Write your reply...') }}" required>{{ old('body') }}</textarea>
                                @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('Post Reply')</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary mt-4">@lang('This thread is locked and no longer accepts replies.')</div>
            @endif
        @else
            <div class="alert alert-light mt-4">
                <a href="{{ route('login') }}">@lang('Log in')</a> @lang('to leave a reply.')
            </div>
        @endauth

    </div>
@endsection
