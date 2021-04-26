<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\BaseController;
use App\Services\UserService;

class UserController extends BaseController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $verify_code = sha1('Nuveto Sigma Verify');
        if ($verify_code == request('sigma_id')) {
            $this->userService->store();

            $this->userService->flashSuccessStore();

            return redirect()->away(request('redirect'));
        }
    }
}
