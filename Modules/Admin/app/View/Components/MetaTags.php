<?php

namespace Modules\Admin\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MetaTags extends Component
{
    public $metaData;

    /**
     * Create a new component instance.
     */
    public function __construct($metaData = null)
    {
        $this->metaData = $metaData;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('admin::components.meta-tags');
    }
}
