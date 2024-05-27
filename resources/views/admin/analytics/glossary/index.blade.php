@extends('admin.layout')
@section('admin.content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="pb-4">
                <form method="get" action="{{ url('admin/glossary-analytics') }}">
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
                    <i class="fa fa-table"></i> Glossary Analytics
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Total glossary base on gender  --}}
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-secondary mb-3">
                                <div class="card-header">Total users base on gender </div>
                                <div class="card-body text-secondary p-2">
                                    <div class="card-text">
                                            <div class="d-flex justify-content-between mb-2 rounded bg-light text-dark">
                                                <div class="p-1 text-capitalize">
                                                    1
                                                </div>
                                                <div class="p-1">
                                                    <span class="badge badge-info">
                                                        12
                                                    </span>
                                                </div>
                                            </div>
                                        <div class="d-flex justify-content-between mb-2 rounded bg-light text-dark">
                                            <div class="p-1 text-capitalize">
                                                Total 
                                            </div>
                                            <div class="p-1">
                                                <span class="badge badge-info">
                                                    0
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