<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\ProjectRequest;
use App\Models\UnitRate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function create(Request $request, ?int $project_request_id = null)
    {
        $assignedRequests = ProjectRequest::with(['estimate', 'technicalReport'])
            ->where('assigned_engineer_id', $request->user()->id)
            ->whereIn('status', ['Assigned', 'In Review', 'Approved'])->latest()->get();
        $rates = UnitRate::pluck('rate', 'item_key')->toArray();
        return view('engineer.estimates', compact('assignedRequests', 'rates', 'project_request_id'));
    }

    public function store(Request $request)
    {
        $fields = ['cement', 'sand', 'steel', 'brick', 'mason', 'carpenter', 'helper', 'mixer', 'excavator', 'truck'];
        $rules = ['project_request_id' => ['required', 'exists:project_requests,id'],
            'estimated_duration' => ['required', 'string', 'max:255'], 'remarks' => ['nullable', 'string']];
        foreach ($fields as $field) {
            $rules["{$field}_qty"] = ['required', 'numeric', 'min:0'];
            $rules["{$field}_cost"] = ['required', 'numeric', 'min:0'];
        }
        $data = $request->validate($rules);
        $project = ProjectRequest::whereKey($data['project_request_id'])
            ->where('assigned_engineer_id', $request->user()->id)->firstOrFail();
        $data['grand_total'] = collect($fields)->sum(fn ($field) => (float) $data["{$field}_cost"]);
        Estimate::updateOrCreate(['project_request_id' => $project->id], $data);
        $project->update(['status' => 'In Review']);
        return back()->with('success', 'Cost estimate saved successfully.');
    }

    public function generateReport(Request $request, int $id)
    {
        $projectRequest = ProjectRequest::with('estimate')->whereKey($id)
            ->where('assigned_engineer_id', $request->user()->id)->firstOrFail();
        abort_unless($projectRequest->estimate, 404, 'No estimate found for this request.');
        return Pdf::loadView('pdf.estimate_pdf', compact('projectRequest'))
            ->stream('Estimate_Report_R-'.$projectRequest->id.'.pdf');
    }
}
