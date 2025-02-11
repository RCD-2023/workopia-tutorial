<?php

namespace App\Http\Controllers;


use App\Models\Job;
use App\Models\Applicant;
use App\Mail\JobApplied;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class ApplicantController extends Controller
{
    //@desc store new job application
    //@route POST /jobs/{job}apply

    public function store(Request $request, Job $job): RedirectResponse
    {
        // Check if the user has already applied for the job
        $existingApplication = Applicant::where('job_id', $job->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You have already applied to this job.');
        }
        //validate incoming data
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'contact_number' => 'string|max:20',
            'contact_email' => 'required|email',
            'message' => 'string',
            'location' => 'string|max:255',
            'resume' => 'required|file|mimes:pdf|max:2048',
        ]);
        // Handle the resume file upload
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validatedData['resume_path'] = $path;
        }
        // Store the application
        $application = new Applicant($validatedData);
        $application->job_id = $job->id;
        $application->user_id = auth()->id(); //ignor eroarea
        $application->save();


        // Send email to owner
        // Mail::to($job->user->email)->send(new JobApplied($application, $job));
        return redirect()->back()->with('success', 'Your application has been submitted!');
    }
    // @desc   Delete a job application
    // @route  DELETE /applicants/{applicant}
    public function destroy($id): RedirectResponse
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->delete();
        return redirect()->route('dashboard')->with('success', 'Applicant deleted successfully.');
    }
}
