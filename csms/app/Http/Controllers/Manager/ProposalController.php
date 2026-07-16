<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    public function store(Request $request, ProjectRequest $projectRequest)
    {
        $data = $request->validate(['proposal_details' => ['required', 'string', 'max:5000']]);
        abort_if($projectRequest->proposals()->exists(), 422, 'A proposal already exists for this request.');
        $report = $projectRequest->technicalReport;
        abort_unless($report, 422, 'Technical report is not available for this request.');

        $proposal = DB::transaction(function () use ($request, $projectRequest, $report, $data) {
            $proposal = Proposal::create([
                'project_request_id' => $projectRequest->id, 'technical_report_id' => $report->report_id,
                'client_id' => $projectRequest->client_id, 'manager_id' => $request->user()->id,
                'proposal_details' => $data['proposal_details'], 'total_budget' => $report->total_budget,
                'estimated_duration' => $report->estimated_duration ?: $report->duration, 'status' => 'Sent',
            ]);
            $path = 'proposals/proposal_'.$proposal->id.'.pdf';
            Storage::disk('public')->put($path, $this->pdf($proposal)->output());
            $proposal->update(['pdf_path' => $path]);
            $projectRequest->update(['status' => 'Proposal Sent']);
            return $proposal;
        });
        return redirect()->route('project.manager.dashboard')->with('proposal_success', 'Proposal created and sent successfully.');
    }

    public function showPdf(Request $request, Proposal $proposal)
    {
        abort_if($request->user()->role === 'client' && $proposal->client_id !== $request->user()->id, 403);
        return $this->pdf($proposal)->stream('proposal_'.$proposal->id.'.pdf');
    }

    public function sendToClient(Proposal $proposal)
    {
        DB::transaction(function () use ($proposal) {
            $proposal->update(['status' => 'Sent']);
            $proposal->projectRequest()->update(['status' => 'Proposal Sent']);
        });
        return back()->with('success', 'Proposal sent to the client successfully.');
    }

    private function pdf(Proposal $proposal)
    {
        $proposal->loadMissing(['projectRequest', 'technicalReport', 'manager']);
        return Pdf::loadView('pdf.proposal', ['proposal' => $proposal,
            'projectRequest' => $proposal->projectRequest, 'technicalReport' => $proposal->technicalReport,
            'manager' => $proposal->manager]);
    }
}
