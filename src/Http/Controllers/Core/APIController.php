<?php

namespace Jalmatari\Http\Controllers\Core;


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

    function gitUpgradeVersion()
    {
        $ver = setting('ver');
        $ver = explode('.', $ver);
        if (count($ver) == 1)
            $ver = [ 0, $ver ];
        $ver[1]++;
        if ($ver[1] >= 10)
            $ver = [ $ver[0] + 1, 0 ];
        return setting('ver', implode('.', $ver))->value;
    }

}
