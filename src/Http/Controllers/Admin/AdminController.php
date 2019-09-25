<?php

namespace Jalmatari\Http\Controllers\Admin;

use Auth;
use AutoController;
use Input;
use Jalmatari\Http\Controllers\Core\MyBaseController;
use Jalmatari\Models\tables;
use Redirect;
use View;


class AdminController extends MyBaseController
{

    public function index()
    {
        return view('admin.home.index', [ 'title' => "لوحة التحكم" ]);
    }

    public function createSession()
    {
        cache()->clear();

        return auth()->check() ? redirect()->route('admin') : view('admin.home.login');
    }

    public function destroySession()
    {
        cache()->clear();
        auth()->logout();

        return redirect()->route('admin.login');
    }

    public function storeSession()
    {
        //dd(bcrypt(request('password')),request('password'));
        //dd(\Hash::make(request('password')),strlen(\Hash::make(request('password'))),request('password'),users::find(1)->password);
        //dd(Auth::attempt(request()->only('username', 'password')));


        cache()->clear();
        if (auth()->attempt(request()->only('username', 'password'), request()->has('remember')))
            return redirect()->route('admin');

        return redirect()->back()->withInput();
    }

    public function construction()
    {
        return view('admin.home.construction', [ 'title' => "قيد الإنشاء" ]);

    }

    public function unauthorized()
    {

        return view('admin.home.unauthorized', [ 'title' => "وصول ممنوع!" ]);
    }

    public function api()
    {
        $type = request('type');
        $data = request()->all();
        if ($type == "change table row order") {
            $table = 'Jalmatari\\Models\\' . request('table');
            $table = new $table;
            $table = $table::where('id', request('id'))->first();

            $orderBy = (int) request('order_by');
            $table->ord = ($table->ord) + $orderBy;
            $table->save();
            $data = $table;

        }

        return response()->json($data);
    }

    public function clearCache()
    {
        cache()->clear();

        return back()->with('alert', 'Cache cleared');
    }

    public function elfinderConnector()
    {
        include public_path('jalmatari/plugins/jalmatari/php/connector.php');
    }

    public function elfinderCkeditor()
    {
        return view('helpers.ckeditor');
    }

    public function documentation()
    {
        $tables = tables::all();

        return view('helpers.documentation', [ 'tables' => $tables]);
    }

}
