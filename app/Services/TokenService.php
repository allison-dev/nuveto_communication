<?php

namespace App\Services;

use Carbon\Carbon;

class TokenService implements TokenServiceInterface
{
	public function userAttempt()
	{
		return auth()->attempt([
			'email'    => request('email'),
			'password' => request('password'),
		]);
	}

	public function generateToken()
	{
		$tokenCreate = auth()->user()->createToken('api');

		$token = $tokenCreate->token;

		$token->expires_at = Carbon::now()->addMinutes(30);

		$token->save();

		return $tokenCreate;
	}

	public function values($tokenInfo)
	{
		return [
			'id'        => auth()->user()->id,
			'name'      => auth()->user()->name,
			'email'     => auth()->user()->email,
			'token'     => $tokenInfo->accessToken,
			'expiresAt' => Carbon::parse($tokenInfo->token->expires_at)->toDateTimeLocalString(),
		];
	}
}
