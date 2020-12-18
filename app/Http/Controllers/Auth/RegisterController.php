<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class RegisterController extends Controller
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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'googleid' => 'required|max:255',
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:tbl_user',
            'password' => 'required',
        ]);
    }

   
    /**
     * Get a response for an incoming registration request.
     *
     * @param  Request  $request (including $googleid, $fname, $lname, $email, $password)
     * @return json response.
     */
    public function index(Request $request)
    {
        $data = array();
        $status = 200;

        $v = $this->validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $v->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

        $googleid = $request->googleid;
        $fname = $request->fname;
        $lname = $request->lname;
        $email = $request->email;
        $password = $request->password;
        $pass = hash('sha512', $password);
       

        try{
            $counter = User::where('email', $email)->count();
            if ($counter > 0) 
            {
                $status = 400;
                $data = array(
                    'status' => 400,
                    'success' => false,
                    'message' => 'User Already Registered.'
                );
                return response(json_encode($data), $status, ['Content-Type', 'application/json']);
            }

            $user = new User;
            $user->googleplusid = $googleid;
            $user->fname = $fname;
            $user->lname = $lname;
            $user->email = $email;
            $user->password = $pass;            
            $user->picture = '';
            $user->catid = '';
            $user->status = 1;
            $user->cr_date = Carbon::now();
            $user->modify_date = Carbon::now();
            $user->sessid = '';

            if ($user->save()) {
                $data = array(
                    'status' => 200,
                    'success' => true,
                    'message' => 'Profile added successfully.',
                    'data' => array(
                        'googleid' => $user->googleplusid,
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'email' => $user->email
                    ),
                );
            } else {
                $status = 401;
                $data = array(
                    'status' => 401,
                    'success' => false,
                    'message' => 'Profile can not update.'
                );
            }
            
        } catch (Exception $e){
            $status = 500;
            $data = array(
                'status' => 500,
                'success' => false,
                'message' => 'Profile can not update.'
            );
        }
        
        return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }
}
