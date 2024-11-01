<?php

namespace App\Http\Controllers;

use App\ElevatorRoomType;
use App\Http\Requests\ElevatorRoomTypeStoreRequest;
use App\Http\Requests\ElevatorRoomTypeUpdateRequest;
use App\Http\Resources\ElevatorRoomTypeCollection;
use App\Http\Resources\ElevatorRoomTypeResource;
use Illuminate\Http\Request;

class ElevatorRoomTypeController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ElevatorRoomTypeCollection
     */
    public function index(Request $request)
    {
        $elevatorRoomTypes = ElevatorRoomType::all();

        return new ElevatorRoomTypeCollection($elevatorRoomTypes);
    }

    /**
     * @param \App\Http\Requests\ElevatorRoomTypeStoreRequest $request
     * @return \App\Http\Resources\ElevatorRoomTypeResource
     */
    public function store(ElevatorRoomTypeStoreRequest $request)
    {
        $elevatorRoomType = ElevatorRoomType::create($request->validated());

        return new ElevatorRoomTypeResource($elevatorRoomType);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\elevatorRoomType $elevatorRoomType
     * @return \App\Http\Resources\ElevatorRoomTypeResource
     */
    public function show(Request $request, ElevatorRoomType $elevatorRoomType)
    {
        return new ElevatorRoomTypeResource($elevatorRoomType);
    }

    /**
     * @param \App\Http\Requests\ElevatorRoomTypeUpdateRequest $request
     * @param \App\elevatorRoomType $elevatorRoomType
     * @return \App\Http\Resources\ElevatorRoomTypeResource
     */
    public function update(ElevatorRoomTypeUpdateRequest $request, ElevatorRoomType $elevatorRoomType)
    {
        $elevatorRoomType->update($request->validated());

        return new ElevatorRoomTypeResource($elevatorRoomType);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\elevatorRoomType $elevatorRoomType
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ElevatorRoomType $elevatorRoomType)
    {
        $elevatorRoomType->delete();

        return response()->noContent();
    }
}
