<?php

namespace App\Services;

interface PatientServiceInterface
{
	public function index();

	public function store();

	public function show($id);

	public function update($id);

	public function destroy($id);

	public function filter();

	public function flashNotFound();

	public function flashSuccessStore();

	public function flashSuccessUpdate();

	public function flashSuccessDestroy();
}
