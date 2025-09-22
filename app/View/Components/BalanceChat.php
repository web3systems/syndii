<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BalanceChat extends Component
{
    public $model;
    public $balance;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->balance = (auth()->user()->tokens == -1) ? __('Unlimited') : auth()->user()->tokens + auth()->user()->tokens_prepaid;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.balance-chat');
    }
}
