<?php

namespace App\Livewire\Dashboard;

use App\Enum\GoalType;
use App\Helpers\Periods;
use App\Models\Activity;
use App\Models\Member;
use App\Models\Team;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ThumbnailWidget extends Component
{
    public $selected_team=[];
    public $members=null;

    #[On('teams-select-change')]
    public function teamSelection($team){
        if (!is_array($team)) $team=[$team];
        else if(isset($team['values'])) $team = $team['values'];

        $this->selected_team = $team;
    }

    public function render()
    {
        $this->members = Member::select('members.*')->where('organization_id', Auth::user()->organization()->id)->where('active',1)
            ->join('member_team', 'members.id', '=', 'member_team.member_id')->whereIn('member_team.team_id', $this->selected_team??[0] )
            ->orderBy('member_team.id')->get();

        return view('livewire.dashboard.thumbnail-widget');
    }
}
