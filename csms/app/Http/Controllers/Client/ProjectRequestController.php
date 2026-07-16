<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;

class ProjectRequestController extends Controller
{
    public function create() { return view('client.request_project'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'phone' => ['required', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'email'], 'project_type' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'], 'width' => ['required', 'numeric', 'gt:0'],
            'height' => ['required', 'numeric', 'gt:0'], 'budget' => ['required', 'numeric', 'min:0'],
            'timeline' => ['required', 'string', 'max:100'], 'requirements' => ['required', 'string'],
        ]);
        ProjectRequest::create([...$data, 
        'client_id' => $request->user()->id, 
        'status' => 'Pending']);

        return redirect()
            ->route('client.dashboard')
            ->with('request_success', 'Project request submitted successfully.');
    }
}
