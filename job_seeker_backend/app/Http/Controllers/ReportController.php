<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
        }

        if ($request->has('filter') && $request->input('filter') !== 'All Reports') {
            $query->where('type', $request->input('filter'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $report = Report::create([
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'last_updated' => now(),
            'icon' => 'fa-file-alt'
        ]);

        return response()->json($report, 201);
    }

    public function show(Report $report)
    {
        return response()->json($report);
    }

    public function update(Request $request, Report $report)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'views' => 'sometimes|integer',
            'downloads' => 'sometimes|integer',
        ]);

        $report->update($request->all());

        return response()->json($report);
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return response()->json(null, 204);
    }

    public function downloadPdf(Report $report)
    {
        try {
            // Increment download count
            $report->increment('downloads');
            
            // Prepare data for PDF
            $data = [
                'report' => $report,
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'sample_data' => [
                    ['date' => date('Y-m-d'), 'metric' => 'Total Users', 'value' => '1,250', 'status' => 'Active'],
                    ['date' => date('Y-m-d'), 'metric' => 'New Registrations', 'value' => '45', 'status' => 'Growing'],
                    ['date' => date('Y-m-d'), 'metric' => 'Job Applications', 'value' => '320', 'status' => 'Stable'],
                    ['date' => date('Y-m-d'), 'metric' => 'Success Rate', 'value' => '78%', 'status' => 'Good']
                ]
            ];
            
            // Generate PDF
            $pdf = Pdf::loadView('reports.pdf-template', $data);
            
            // Create filename
            $fileName = str_replace(' ', '_', $report->title) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Return PDF download
            return $pdf->download($fileName);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate PDF report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function share(Report $report)
    {
        // Logic for sharing the report (e.g., generate a shareable link, send email)
        return response()->json(['message' => 'Report shared successfully']);
    }
}
