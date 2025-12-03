<?php

namespace App\Livewire\Dashboard;

use App\Enum\GoalType;
use App\Helpers\Periods;
use App\Models\Activity;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TotalTasksWidget extends Component
{
    public $selected_period;
    public $selected_team;

    public $amount;
    public $count;

    private $startdate=null;
    private $enddate=null;
    public $member=null;

    #[On('teams-select-change')]
    public function teamSelection($team){
        if (!is_array($team)) $team=[$team];
        else if(isset($team['values'])) $team = $team['values'];
        $this->selected_team = $team;
        if ($this->member) {
            $this->selected_team = $this->member->teams()->pluck( 'teams.id' )->toArray();
        }
    }

    #[On('dashboard-counters-period-change')]
    public function periodSelection($period){
        $this->selected_period = $period['values'];
    }

    #[On('refresh-counter')]
    public function refresh()
    {
        //
    }

    private function fillPeriodDates(){
        $dates=Periods::get($this->selected_period);
        if ($dates) {
            $this->startdate= $dates['from'];
            $this->enddate= $dates['to'];
        }else {
            $this->startdate=null;
            $this->enddate=null;
        }
    }

    public function render()
    {
        //ray()->showQueries();
        $tasks = Activity::where('type', GoalType::TASK)->where('hubspot_status', 'COMPLETED')->
                           whereHas('deal', function($q){
                                $q->whereHas('pipeline', function($q2){
                                    $q2->where('organization_id', '=', Auth::user()->organization()->id);
                                    $q2->where('active',1);
                                });
                           })->
                           whereHas('member', function($q) {
                                $q->whereHas( 'teams', function ( $q2 ) {
                                    $q2->whereIn( 'teams.id', $this->selected_team??[0] );
                                });
                           });

        if ($this->member)
            $tasks->where('member_id', $this->member->id);

        /*
         $this->fillPeriodDates();
          if (($this->startdate)&&($this->enddate))
             $deals->whereBetween('closedate', [$this->startdate, $this->enddate]);
         */

        $this->count = $tasks->count();
        return view('livewire.dashboard.total-tasks-widget');
    }
}
