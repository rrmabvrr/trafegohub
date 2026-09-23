<?php

namespace App\Jobs;

use App\Models\Workspace;
use App\Services\AutomationEngineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EvaluateAutomationRulesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $workspaceId = null) {}

    public function handle(AutomationEngineService $automationService): void
    {
        $workspaces = $this->workspaceId 
            ? Workspace::where('id', $this->workspaceId)->get() 
            : Workspace::all();

        foreach ($workspaces as $workspace) {
            $automationService->evaluateRulesForWorkspace($workspace->id);
        }
    }
}
