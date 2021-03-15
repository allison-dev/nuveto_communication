<?php

namespace App\Services;

interface InvoicesServiceInterface
{
	public function index();

	public function store();

	public function show($id);

	public function update($id);

	public function destroy($id);

	public function flashNotFound();

	public function flashSuccessStore();

	public function flashSuccessUpdate();

	public function flashSuccessDestroy();
}
