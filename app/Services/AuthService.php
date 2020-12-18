<?php
namespace App\Services;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\User;
use App\Models\AdminUser;
use Mail;
use Auth;

class AuthService {
	use SendsPasswordResetEmails;

	public function login ($email, $password) {
		$user = User::where('email', '=', $email)->first();

		if (!$user) return null;

		$pass = hash('sha512', $password);

		if ($pass != $user->password) {
			return null;
		}

		Auth::guard('users')->login($user);

		return $user;
	}

	public function loginAdmin ($email, $password) {
		$user = AdminUser::where('email', '=', $email)->first();

		if (!$user) return null;

		$pass = hash('md5', $password);

		if ($pass != $user->password) {
			return null;
		}

		Auth::guard('admin')->login($user);

		return $user;
	}

	public function register ($first, $last, $email, $password) {
		$user = User::where('email', '=', $email)->first();

		if ($user) {
			return [
				'success' => false,
				'message' => 'An account exists with that email already.'
			];
		}

		$user = User::create([
				'fname' => $first,
				'lname' => $last,
				'email' => $email,
 		]);

		$user->password = hash('sha512', $password);

		$user->save();

		Auth::guard('users')->login($user);

		return [
			'success' => true
		];
	}

	public function forgotPassword ($email) {
		$user = User::where('email', '=', $email)
						->where('status', 1)->first();

		if ($user) {
			$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
			$pass = [];
			$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
			for ($i = 0; $i < 8; $i++) {
				$n = rand(0, $alphaLength);
				$pass[] = $alphabet[$n];
			}

			$password = implode($pass);
			$hashed = hash('sha512', $password);

			$user->password = $hashed;

			$user->save();

			$host;

      if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
      }
      else {
        $host = $_SERVER['HTTP_HOST'];
      }

			$data = [
				'password' => $password,
				'sitefront' => 'http://'.$host
			];

			Mail::send('Auth/mail', $data, function($message) use ($email, $user) {
					$message->to($email, $user->fname . ' ' . $user->lname)->subject("Let's get you back in!");
			 		$message->from('jaketvmanager@gmail.com','No Reply');
			});
		}
	}

	public function updateProfile ($profile) {
		$user = Auth::guard('users')->user();

		$user->fill($profile);

		$user->save();

		return $user;
	}

	public function changePassword ($password) {
		$user = Auth::guard('users')->user();

		$user->password = hash('sha512', $password);

		$user->save();

		return $user;
	}
}
