<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
	return redirect('admin');
});

Route::prefix('admin')->name('admin.')->group(function () {
	Auth::routes();

	Route::get('/logout', function () {
		Auth::logout();
		return redirect('admin');
	});

	Route::middleware(['auth'])->group(function () {
		Route::get('/', function () {
			return redirect('admin');
		});

		Route::get('', 'HomeController@index')->name('admin');

		Route::post('addresses/showByPostcode', 'Admin\AddressController@showByPostcode')->name('address.showByPostcode');

		Route::resource('usuarios', 'Admin\UserController', ['as' => 'users'])->names([
			'index'   => 'users.index',
			'create'  => 'users.create',
			'store'   => 'users.store',
			'edit'    => 'users.edit',
			'update'  => 'users.update',
			'destroy' => 'users.destroy',
		]);

		Route::resource('pacientes', 'Admin\PatientController', ['as' => 'patients'])->names([
			'index'   => 'patients.index',
			'create'  => 'patients.create',
			'store'   => 'patients.store',
			'edit'    => 'patients.edit',
			'update'  => 'patients.update',
			'destroy' => 'patients.destroy',
		]);

		Route::post('pacientes/filtrar', 'Admin\PatientController@filter')->name('patients.json');

		Route::resource('medicos', 'Admin\DoctorController', ['as' => 'doctors'])->names([
			'index'   => 'doctors.index',
			'create'  => 'doctors.create',
			'store'   => 'doctors.store',
			'edit'    => 'doctors.edit',
			'update'  => 'doctors.update',
			'destroy' => 'doctors.destroy',
		]);

		Route::post('medicos/filtrar', 'Admin\DoctorController@filter')->name('doctors.json');

		Route::resource('agendamentos', 'Admin\SchedulingController', ['as' => 'schedules'])->names([
			'index'   => 'schedules.index',
			'create'  => 'schedules.create',
			'store'   => 'schedules.store',
			'edit'    => 'schedules.edit',
			'update'  => 'schedules.update',
			'destroy' => 'schedules.destroy',
		]);

		Route::get('unauthorized', 'Admin\ErrorController@error403')->name('errors.403');
	});
});
