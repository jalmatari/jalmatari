<?php

namespace Jalmatari\Http\Controllers\Core;

use Jalmatari\Funs\Funs;


class APIController extends MyBaseController
{


    public function api()
    {
        $data = [];
        $ac = request('ac');
        dd($ac);
        if (method_exists($this, $ac))
            $data = $this->{$ac}();//call the function

        return response()->json($data);
    }

    function tdbrSourceList()
    {
        return Funs::AddKeyValueToArr(Funs::TdbrSourcesList());
    }

}
