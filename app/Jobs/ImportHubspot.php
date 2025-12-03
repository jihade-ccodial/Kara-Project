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
     * The unique ID of the job.
     *
     * @return string
     */
    /*
     public function uniqueId()
     {
         return $this->user->organization_id;
     }
*/
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 540;

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function handle()
    {
        //$time_start = microtime(true);//seconds
        $hubspot = HubspotClientHelper::createFactory($this->user);

        $organization = Organization::find($this->organization_id);
        $organization->synchronizing = true;
        $organization->save();

        DB::beginTransaction();
        try {
            HubspotPipelines::sync_with_hubspot($hubspot, $this->user, $organization->id);
            HubspotForecastCategories::sync_with_hubspot($hubspot, $this->user, $organization->id);
            HubspotOwners::sync_with_hubspot($hubspot, $this->user, $organization->id);
            HubspotDeals::sync_with_hubspot($hubspot, $this->user, $organization->id);

            $organization->last_sync = Carbon::now();
            $organization->synchronizing = false;
            $organization->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $organization->synchronizing = false;
            $organization->save();
        }

        //$time_end = microtime(true);//seconds
        //ray($time_end - $time_start);
    }
}
