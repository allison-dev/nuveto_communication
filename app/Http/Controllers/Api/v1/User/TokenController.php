<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\BaseController;
use App\Services\TokenService;

class TokenController extends BaseController
{
	private $tokenService;

	public function __construct(TokenService $tokenService)
	{
		$this->tokenService = $tokenService;
	}

	public function token()
	{
		if (is_null(request('email'))) {
			return $this->responseError(trans('system.not_authorizad_m', ['value' => trans('system.user')]), 401);
		}

		$guardAttempt = $this->tokenService->userAttempt();

		if (is_null(auth()->user())) {
			return $this->responseError(trans('system.not_authorizad_m', ['value' => trans('system.user')]), 401);
		}

		if (!$guardAttempt) {

			if (!is_null(auth()->user()->token())) {
				auth()->user()->token()->revoke();
			}

			auth()->logout();

			return $this->responseError(trans('system.not_authorizad_m', ['value' => trans('system.user'),]), 401);
		}

		$tokenInfo = $this->tokenService->generateToken();

		return $this->responseSuccess($this->tokenService->values($tokenInfo));
	}
}
