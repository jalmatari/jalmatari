<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Http\Controllers\Core;

use Auth;
use AutoController;
use DB;
use Jalmatari\Models\errors;
use Jalmatari\Funs\Funs;
use Redirect;
use View;

class ErrorsController extends MyBaseController
{

    public function __construct()
    {
        $this->init();
    }



    public function delete_all()
    {
        DB::table('errors')->truncate();

        return redirect()->route($this->mainRoute);
    }

    public function error($id)
    {
        $error = errors::find($id);
        $error->status = 1;
        $error->save();

        return '<div id="sf-resetcontent" class="sf-reset">
<h1 style=" overflow: auto; "><pre>' . $error->request . '</pre></h1>
<h1 style=" overflow: auto; ">' . $error->url . '</h1>
<h1 style=" overflow: auto; ">' . $error->exception_msg . '</h1>
<div>' . $error->rendered_page;
    }


    public function getData($data = [])
    {
        $data=[
            'user_id'=>[
                'user_id',
                'code' => '$d!=0?"<a href=\"".route("admin.users.edit",$d)."\">".Funs::GetUserName($d,1)."</a>":Funs::GetUserName($d,1)'
            ]

        ];

        $this->listBtns = [
            'delete',
            [ 'info ', 'error', 'إستعراض', 'folder-open' ],
        ];
        return parent::getData($data);
    }

}
