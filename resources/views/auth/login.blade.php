@extends('layouts.main')
@section('title')
    @lang('Log in to Darakht-e Danesh Library') - @lang('Darakht-e Danesh Library')
@endsection
@section('description')
    @lang('The Darakht-e Danesh Online Library for Educators is a repository of open educational resources for teachers, teacher trainers, school administrators, literacy workers and others involved in furthering education in Afghanistan.')
@endsection
@section('page_image')
    {{ asset('storage/files/logo-dd.png') }}
@endsection
@section('content')
    <style>
        .divider {
            display: flex;
            margin: 30px 0;
            color: #151429;
            font-weight: 500;
            font-size: 16px;
            white-space: nowrap;
            text-align: center;
            border-top: 0;
            border-top-color: rgba(0, 0, 0, 0.06);
        }

        .divider-inner-text {
            display: inline-block;
            padding: 0 1em;
        }

        .divider::after,
        .divider::before {
            position: relative;
            top: 50%;
            width: 50%;
            border-top: 1px solid transparent;
            border-top-color: inherit;
            border-bottom: 0;
            -webkit-transform: translateY(50%);
            transform: translateY(50%);
            content: '';
        }

        .socialite {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <section class="ddl-forms login">
        <div>
            <header>
                <h3>@lang('Log in to Darakht-e Danesh Library')</h3>
            </header>
            <div>
                @include('layouts.messages')
                <form method="POST" action="{{ route('login') }}">
                    @honeypot
                    @csrf
                    <div class="form-item">
                        <input class="form-control{{ $errors->has('user-field') ? ' is-invalid' : '' }}" id="user-field"
                            name="user-field" autocomplete="username" spellcheck="false" placeholder="@lang('Email or username or phone')"
                            size="40" type="text" value="{{ old('user-field') }}" required autofocus>
                        @if ($errors->has('user-field'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('user-field') }}</strong>
                            </span><br>
                        @endif
                    </div>

                    <div class="form-item">
                        <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password"
                            name="password" autocomplete="current-password" spellcheck="false"
                            placeholder="@lang('Password')" size="40" type="password" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-item text-start">
                        <label id="remember-me">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                    <div class="form-item">
                        <input class="btn btn-primary btn-md btn-block" type="submit" value="@lang('Log in')">
                    </div>
                    <div class="form-item">

                        <div class="divider">
                            <span class="divider-inner-text">or</span>
                        </div>
                    </div>
                    <div class="socialite">
                        {{-- <a href="{{ route('login.google') }}" class="form-control login-submit btn btn-primary">Login with
                            Google</a>
                        <a href="{{ route('login.facebook') }}" class="form-control login-submit btn btn-primary">Login with
                            Facebook</a> --}}
                        <a href="{{ route('login.google') }}" class="btn btn-outline-secondary btn-md" type="submit">
                            <i class="fab fa-google"></i>
                            <span class="oauth-icon-separator"></span>
                            @lang('Log in with Google')
                        </a>
                        <a href="{{ route('login.facebook') }}" class="btn btn-outline-secondary btn-md float-xl-right"
                            type="submit">
                            <i class="fab fa-facebook-f"></i>
                            <span class="oauth-icon-separator"></span>
                            @lang('Log in with Facebook')
                        </a>
                    </div>


                    <div class="form-group text-start" style="margin-top: 20px;">
                        <a href="{{ route('register') }}" style="margin-right: 25px;">@lang('Sign up')</a>
                        <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
