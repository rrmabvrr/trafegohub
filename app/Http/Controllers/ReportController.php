<?php

namespace App\Http\Controllers;

use App\Services\Reports\ReportExportService;
use App\Services\Reports\ReportMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportMetricsService $metricsService,
        protected ReportExportService $exportService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $filters = [
            'workspace_id' => (int) $request->input('workspace_id', 1),
            'client_id' => $request->input('client_id', 'all'),
            'platforms' => $request->input('platforms', []),
            'campaign_ids' => $request->input('campaign_ids', []),
            'start_date' => $request->input('start_date', now()->subDays(30)->toDateString()),
            'end_date' => $request->input('end_date', now()->toDateString()),
        ];

        $report = $this->metricsService->build($filters);

        return response()->json($report);
    }

    public function export(Request $request, string $format): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filters = [
            'workspace_id' => (int) $request->input('workspace_id', 1),
            'client_id' => $request->input('client_id', 'all'),
            'platforms' => $request->input('platforms', []),
            'campaign_ids' => $request->input('campaign_ids', []),
            'start_date' => $request->input('start_date', now()->subDays(30)->toDateString()),
            'end_date' => $request->input('end_date', now()->toDateString()),
        ];

        $report = $this->metricsService->build($filters);

        return $this->exportService->export($report, $format);
    }
}
