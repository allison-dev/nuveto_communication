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

		Route::resource('faturamento', 'Admin\BillingController', ['as' => 'billings'])->names([
			'index'   => 'billings.index',
			'create'  => 'billings.create',
			'store'   => 'billings.store',
			'edit'    => 'billings.edit',
			'update'  => 'billings.update',
			'destroy' => 'billings.destroy',
		]);

		Route::resource('empresa', 'Admin\CompanyController', ['as' => 'company'])->names([
			'index'   => 'company.index',
			'create'  => 'company.create',
			'store'   => 'company.store',
			'edit'    => 'company.edit',
			'update'  => 'company.update',
			'destroy' => 'company.destroy',
		]);

        Route::resource('faturas', 'Admin\InvoicesController', ['as' => 'invoices'])->names([
			'index'    => 'invoices.index',
			'create'   => 'invoices.create',
			'store'    => 'invoices.store',
			'edit'     => 'invoices.edit',
			'update'   => 'invoices.update',
			'destroy'  => 'invoices.destroy',
		]);

        Route::post('faturas/gerar', 'Admin\InvoicesController@generate')->name('invoices.generate');

		Route::get('unauthorized', 'Admin\ErrorController@error403')->name('errors.403');
	});
});
