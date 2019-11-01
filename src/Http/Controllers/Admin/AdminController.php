<?php

namespace Jalmatari\Http\Controllers\Admin;

use Artisan;
use Auth;
use AutoController;
use Jalmatari\Funs\Funs;
use Jalmatari\Http\Controllers\Core\MyBaseController;
use Jalmatari\Models\tables;
use Jalmatari\Models\tables_cols;
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
        //if logout from home go to home login
        $route = !request()->is('logout') ? 'admin.' : '';
        $route .= 'login';

        return redirect()->route($route)->with('alert', "تم تسجيل خروجك بنجاح");;
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

        return view('helpers.documentation', [ 'tables' => $tables ]);
    }

    public function publishConfig()
    {
        $msg = 'Copied File [/vendor/jalmatari/jalmatari/src/config.php] To [/config/jalmatari.php]';
        $config = config('jalmatari');
        if (is_null($config))
            Artisan::call('vendor:publish --tag jalmatari-config');
        else
            $msg = 'Jalmatari Config file is existed before!';

        return back()->with('alert', $msg);
    }

    public function authSetup()
    {

        $tables = tables_cols::whereRaw('COLUMN_NAME not in ("id","created_at","updated_at","api_token","created_by_app","email_verified_at","photo","status","remember_token","user_id","permissions") and TABLE_ID in (select id from ' . db_prefix() . 'tables where name in("users","users_info"))')
            ->orderBy('ORDINAL_POSITION')
            ->get()
            ->groupBy('TABLE_ID');
        $authList = $tables
            ->first()
            ->whereIn('COLUMN_NAME', [ 'name', 'username', 'phone', 'email', 'job_title' ])
            ->pluck('TITLE', 'COLUMN_NAME');

        $authRegisterCols = Funs::SettingAsArr('authRegisterCols');
        $authLoginCols = Funs::SettingAsArr('authLoginCols');
        $authAdminLoginCols = Funs::SettingAsArr('authAdminLoginCols');
        if (count($authRegisterCols) == 0)
            $authRegisterCols = [ 'name', 'email', 'password' ];
        if (count($authLoginCols) == 0)
            $authLoginCols = [ 'email', 'password' ];
        if (count($authAdminLoginCols) == 0)
            $authAdminLoginCols = [ 'username', 'password' ];

        return view('admin.auth.setup', compact('tables', 'authRegisterCols', 'authLoginCols', 'authList', 'authAdminLoginCols'));
    }

    public function saveAuthCols()
    {
        setting('authRegisterCols', json_encode(request('registerCols')));
        setting('authLoginCols', json_encode(request('loginCols')));
        setting('authAdminLoginCols', json_encode(request('adminLoginCols')));

        return response()->json(true);
    }

}
