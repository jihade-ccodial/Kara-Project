<?php

namespace App\Livewire\Dashboard;

use App\Helpers\Periods;
use App\Models\Deal;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DealsWonWidget extends Component
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

    private function getQuery() {
        $deals = Deal::whereHas('pipeline', function($q){
            $q->where('organization_id', '=', Auth::user()->organization()->id);
            $q->where('active',1);
        })->whereHas('member', function($q){
            $q->whereHas('teams',  function($q2){
                $q2->whereIn('teams.id', $this->selected_team);
            });
        })->whereHas('stage', function($q) {
            $q->where('probability', 1);
        });

        if ($this->member)
            $deals->where('member_id', $this->member->id);

        $this->fillPeriodDates();
        if (($this->startdate)&&($this->enddate))
            $deals->whereBetween('closedate', [$this->startdate, $this->enddate]);

        return $deals;
    }

    public function render()
    {
        $currency = Auth::user()->currency();
        if (!$this->selected_team){
            $this->count = 0;
            $this->amount = currency_format(0, $currency);
        }else {
            $deals = $this->getQuery();
            $this->count = $deals->count();
            $this->amount = currency_format($deals->sum('amount'), $currency);
        }

        return view('livewire.dashboard.deals-won-widget');
    }
}
