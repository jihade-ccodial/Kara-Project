<?php

namespace App\Livewire\Tasks;

use App\Models\Activity;
use Illuminate\Console\View\Components\Task;
use Livewire\Component;

class ShowTasks extends Component
{
    public $deal;
    public $owner;
    protected $listeners = ['refreshTasks' => '$refresh'];

    /*
    public function mount($deal, $owner)
    {
        $this->deal = $deal;
        $this->owner = $owner;
    }
    */
    function getTasks(){
        $tasks = Activity::where('type', 'Task');
        if ($this->owner)
            $tasks->where('member_id', $this->owner);
        if ($this->deal)
            $tasks->where('deal_id', $this->deal);

        return $tasks->with('deal')->orderBy('deal_id')->get();
    }


    public function render()
    {
        $tasks = $this->getTasks();
        return view('livewire.tasks.show-tasks')->with([
            'tasks'=>$tasks
        ]);
    }

    public function dehydrate(){
        $this->dispatch('contentChanged');
    }
}
