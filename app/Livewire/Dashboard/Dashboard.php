<?php

namespace App\Livewire\Dashboard;

use App\Models\Team;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public $teams=[];
    public $selected_team;

    #[On('teams-select-change')]
    public function teamSelection($team){
        if (!is_array($team)) $team=[$team];
        else if(isset($team['values'])) $team = $team['values'];
        $this->selected_team = $team;
    }

    public function render()
    {
        $this->teams = Team::where('organization_id', Auth::user()->organization()->id)->pluck('name','id');
        return view('livewire.dashboard.dashboard');
    }
}
