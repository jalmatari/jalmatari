<?php

namespace Jalmatari\Composers;

use Illuminate\View\View;
use Jalmatari\Funs\Funs;
use Jalmatari\Models\permissions;
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
            ->orderBy('ORDINAL_POSITION')
            ->get();

        foreach ($cols as $col) {
            $col->inputType = $col->COLUMN_NAME == 'email' ? 'email' : 'text';
            if ($col->COLUMN_NAME == 'password')
                $col->inputType = 'password';
            $col->inputValue = old($col->COLUMN_NAME);
            //for security Reasons, Change the key of job_title
            if ($col->COLUMN_NAME == 'job_title') {
                $col->COLUMN_NAME = "acount_type";

                $acountTypes = permissions::active()
                    ->where('id', '!=', 2) //Not Manager
                    ->get()
                    ->pluck('name', 'id');
                $acountTypes = $acountTypes->map(function ($value) {
                    return __($value);
                });
                $col->inputType = 'select';
                $col->inputSource = $acountTypes;
            }
        }
        $view->with('cols', $cols);
    }
}
