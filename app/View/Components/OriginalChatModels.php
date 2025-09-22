<?php

namespace App\View\Components;

use App\Models\ApiManagement;
use Illuminate\View\Component;
use App\Models\SubscriptionPlan;
use App\Models\FineTuneModel;

class OriginalChatModels extends Component
{
    public $models;
    public $fine_tunes;
    public $default_model;
    public $model_list;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        # Apply proper model based on role and subsciption
        if (auth()->user()->group == 'user') {
            $models = explode(',', config('settings.free_tier_models'));
        } elseif (!is_null(auth()->user()->plan_id)) {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            $models = explode(',', $plan->model_chat);
        } else {            
            $models = explode(',', config('settings.free_tier_models'));
        }

        $this->models = $models;
        $this->fine_tunes = FineTuneModel::all();
        $this->default_model = auth()->user()->default_model_chat;

        $models = array_map('trim', $models); 

        $lists = ApiManagement::get(); 

        foreach ($lists as $list)
            foreach ($models as $model) {
                if ($model == $list->model) {
                    $checked = ($this->default_model == $list->model) ? 'checked': '';
                    $newSpan = ($list->new) ? '<span class="chat-new-model-info">'. __('New').'</span>' : '';
                    $this->model_list .= '<div class="col-md-4 col-sm-12">
                                            <input type="radio" id="control_'.$list->id.'" name="model" onclick="handleClick(this);" value="'. $list->model .'"' . $checked . '>
                                            <label for="control_'.$list->id.'">
                                                <h6 class="pt-3 font-weight-bold">
                                                    '. $list->logo . '
                                                    '. __($list->title) . '
                                                </h6>
                                                <p class="text-muted">'. __($list->description) .'</p>
                                            </label>
                                            ' . $newSpan . '
                                        </div>';
                }
        }    
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.original-chat-models');
    }
}
