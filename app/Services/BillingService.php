<?php

namespace App\Services;

use App\Repositories\BillingRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BillingService implements BillingServiceInterface
{
    private $billingRepository;

    public function __construct(BillingRepository $billingRepository)
    {
        $this->billingRepository = $billingRepository;
    }

    public function index()
    {
        $billing = $this->billingRepository->orderBy('network')->get();

        if (!count($billing)) {
            return false;
        }

        return $billing;
    }

    public function store()
    {
        return $this->billingRepository->create($this->values());
    }

    public function show($id)
    {
        try {
            return $this->billingRepository->where('id', $id)->first();
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function update($id)
    {
        $patient = $this->show($id);

        if (!$patient) {
            return false;
        }

        $this->billingRepository->updateById($patient->id, $this->values());

        return $patient;
    }

    public function destroy($id)
    {
        $patient = $this->show($id);

        if (!$patient) {
            return false;
        }

        return $this->billingRepository->deleteById($patient->id);
    }

    public function filter()
    {
        $billing = $this->billingRepository->filter();

        return !is_null($billing) ? $billing : [];
    }

    private function values()
    {
        $values = [
            'network'   => request('network'),
            'sessions'  => request('sessions'),
            'price'     => str_replace(['R$', '.', ','], ['', '', '.'], request('price')),
        ];

        return $values;
    }

    public function flashNotFound()
    {
        return flash(trans('system.not_found_m', ['value' => trans('system.billing'),]))->error()->important();
    }

    public function flashSuccessStore()
    {
        return flash(trans('system.store_success_m', ['value' => trans('system.billing'),]))->success();
    }

    public function flashSuccessUpdate()
    {
        return flash(trans('system.update_success_m', ['value' => trans('system.billing'),]))->success()->important();
    }

    public function flashSuccessDestroy()
    {
        return flash(trans('system.destroy_success_m', ['value' => trans('system.billing'),]))->success()->important();
    }
}
