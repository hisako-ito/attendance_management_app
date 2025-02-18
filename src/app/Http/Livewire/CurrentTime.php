<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;

class CurrentTime extends Component
{
    public function render()
    {
        return view('livewire.current-time', [
            'time' => Carbon::now()->format('H:i'),
        ]);
    }
}
