<?php

namespace App\Services;

use Firebase\JWT\JWT;

class TokenService 
{
	public static function createToken($payload)
	{
		$key = config('app.key');
		$token = JWT::encode($payload, $key);
		
		return $token;
	} 

	public static function validateToken($token, $userid)
	{
		$key = config('app.key');
		try {
			$payload = JWT::decode($token, $key, ['HS256']);
			return true;
			
		} catch (\Exception $e) {
			return false;
		}
	}
}

?>

