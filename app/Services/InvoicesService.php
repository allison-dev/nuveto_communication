<?php

namespace App\Services;

use App\Repositories\InvoicesRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoicesService implements InvoicesServiceInterface
{
    private $invoicesRepository;

    public function __construct(InvoicesRepository $invoicesRepository)
    {
        $this->invoicesRepository = $invoicesRepository;
    }

    public function index()
    {
        $invoices = $this->invoicesRepository->orderBy('id', 'DESC')->get();

        if (!count($invoices)) {
            return false;
        }

        return $invoices;
    }

    public function store()
    {
        return $this->invoicesRepository->create($this->values());
    }

    public function show($id)
    {
        try {
            return $this->invoicesRepository->where('id', $id)->first();
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function update($id)
    {
        $invoice = $this->show($id);

        if (!$invoice) {
            return false;
        }

        $this->invoicesRepository->updateById($invoice->id, $this->values());

        return $invoice;
    }

    public function destroy($id)
    {
        $invoice = $this->show($id);

        if (!$invoice) {
            return false;
        }

        return $this->invoicesRepository->deleteById($invoice->id);
    }

    private function values()
    {
        return [
            'channel'   => request('channel'),
            'price'     => request('price'),
            'sessions'  => request('sessions'),
            'subtotal'  => request('subtotal'),
            'total'     => request('total'),
            'taxes'     => request('taxes'),
        ];
    }

    public function flashNotFound()
    {
        return flash(trans('system.not_found_m', ['value' => trans('system.invoices'),]))->error()->important();
    }

    public function flashSuccessStore()
    {
        return flash(trans('system.store_success_m', ['value' => trans('system.invoices'),]))->success();
    }

    public function flashSuccessUpdate()
    {
        return flash(trans('system.update_success_m', ['value' => trans('system.invoices'),]))->success()->important();
    }

    public function flashSuccessDestroy()
    {
        return flash(trans('system.destroy_success_m', ['value' => trans('system.invoices'),]))->success()->important();
    }
}
