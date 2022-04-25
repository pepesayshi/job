@extends('layouts.app')

@section('content')

<form class="container" method="post" action="{{ route('jobcreate') }}">
    <h3>Create a new job</h3><br>
    <!-- crsf protection -->
    @csrf
    <!-- 2 column grid layout with text inputs for the first and last names -->
    <div class="row mb-4">
      <div class="col">
        <div class="form-outline">
          <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" />
          <label class="form-label" for="name">Name</label>
        </div>
      </div>
      <div class="col">
        <div class="form-outline">
          <input type="number" name="contact" id="contact" class="form-control" value="{{ old('contact') }}" />
          <label class="form-label" for="contact">Contact Number</label>
        </div>
      </div>
    </div>
    <div class="col-6">
        <div class="form-outline mb-4">
            <input type="text" name="jobname" id="jobname" class="form-control" value="{{ old('jobname') }}" />
            <label class="form-label" for="jobname">Job Name</label>
        </div>
    </div>

    <!-- Message input -->
    <div class="form-outline mb-4">
      <textarea class="form-control" name="description" id="description" rows="4">{{ old('description') }}</textarea>
      <label class="form-label" for="description">Job Description</label>
    </div>
    <!-- Submit button -->
    <button type="submit" class="btn btn-primary btn-block mb-4">Post</button>
    
    <!-- Error massage -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Success -->
    @if(Session::has('new_job_post'))
        <div class="alert alert-success">
            {{Session::get('new_job_post')}}
        </div>
    @endif
</form>

@endsection