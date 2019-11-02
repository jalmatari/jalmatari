<?php

namespace Jalmatari\Http\Controllers\Auth;

use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Jalmatari\Funs\Funs;
use Jalmatari\Http\Controllers\Core\MyBaseController;

class RegisterController extends MyBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * What Columns when registration.
     *
     * @var array
     */
    protected $cols = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $cols = [];
        $this->cols = Funs::SettingAsArr('authRegisterCols');
        if (count($this->cols) == 0)
            $this->cols = [ "email", "name", "password" ];
        foreach ($this->cols as $col) {
            $rule = 'required|string';
            if ($col == 'email')
                $rule .= '|email';
            if (in_array($col, [ 'email', 'username', 'phone' ]))
                $rule .= '|unique:users';
            else if ($col == 'password')
                $rule .= '|confirmed';

            $cols[ $col ] = $rule;
        }

        return Validator::make($data, $cols);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $cols = [];
        foreach ($this->cols as $col) {
            $row = $data[ $col ];
            if ($col == 'password')
                $row = bcrypt($row);
            $cols[ $col ] = $row;
        }
        session()->flash('alert', __("Your account has been successfully registered"));
        $user = User::create($cols);
        foreach ($cols as $col => $value)
            if (!in_array($col, [ 'name', 'email', 'password', ]))    //not in default fillable
                $user->{$col} = $value;

        $user->save();

        return $user;
    }
}
