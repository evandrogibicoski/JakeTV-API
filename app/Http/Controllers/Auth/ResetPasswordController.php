<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Services\TokenService;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
     * Get a validator for an incoming reset request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'password' => 'required'
        ]);
    }

    /**
     * Get a response for an incoming change password request.
     *
     * @param  Request  $request (including $userid, $password)
     * @return json response.
     */
    public function index(Request $request)
    {
        $token = $request->accessToken;
        $userid = $request->userid;        
        $password = $request->password;
        $pass = hash('sha512', $password);

        $data = array();
        $status = 200;

        if (!(TokenService::validateToken($token, $userid))) {
            $status = 401;
            $data = array(
                'status' => 401,
                'success' => false,
                'message' => 'Unathorized'
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

        $reset_validator = $this->validator($request);
        if ($reset_validator->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $reset_validator->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }
               
        
        try {
            $user = User::where('userid', $userid)
                    ->firstOrFail();

            $user->password = $pass;
            if($user->save()){
                $data = array(
                    'status' => 200,
                    'success' => true,
                    'message' => 'Password Change Successfully',
                    'data' => array(
                        'googleid' => $user->googleplusid,
                        'userid'  => $user->userid,
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'email' => $user->email,
                        'status' => $user->status,
                        'sessid' => $user->sessid,
                    ),
                );    
            } else {
                $status = 400;
                $data = array(
                    'status' => 400,
                    'success' => false,
                    'message' => 'Update Failed.'
                );
            }

            
        } catch (ModelNotFoundException $e) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Invalid Arguments.'
            );
        }

        return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }
}
