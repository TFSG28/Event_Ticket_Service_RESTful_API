<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Repositories\EventRepository;

class EventController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the events.
     */
    public function index()
    {
        $events = $this->eventRepository->getAll();
        return response()->json($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $this->eventRepository->create($request->all());
        return response()->json(['message' => 'Event created successfully']);
    }

    /**
     * Display the specified event.
     */
    public function show(string $id)
    {
        $event = $this->eventRepository->getById($id);
        return response()->json($event);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, string $id)
    {
        $this->eventRepository->update($id, $request->all());
        return response()->json(['message' => 'Event updated successfully']);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(string $id)
    {
        $this->eventRepository->delete($id);
        return response()->json(['message' => 'Event deleted successfully']);
    }
}
