<?php

namespace Modules\Admin\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Banner extends Component
{
    public $bannerData;

    /**
     * Create a new component instance.
     */
    public function __construct($bannerData = null)
    {
        $this->bannerData = $bannerData;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('admin::components.banner');
    }
}
