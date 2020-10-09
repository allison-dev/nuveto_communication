<?php

namespace App\Services;

interface TokenServiceInterface
{
	public function userAttempt();

	public function values($tokenInfo);

	public function generateToken();
}
