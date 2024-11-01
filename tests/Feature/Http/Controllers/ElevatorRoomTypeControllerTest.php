<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ElevatorRoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ElevatorRoomTypeController
 */
class ElevatorRoomTypeControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_behaves_as_expected()
    {
        $elevatorRoomTypes = ElevatorRoomType::factory()->count(3)->create();

        $response = $this->get(route('elevator-room-type.index'));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ElevatorRoomTypeController::class,
            'store',
            \App\Http\Requests\ElevatorRoomTypeStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function store_saves()
    {
        $response = $this->post(route('elevator-room-type.store'));

        $response->assertCreated();
        $response->assertJsonStructure([]);

        $this->assertDatabaseHas(elevatorRoomTypes, [ /* ... */ ]);
    }


    /**
     * @test
     */
    public function show_behaves_as_expected()
    {
        $elevatorRoomType = ElevatorRoomType::factory()->create();

        $response = $this->get(route('elevator-room-type.show', $elevatorRoomType));

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function update_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ElevatorRoomTypeController::class,
            'update',
            \App\Http\Requests\ElevatorRoomTypeUpdateRequest::class
        );
    }

    /**
     * @test
     */
    public function update_behaves_as_expected()
    {
        $elevatorRoomType = ElevatorRoomType::factory()->create();

        $response = $this->put(route('elevator-room-type.update', $elevatorRoomType));

        $elevatorRoomType->refresh();

        $response->assertOk();
        $response->assertJsonStructure([]);
    }


    /**
     * @test
     */
    public function destroy_deletes_and_responds_with()
    {
        $elevatorRoomType = ElevatorRoomType::factory()->create();

        $response = $this->delete(route('elevator-room-type.destroy', $elevatorRoomType));

        $response->assertNoContent();

        $this->assertDeleted($elevatorRoomType);
    }
}
