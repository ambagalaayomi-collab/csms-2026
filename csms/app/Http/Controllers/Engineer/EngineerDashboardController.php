<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;

class EngineerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectRequest::with(['technicalReport'])
            ->where('assigned_engineer_id', $request->user()->id)
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('id', 'like', "%{$search}%")->orWhere('project_type', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%");
            }))->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
        $assignedRequests = $query->latest('updated_at')->get();
        return view('engineer.dashboard', ['assignedRequests' => $assignedRequests,
            'assignedCount' => $assignedRequests->count()]);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate(['request_id' => ['required', 'exists:project_requests,id'],
            'status' => ['required', 'in:Assigned,In Review,Completed'], 'remarks' => ['nullable', 'string']]);
        $project = ProjectRequest::whereKey($data['request_id'])
            ->where('assigned_engineer_id', $request->user()->id)->firstOrFail();
        $project->update(['status' => $data['status']]);
        return back()->with('success', 'Status updated successfully.');
    }
}
