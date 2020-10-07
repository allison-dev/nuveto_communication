<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService implements UserServiceInterface
{
	private $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function index()
	{
		$users = $this->userRepository->orderBy('name')->get();

		if (!count($users)) {
			return false;
		}

		return $users;
	}

	public function store()
	{
		return $this->userRepository->create($this->values('store'));
	}

	public function show($id)
	{
		try {
			return $this->userRepository->where('id', $id)->first();
		} catch (ModelNotFoundException $e) {
			return false;
		}
	}

	public function update($id)
	{
		$user = $this->show($id);

		if (!$user) {
			return false;
		}

		$this->userRepository->updateById($user->id, $this->values('update'));

		return $user;
	}

	public function destroy($id)
	{
		$user = $this->show($id);

		if (!$user) {
			return false;
		}

		return $this->userRepository->deleteById($user->id);
	}

	private function values($type)
	{
		$values = [
			'name'  => request('name'),
			'email' => request('email'),
		];

		if ($type == 'store' || !is_null(request('password'))) {
			$values['password'] = bcrypt(request('password'));
			$values['password_confirmation'] = bcrypt(request('password_confirmation'));
		}
		return $values;
	}

	public function flashNotFound()
	{
		return flash(trans('system.not_found_m', ['value' => trans('system.user'),]))->error()->important();
	}

	public function flashSuccessStore()
	{
		return flash(trans('system.store_success_m', ['value' => trans('system.user'),]))->success();
	}

	public function flashSuccessUpdate()
	{
		return flash(trans('system.update_success_m', ['value' => trans('system.user'),]))->success()->important();
	}

	public function flashSuccessDestroy()
	{
		return flash(trans('system.destroy_success_m', ['value' => trans('system.user'),]))->success()->important();
	}
}
