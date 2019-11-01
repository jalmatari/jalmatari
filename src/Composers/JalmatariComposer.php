<?php

namespace Jalmatari\Composers;

use Carbon\Carbon;
use Illuminate\View\View;
use Jalmatari\Funs\Funs;

class JalmatariComposer
{

    /**
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $this->loginRegisterHeader($view);
    }


    private function loginRegisterHeader(View $view)
    {
        if (request()->is([ 'login','register','password/reset' ])) {
            dd('s');
            $view->with('headerHTML', '<link href="' . url('css/login.css') . '" rel="stylesheet">');
        }

    }
}
