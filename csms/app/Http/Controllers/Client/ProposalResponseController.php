<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProposalResponseController extends Controller
{
    public function __invoke(Request $request, Proposal $proposal)
    {
        abort_unless($proposal->client_id === $request->user()->id, 403);
        $data = $request->validate([
            'response' => ['required', 'in:Approved,Rejected,Changes Requested'],
            'response_comment' => ['nullable', 'string', 'max:1000'],
        ]);
        DB::transaction(function () use ($proposal, $data) {
            $proposal->update(['status' => $data['response'], 'response_comment' => $data['response_comment'] ?? null]);
            $proposal->projectRequest()->update(['status' => $data['response']]);
        });
        if ($proposal->pdf_path) {
            $proposal->load(['projectRequest', 'technicalReport', 'manager']);
            Storage::disk('public')->put($proposal->pdf_path, Pdf::loadView('pdf.proposal', [
                'proposal' => $proposal, 'projectRequest' => $proposal->projectRequest,
                'technicalReport' => $proposal->technicalReport, 'manager' => $proposal->manager,
            ])->output());
        }
        return back()->with('proposal_response_success', 'Your response was sent successfully.');
    }
}
