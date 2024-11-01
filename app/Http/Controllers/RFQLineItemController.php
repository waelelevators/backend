<?php

namespace App\Http\Controllers;

use App\Models\RFQLineItem;
use App\Http\Requests\StoreRFQLineItemRequest;
use App\Http\Requests\UpdateRFQLineItemRequest;

class RFQLineItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreRFQLineItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRFQLineItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RFQLineItem  $rFQLineItem
     * @return \Illuminate\Http\Response
     */
    public function show(RFQLineItem $rFQLineItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RFQLineItem  $rFQLineItem
     * @return \Illuminate\Http\Response
     */
    public function edit(RFQLineItem $rFQLineItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRFQLineItemRequest  $request
     * @param  \App\Models\RFQLineItem  $rFQLineItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRFQLineItemRequest $request, RFQLineItem $rFQLineItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RFQLineItem  $rFQLineItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(RFQLineItem $rFQLineItem)
    {
        //
    }
}
