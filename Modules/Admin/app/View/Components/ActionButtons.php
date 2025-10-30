<?php

namespace Modules\Admin\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ActionButtons extends Component
{
    public $saveLabel;
    public $cancelUrl;

    /**
     * Create a new component instance.
     */
    public function __construct($cancelUrl = null, $saveLabel = 'Save')
    {
        $this->cancelUrl = $cancelUrl;
        $this->saveLabel = $saveLabel;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('admin::components.action-buttons');
    }
}
