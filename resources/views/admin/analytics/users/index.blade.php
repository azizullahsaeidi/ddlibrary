@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="pb-4">
                <form method="get" action="{{ url('admin/user-analytics') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <label>From Date</label>
                            <input type="date" value="{{ request()->date_from }}" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-2">
                            <label>To Date</label>
                            <input type="date" value="{{ request()->date_to }}" class="form-control" name="date_to">
                        </div>

                        <div class="col-md-2" style="align-self: flex-end">
                            <input class="btn btn-primary" type="submit" value="Filter">
                        </div>
                    </div>

                </form>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-table"></i> User Analytics
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Total registered users  --}}
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-secondary mb-3">
                                <div class="card-header">Total registered users </div>
                                <div class="card-body text-secondary p-2">
                                    <div class="card-text">
                                        <div class="d-flex justify-content-between mb-2 rounded bg-light text-dark">
                                            <div class="p-1 text-capitalize">
                                                Total Users
                                            </div>
                                            <div class="p-1">
                                                <span class="badge badge-info">
                                                    {{ number_format($totalRegisteredUsers) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- List the total admins/managers/translators.  --}}
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-secondary mb-3">
                                <div class="card-header">List the total admins/managers/translators. </div>
                                <div class="card-body text-secondary p-2">
                                    <div class="card-text">
                                        @foreach ($roles as $role)
                                            <div class="d-flex justify-content-between mb-2 rounded bg-light text-dark">
                                                <div class="p-1 text-capitalize">
                                                    {{ $loop->iteration }}.
                                                    {{ $role->name }}
                                                </div>
                                                <div class="p-1">
                                                    <span class="badge badge-info">
                                                        {{ number_format($role->users_count) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total users base on gender  --}}
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-secondary mb-3">
                                <div class="card-header">Total users base on gender </div>
                                <div class="card-body text-secondary p-2">
                                    <div class="card-text">
                                        @foreach ($totalUsersBaseOnGenders as $totalUsersBaseOnGender)
                                            <div class="d-flex justify-content-between mb-2 rounded bg-light text-dark">
                                                <div class="p-1 text-capitalize">
                                                    {{ $loop->iteration }}.
                                                    {{ $totalUsersBaseOnGender->gender }}
                                                </div>
                                                <div class="p-1">
                                                    <span class="badge badge-info">
                                                        {{ number_format($totalUsersBaseOnGender->users_count) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="d-flex justify-content-between mb-2 rounded bg-light text-dark">
                                            <div class="p-1 text-capitalize">
                                                Total Users
                                            </div>
                                            <div class="p-1">
                                                <span class="badge badge-info">
                                                    {{ number_format($totalUsersBaseOnGenders->sum('users_count')) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection