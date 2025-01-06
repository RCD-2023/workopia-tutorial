<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


/** */
class JobController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     * @desc   Show all jobs
     * @route  GET /jobs
     */
    public function index(): View
    {

        $jobs = Job::latest()->paginate(9);
        return view('jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // @desc   Show create job form
    // @route  GET /jobs/create
    public function create(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // @desc   Store a new job
    // @route  POST /jobs
    public function store(Request $request): RedirectResponse
    {
        //check if user is authorized

        // dd($request->file('company_logo'));
        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'required|integer',
            'tags' => 'nullable|string',
            'job_type' => 'required|string',
            'remote' => 'required|boolean',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zipcode' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_website' => 'nullable|url',
        ]);
        //Harcoded user id
        $validatedData['user_id'] = 1;
        //check for image
        if ($request->hasFile('company_logo')) {
            //store the file and get the path
            $path = $request->file('company_logo')->store('logos', 'public');
            //add path to validated data
            $validatedData['company_logo'] = $path;
        }

        // Create a new job listing with the validated data
        //Submit to the database
        Job::create($validatedData);

        return redirect()->route('jobs.index')->with('success', 'Job listing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    // @desc   Show a single job
    // @route  GET /jobs/{id}
    public function show(Job $job): View
    {
        return view('jobs.show')->with('job', $job);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // @desc   Show the form for editing a job
    // @route  GET /jobs/{id}/edit
    public function edit(Job $job): View
    {
        $this->authorize('update', $job);
        return view('jobs.edit')->with('job', $job);
    }

    /**
     * Update the specified resource in storage.
     */
    // @desc   Update a job
    // @route  PUT /jobs/{id}
    public function update(Request $request, Job $job): string
    {
        //verifica daca userul este autorizat
        $this->authorize('update', $job);
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'required|integer',
            'tags' => 'nullable|string',
            'job_type' => 'required|string',
            'remote' => 'required|boolean',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zipcode' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_website' => 'nullable|url',
        ]);
        //Hardoded user id
        $validatedData['user_id'] = auth('')->user()->id;
        //check for image
        if ($request->hasFile('company_logo')) {
            //delete old logo
            Storage::delete('public/logos' . basename($job->company_logo));
            //store the file and get the path
            $path = $request->file('company_logo')->store('logos', 'public');
            //add path to validated data
            $validatedData['company_logo'] = $path;
        }


        //Submit to the database
        $job->update($validatedData);

        return redirect()->route('jobs.index')->with('success', 'Job listing updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    // @desc  Delete a job
    // @route DELETE /jobs/{id}
    public function destroy(Job $job): RedirectResponse
    {
        //verifica daca userul este autorizat
        $this->authorize('delete', $job);
        // If there is a company logo, delete it from storage
        if ($job->company_logo) {
            Storage::delete("public/logos/{$job->company_logo}");
        }

        // Delete the job
        $job->delete();
        //verifica daca cererea de delete vine din dashboard
        if (request()->query('from') === 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Job listing deleted successfully!');
        }

        return redirect()->route('jobs.index')->with('success', 'Job listing deleted successfully!');
    }
    // @desc   Search for jobs
    // @route  GET /jobs/search
    public function search(Request $request)
    {
        $keywords = strtolower($request->input('keywords'));
        $location = strtolower($request->input('location'));

        $query = Job::query();

        if ($keywords) {
            $query->where(function ($q) use ($keywords) {
                $q->whereRaw('LOWER(title) like ?', ['%' . $keywords . '%'])
                    ->orWhereRaw('LOWER(description) like ?', ['%' . $keywords . '%']);
            });
        }

        if ($location) {
            $query->where(function ($q) use ($location) {
                $q->whereRaw('LOWER(address) like ?', ['%' . $location . '%'])
                    ->orWhereRaw('LOWER(city) like ?', ['%' . $location . '%'])
                    ->orWhereRaw('LOWER(state) like ?', ['%' . $location . '%'])
                    ->orWhereRaw('LOWER(zipcode) like ?', ['%' . $location . '%']);
            });
        }

        $jobs = $query->paginate(12);

        return view('jobs.index')->with('jobs', $jobs);
    }
}
