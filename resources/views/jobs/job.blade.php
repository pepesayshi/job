@extends('layouts.app')

@section('content')

<div class="container">
    @if (!empty($job))
    <div>
        <a href="{{ route('jobs') }}">
            <button type="button" class="btn btn-dark">Back</button>
        </a>
    </div><br>
    <form method="post" action="{{ route('jobupdate') }}">
        <div class="card">
            <div class="card-body">
                <div><h5>Job ID: {{ $job->id }}</h5></div>
                <br><h5 class="card-title">{{ ucfirst($job->label) }}</h5><br>
                <p class="card-text">{{ ucfirst($job->description) }}</p><br>
            </div>
            <ul class="list-group list-group-flush">
                <br><li class="list-group-item">Client: {{ ucwords($job->clientname) }}</li><br>
                <li class="list-group-item">Client Contact: 
                    <a href="tel:{{ $job->clientcontact }}">
                        {{ $job->clientcontact }}
                    </a>
                </li><br>
                <li class="list-group-item">Job Status:
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" name="status" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            {{  $job->status }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            @foreach ($actions as $action)
                                @if (stripos($action, $job->status) !== false)
                                    @continue;
                                @endif
                                <li><a class="dropdown-item" onclick="dropdown(this.innerHTML);">{{ $action }}</a></li>
                            @endforeach
                        </ul>
                    </div><br>
                </li>
            </ul>
            <div class="card-footer">
                <small class="text-muted">Last updated at {{ $job->updated_at }}</small>
                <small style="float: right;" class="text-muted">Created at {{ $job->updated_at }}</small>
            </div><br>
            <div style="width: 50%;margin: auto;">
                <!-- Message input -->
                <div class="form-outline mb-4">
                    <textarea class="form-control" name="notes" id="notes" rows="4">{{ old('notes') }}</textarea>
                    <label class="form-label" for="notes">Notes</label><br>
                    <small class="text-muted">* Notes will only be visible for yourself</small>
                </div>
            </div>
            <input hidden name="id" value="{{ $job->id }}"/>
            <input hidden name="status" id="jobstatus" value=""/>
            @csrf
            <!-- Submit button -->
            <button style="width: 20%;margin: auto;" type="submit" name="save" class="btn btn-primary btn-block mb-4">Save</button>

            <!-- Only the person created the job can delete it -->
            @if ($job->user_id == Auth::id())
                <button style="margin: auto;" type="submit" name="remove" class="btn btn-danger btn-block mb-4">Delete</button>
                <small style="text-align:center;" class="text-muted">* Only Author of the post can remove</small><br>
            @endif
        </div>
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
        @if(Session::has('job_update'))
            <div class="alert alert-success">
                {{Session::get('job_update')}}
            </div>
        @endif
        <br>
        @if (!empty($notes->count()))
            <div class="card-group">
                @foreach ($notes as $note)
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text" id="singlenotelabel-{{ $note->id }}">{{ ucfirst($note->details) }}</p>
                            <textarea class="d-none form-control" name="singlenotetext-{{ $note->id }}" id="singlenotetext-{{ $note->id }}" rows="1">{{ ucfirst($note->details) }}</textarea><br>
                            <button type="button" name="editnote" id="editnote-{{ $note->id }}" class="btn btn-dark" onclick="note({{ $note->id }});">Edit</button>
                            <button type="submit" name="savenote" value="{{ $note->id }}" id="savenote-{{ $note->id }}" class="d-none btn btn-success">Save</button>
                            <button type="submit" name="removenote" value="{{ $note->id }}" class="btn btn-danger">Remove</button>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Created at: {{ $note->created_at }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
        <div class="alert alert-success" role="alert">
            Create a note for yourself!
        </div>
        @endif
       
    </form>
    @endif
<div>

@endsection