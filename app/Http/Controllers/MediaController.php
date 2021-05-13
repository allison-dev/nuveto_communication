<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MediaService;

class MediaController extends Controller
{
    private $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index()
    {
        $medias = $this->mediaService->index();
        return view('pages.medias.index')->with(compact(['medias']));
    }
}
