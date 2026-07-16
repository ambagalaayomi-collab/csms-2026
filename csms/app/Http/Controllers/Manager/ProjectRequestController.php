<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectRequestController extends Controller
{
    public function assign(Request $request, ProjectRequest $projectRequest)
    {
        $engineerId = $request->input('assigned_engineer_id')
            ?: User::where('role', 'engineer')->value('id');
        if (! $engineerId) return back()->with('error', 'No engineer account is available.');
        $data = $request->validate([
            'assigned_engineer_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);
        abort_unless(User::whereKey($engineerId)->where('role', 'engineer')->exists(), 422);
        $projectRequest->update(['assigned_engineer_id' => $engineerId,
            'due_date' => $data['due_date'] ?? now()->addDays(7), 'status' => 'Assigned']);
        return back()->with('success', 'Request sent to the engineer successfully.');
    }

    public function updateStatus(Request $request, ProjectRequest $projectRequest)
    {
        $data = $request->validate(['status' => ['required', 'in:Pending,Assigned,In Review,Approved,Rejected,Changes Requested,Completed,Proposal Sent']]);
        $projectRequest->update($data);
        return back()->with('status_success', 'Request status updated successfully.');
    }
}
