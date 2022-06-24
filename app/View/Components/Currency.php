<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Currency extends Component
{

    public $type;
    public $top;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($type = 'theta', $top = 7)
    {
        $this->type = $type;
        $this->top = $top;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.currency');
    }
}
