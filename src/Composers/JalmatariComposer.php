<?php

namespace Jalmatari\Composers;

use Illuminate\View\View;
use Jalmatari\Funs\Funs;
use Jalmatari\Models\tables_cols;

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
        if (request()->is('login'))
            $this->login($view);
        if (request()->is('register'))
            $this->register($view);
    }


    private function login(View $view)
    {
        $col = setting('authLoginCol');
        if (is_null($col))
            $col = 'email';
        $col = tables_cols::whereRaw('TABLE_ID=(select id from ' . db_prefix() . 'tables where name ="users" limit 1)')
            ->where('COLUMN_NAME', $col)
            ->first();
        $view->with('col', $col);


    }


    private function register(View $view)
    {
        $cols = Funs::SettingAsArr('authRegisterCols');
        if (count($cols) == 0)
            $cols = [ "email", "name", "password" ];
        $cols = tables_cols::whereRaw('TABLE_ID=(select id from ' . db_prefix() . 'tables where name ="users" limit 1)')
            ->whereIn('COLUMN_NAME', $cols)
            ->get();

        $password = $cols->where('COLUMN_NAME', 'password');
        if ($password->count()) {
            $password = $password->first();

            //Remove password from the collection
            $cols = $cols->reject(function ($value, $key) {
                return $value->COLUMN_NAME == 'password';
            });
        }
        else
            $password = null;
        $view->with('cols', $cols);
        $view->with('password', $password);
    }
}
