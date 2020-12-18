<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Database;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Services\TokenService;
use Illuminate\Support\Facades\Auth;

use Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest', ['except' => 'logout']);
    }


    /**
     * Get a validator for an incoming login request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|max:255|email',
            'password' => 'required|max:255'
        ]);
    }

    /**
     * Get a response for an incoming login request.
     *
     * @param  Request  $request (including $email, $password)
     * @return json response.
     */
    public function login(Request $request){

        $data = array();
        $status = 200;

        $login_validator = $this->validator($request);
        if ($login_validator->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $login_validator->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

        //Retrieve the name input field
        $email = $request->input('email');

        $password = $request->password;
        $pass = hash('sha512', $password);

        $data = array();
        $status = 200;

        try {
            $user = User::where('email', '=', $email)
                    ->where('password', $pass)
                    ->firstOrFail();

            $payload = [
                'userid' => $user->userid,
                'email' => $user->email
            ];

            $token = TokenService::createToken($payload);

            $data = array(
                'status' => 200,
                'success' => true,
                'message' => 'Login Successfully.',
                'accessToken' => $token,
                'data' => array(
                    'googleid' => $user->googleplusid,
                    'userid'  => $user->userid,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'email' => $user->email,
                    'status' => $user->status,
                    'sessid' => $user->sessid
                ),
            );
        } catch (ModelNotFoundException $e) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Please enter valid username and password.'
            );
        }

        return response(json_encode($data), $status, ['Content-Type', 'application/json']);
   }

   public function redirectToProvider() {
     return Socialite::driver('google')->redirect();
   }

   public function handleProviderCallback() {
     $googleUser = Socialite::driver('google')->user();

     $user = User::where('email', '=', $googleUser->email)->first();

     if (!$user) {
       $user = User::create([
         'fname' => $googleUser->user->name->givenName,
         'lname' => $googleUser->user->name->familyName,
         'email' => $googleUser->email
         ]);
     }

     Auth::guard('users')->login($user);

     $host;

     if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
       $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
     }
     else {
       $host = $_SERVER['HTTP_HOST'];
     }

     return redirect('http://'.$host);
   }
}
