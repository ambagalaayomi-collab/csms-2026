<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $clientId = $request->user()->id;
        $base = ProjectRequest::where('client_id', $clientId);
        $myRequests = (clone $base)->with('latestProposal')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('id', 'like', "%{$search}%")->orWhere('project_type', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()->get();
        $myProposals = Proposal::with('projectRequest')->where('client_id', $clientId)
            ->whereNotNull('pdf_path')->latest()->get();

        return view('client.dashboard', [
            'myRequests' => $myRequests, 'myProposals' => $myProposals,
            'totalRequests' => (clone $base)->count(),
            'pendingRequests' => (clone $base)->where('status', 'Pending')->count(),
            'approvedRequests' => (clone $base)->where('status', 'Approved')->count(),
            'completedRequests' => (clone $base)->where('status', 'Completed')->count(),
            'proposalCount' => $myProposals->count(),
            'notificationCount' => $myProposals->where('status', 'Sent')->count(),
        ]);
    }
}
