<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Services\UserService;

class UserController extends Controller
{
	private $userService;

	public function __construct(UserService $userService)
	{
		$this->userService = $userService;
	}

	public function index()
	{
		$users = $this->userService->index();

		return view('pages.admin.users.index')->with(compact(['users']));
	}

	public function create()
	{
		$user = null;

		return view('pages.admin.users.create')->with(compact(['user']));
	}

	public function store(UserRequest $request)
	{
		$request->validated();

		$this->userService->store();

		$this->userService->flashSuccessStore();

		return redirect()->route('admin.users.index');
	}

	public function edit($id)
	{
		$user = $this->userService->show($id);

		if (!$user) {

			$this->userService->flashNotFound();

			return redirect()->route('admin.users.index');
		}

		return view('pages.admin.users.edit')->with(compact(['user']));
	}

	public function update(UserRequest $request, $id)
	{
		$request->validated();

		$user = $this->userService->update($id);

		if (!$user) {
			$this->userService->flashNotFound();

			return redirect()->route('admin.users.index');
		}

		$this->userService->flashSuccessUpdate();

		return redirect()->route('admin.users.index');
	}

	public function destroy($id)
	{
		!$this->userService->destroy($id) ? $this->userService->flashNotFound() : $this->userService->flashSuccessDestroy();

		return redirect()->route('admin.users.index');
	}
}
