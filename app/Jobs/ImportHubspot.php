<?php

namespace App\Jobs;

use App\Helpers\HubspotClientHelper;
use App\Imports\HubspotDeals;
use App\Imports\HubspotForecastCategories;
use App\Imports\HubspotOwners;
use App\Imports\HubspotPipelines;
use App\Models\Organization;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportHubspot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $organization_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $organization_id)
    {
        $this->user = $user;
        $this->organization_id = $organization_id;
    }

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 540;

    /**
     * The unique ID of the job to prevent duplicate processing.
     *
     * @return string
     */
    public function uniqueId()
    {
        return 'import-hubspot-' . $this->organization_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $organization = Organization::find($this->organization_id);
        
        // Prevent duplicate simultaneous imports
        if ($organization->synchronizing) {
            \Log::info('HubSpot import already in progress', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name
            ]);
            return;
        }

        $organization->synchronizing = true;
        $organization->save();

        try {
            $hubspot = HubspotClientHelper::createFactory($this->user);

            // #region agent log
            $startTime = microtime(true);
            file_put_contents('/Users/user/Downloads/Kara Test/kara-main/.cursor/debug.log', json_encode(['timestamp'=>time()*1000,'location'=>'ImportHubspot.php:78','message'=>'Import started','data'=>['org_id'=>$organization->id,'org_name'=>$organization->name],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A,B,C']) . "\n", FILE_APPEND);
            // #endregion

            // Each sync method handles its own transactions internally
            // #region agent log
            $pipelineStart = microtime(true);
            // #endregion
            HubspotPipelines::sync_with_hubspot($hubspot, $this->user, $organization->id);
            // #region agent log
            file_put_contents('/Users/user/Downloads/Kara Test/kara-main/.cursor/debug.log', json_encode(['timestamp'=>time()*1000,'location'=>'ImportHubspot.php:85','message'=>'Pipelines sync completed','data'=>['duration_sec'=>round(microtime(true)-$pipelineStart,2)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A']) . "\n", FILE_APPEND);
            $forecastStart = microtime(true);
            // #endregion
            HubspotForecastCategories::sync_with_hubspot($hubspot, $this->user, $organization->id);
            // #region agent log
            file_put_contents('/Users/user/Downloads/Kara Test/kara-main/.cursor/debug.log', json_encode(['timestamp'=>time()*1000,'location'=>'ImportHubspot.php:87','message'=>'Forecast sync completed','data'=>['duration_sec'=>round(microtime(true)-$forecastStart,2)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A']) . "\n", FILE_APPEND);
            $ownersStart = microtime(true);
            // #endregion
            HubspotOwners::sync_with_hubspot($hubspot, $this->user, $organization->id);
            // #region agent log
            file_put_contents('/Users/user/Downloads/Kara Test/kara-main/.cursor/debug.log', json_encode(['timestamp'=>time()*1000,'location'=>'ImportHubspot.php:89','message'=>'Owners sync completed','data'=>['duration_sec'=>round(microtime(true)-$ownersStart,2)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E']) . "\n", FILE_APPEND);
            $dealsStart = microtime(true);
            // #endregion
            HubspotDeals::sync_with_hubspot($hubspot, $this->user, $organization->id);
            // #region agent log
            file_put_contents('/Users/user/Downloads/Kara Test/kara-main/.cursor/debug.log', json_encode(['timestamp'=>time()*1000,'location'=>'ImportHubspot.php:91','message'=>'Deals sync completed','data'=>['duration_sec'=>round(microtime(true)-$dealsStart,2),'total_duration_sec'=>round(microtime(true)-$startTime,2)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A,B']) . "\n", FILE_APPEND);
            // #endregion

            $organization->last_sync = Carbon::now();
            $organization->synchronizing = false;
            $organization->save();

            \Log::info('HubSpot import completed successfully', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'synced_at' => $organization->last_sync
            ]);
        } catch (\Exception $e) {
            $organization->synchronizing = false;
            $organization->save();
            
            // Log the detailed error
            \Log::error('HubSpot import failed', [
                'organization_id' => $this->organization_id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to mark job as failed in queue
            throw $e;
        }
    }
}
