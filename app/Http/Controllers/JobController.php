<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use \App\Http\Requests\JobPostRequest;
use App\Models\Job;
use App\Models\Note;


class JobController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() : Renderable
    {
        return view('jobs.jobs', [
            'jobs' =>  $this->propertiesInjection(Job::all()),
            'sortbyoptions' => [
                'status',
                'title',
                'job id',
                'newest'
            ]
        ]);
    }

    /**
     * Method to return a new job form for user to post
     * 
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function new() : Renderable
    {
        return view('jobs.jobnew');
    }

     /**
     * Method to receive the request from a new job form
     * 
     * @param  \App\Http\Requests\JobPostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(JobPostRequest $request) : RedirectResponse
    {
        // check for crsf token
        $token = $request->session()->token();
        $token = csrf_token();

        // redirect if validation failed 
        if ((!$request->validated()) || ($token != $request->_token)) {
            return view('jobs.jobnew');
        }

        // save into db
        $post = new Job;
        $post->label = $request->jobname;
        $post->clientcontact = $request->contact;
        $post->user_id = Auth::id();
        $post->clientname = $request->name;
        $post->status = 'active';
        $post->description = $request->description;
        $post->save();

        // redirect with success message
        return redirect('/job/new')->with('new_job_post', 'New Job has been posted successfully!');
    }

    /**
     * Method pulls the data for viewing a single post
     * 
     * @param int $jobId 
     * @return Renderable|RedirectResponse
     */
    public function view(int $jobId) : Renderable|RedirectResponse
    {
        // get the single job
        if (!empty($job = Job::find($jobId))) {
            return view('jobs.job', [
                'job' => $job,
                'actions' => [
                    'active',
                    'scheduled',
                    'invoicing',
                    'to priced',
                    'completed'
                ],
                'notes' => Note::where('job_id', $jobId)->where('user_id', Auth::id())->get() ?: []
            ]);
        }
        return redirect('/jobs');
    }

    /**
     * Method filters & sort the list of jobs
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Renderable|RedirectResponse
     */
    public function search(Request $request) : Renderable
    {

        // check for crsf token
        $token = $request->session()->token();
        $token = csrf_token();

        // redirect if validation failed 
        if ($token != $request->_token) {
            return view('jobs.jobs');
        }

        // get all jobs
        if (!empty($request->search)) {
            $jobs = Job::where('label', 'like', '%'.$request->search.'%')
                        ->orwhere('description', 'like', '%'.$request->search.'%')->get();
        }
        else {
            $jobs = Job::all();
        }

        // sort them in php
        if (!empty($request->sort)) {

            $sortby = $request->sort;
            $sortingFunction = 'sortBy';

            if (stripos($request->sort, 'job id') !== false) {
                $sortby = 'id';
            }

           if (stripos($request->sort, 'newest') !== false) {
                $sortby = 'created_at';
                $sortingFunction = 'sortByDesc';
            }

            $jobs = $jobs->{$sortingFunction}($sortby);
        }

        // finally return
        return view('jobs.jobs', [
            'jobs' =>  $this->propertiesInjection($jobs),
            'sort' => $request->sort,
            'search' => $request->search,
            'sortbyoptions' => [
                'status',
                'title',
                'job id',
                'newest'
            ],
            'showsearch' => true
        ]);
    }

    /**
     * Method for update or remove a job's detail
     * Only author can remove their own job post
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Renderable|RedirectResponse
     */
    public function update(Request $request) : Renderable|RedirectResponse
    {
        // check for crsf token
        $token = $request->session()->token();
        $token = csrf_token();

        // redirect if validation failed 
        if ($token != $request->_token) {
            return view('jobs.job');
        }
        
        // request for updating a job post
        if ($request->has('save')) {
            
            // try to find the current status of the job
            $job = Job::find($request->id);
            
            // update job status only if 
            // 1. different
            // 2. new status not empty
            // 3. current job is valid
            if (!empty($job) && ($job->status != $request->status) && !empty($request->status)) {
                $job->status = $request->status;
                $job->save();
            }
        
            // update notes
            if (!empty($request->notes)) {
                $post = new Note;
                $post->user_id = Auth::id();
                $post->job_id = $request->id;
                $post->details = $request->notes;
                $post->save();
            } 

            // redirect with success message
            return redirect('/job/'.$request->id.'')->with('job_update', 'Updated successfully!');
        }
       
        // if its for remove a job post
        if ($request->has('remove')) {

            // only can remove if its the same user_id
            $auth = $this->AuthenticateUserByJobId($request->id);

            if ($auth) {
                $job = Job::find($request->id);
                $job->delete();
            }

            return redirect('/jobs')->with('job_updated', 'Job has been deleted successfully!');
        }

        // if its for editing a note
        if ($request->has('savenote')) {

            // only can remove if its the same user_id
            $auth = $this->AuthenticateUserByNoteId($request->savenote);

            if ($auth) {
                $note = Note::find($request->savenote);
                $note->details = $request->{'singlenotetext-'.$request->savenote};
                $note->save();
            }

            return redirect('/job/'.$request->id.'')->with('job_update', 'Updated successfully!');

        }

        // if its for removing a note
        if ($request->has('removenote')) {

            // only can remove if its the same user_id
            $auth = $this->AuthenticateUserByNoteId($request->removenote);

            if ($auth) {
                $note = Note::find($request->removenote);
                $note->delete();
            }

            return redirect('/job/'.$request->id.'')->with('job_update', 'Removed successfully!');

        }
      
        // default 
        return redirect('/job/'.$request->id.'');
    }

    /**
     * Method injects additional properties into jobs array
     * 
     * @param Collection $jobs
     * @return Collection $jobs after injection
     */
    private function propertiesInjection(Collection|array $jobs) : Collection|array
    {

        // loop through all jobs, add addtional properties
        foreach ($jobs as $job) {

            // additional css class name
            switch ($job->status ?? null) {
                case 'active':
                    $job->badge = 'bg-warning';
                    break;
                case 'scheduled':
                    $job->badge = 'bg-info';
                    break;
               case 'invoicing':
                    $job->badge = 'bg-primary';
                    break;
                case 'to priced':
                    $job->badge = 'bg-secondary';
                    break;
                case 'completed':
                    $job->badge = 'bg-success';
                    break;
                default:
                    break;
            }

        }

        return $jobs;

    }

    /**
     * Method to check if the person who posted the job matches the user in the session
     * 
     * @param string userid
     * @return boolean
     */
    private function AuthenticateUserByJobId(string $jobId) : bool
    {
        // make sure this is from author's action
        $userId = Job::where('id', $jobId)->pluck('user_id')->first();

        // only can remove if its the same user_id
        if ($userId == Auth::id()) {
            return true;
        }

        return false;
    }

    /**
     * Method to check if the person who posted the note matches the user in the session
     * 
     * @param string userid
     * @return boolean
     */
    private function AuthenticateUserByNoteId(string $noteId) : bool
    {
        // make sure this is from author's action
        $userId = Note::where('id', $noteId)->pluck('user_id')->first();

        // only can remove if its the same user_id
        if ($userId == Auth::id()) {
            return true;
        }

        return false;
    }

}
