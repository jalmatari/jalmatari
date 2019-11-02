<?php

namespace Jalmatari\Composers;

use Illuminate\View\View;
use Jalmatari\Models\tables_cols;

class AdminComposer
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
        if (request()->is('admin/login'))
            $this->login($view);
    }


    private function login(View $view)
    {
        $col = setting('authAdminLoginCol');
        if (is_null($col))
            $col = 'email';
        $col = tables_cols::whereRaw('TABLE_ID=(select id from ' . db_prefix() . 'tables where name ="users" limit 1)')
            ->where('COLUMN_NAME', $col)
            ->first();
        $view->with('col', $col);
    }

}
