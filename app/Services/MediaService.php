<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class MediaService implements MediaServiceInterface
{
    private $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function index()
    {
        $medias = $this->mediaRepository->orderBy('id', 'DESC')->get();

        if (!count($medias)) {
            return false;
        }

        return $medias;
    }

    public function store()
    {
        return $this->mediaRepository->create($this->values());
    }

    public function show($id)
    {
        try {
            return $this->mediaRepository->where('conversationId', $id)->get();
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

        $this->mediaRepository->updateById($invoice->id, $this->values());

        return $invoice;
    }

    public function destroy($id)
    {
        $invoice = $this->show($id);

        if (!$invoice) {
            return false;
        }

        return $this->mediaRepository->deleteById($invoice->id);
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
