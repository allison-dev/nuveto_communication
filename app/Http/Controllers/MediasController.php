<?php

namespace App\Http\Controllers;

use App\Services\MediaService;
use Illuminate\Http\Request;

class MediasController extends Controller
{
    private $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medias = $this->mediaService->index();
        return view('pages.medias.index')->with(compact(['medias']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->interactionid;
        $medias = $this->mediaService->show($id);
        $conversationId = $id;
        return view('pages.medias.index')->with(compact(['medias', 'conversationId']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medias  $medias
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }
}
