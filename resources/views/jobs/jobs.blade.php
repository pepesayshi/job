@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (empty($jobs->count()) && !($showsearch ?? false))
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Be the first to create a new job!</h4>
                <p>Create the first job by clicking the button on the top right corner - new job</p>
                <hr>
                <p class="mb-0">Page will start showing jobs after.</p>
            </div>
            @else
            <!-- Success -->
            @if(Session::has('job_updated'))
                <div class="alert alert-success">
                    {{Session::get('job_updated')}}
                </div>
            @endif
            <form method="post" action="{{ route('jobsearch') }}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="input-group rounded">
                            <input name="search" value="{{ $search ?? null }}" type="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" />
                        </div>
                        </div class="col-md-8">
                        <div class="col-md-4">
                        <select name="sort" class="form-select" aria-label="select">
                            <option disabled>Sort by</option>
                            @if (!empty($sortbyoptions))
                                @foreach ($sortbyoptions as $option)
                                    <?= $class = null ?>
                                    @if (stripos(($sort ?? null), $option) !== false)
                                        <?= $class = 'selected'; ?>
                                    @endif
                                    <option {{ $class ?? null }} value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div class="col-md-4">
                </div><br>
                @csrf
                <div class="row" style="width:45%;margin:auto;">
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-info" >Apply</button>
                    </div class="col-md-3">
                    <div class="col-md-3">
                        <a href="/">
                            <button type="button" class="btn btn-danger" >Reset</button>
                        </a>
                    </div class="col-md-3">
                </div>
            </form>
            <div class="album py-5 bg-light">
                <div class="container">
                  <div class="row">
                    @foreach ($jobs as $job)
                        <div class="col-md-4">
                            <div class="card mb-4 box-shadow">
                                <div><h5>Job ID: {{ $job->id }}</h5></div>
                                <div><h5>{{ ucfirst($job->label) }}</h5></div>
                                <div class="card-body">
                                    <p class="card-text">{{ ucfirst($job->description) }}</p>
                                    <br>
                                    <div>
                                       Status:<br>
                                       <span class="badge badge-pill {{ $job->badge }}">{{  $job->status }}</span>
                                    </div><br>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <a href="/job/{{ $job->id }}">
                                                <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">Updated at: {{ $job->updated_at }}</small>
                                    <small class="text-muted">Created at: {{ $job->created_at }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                  </div>
                </div>
              </div>
            @endif
            
    </div>
</div>
@endsection
