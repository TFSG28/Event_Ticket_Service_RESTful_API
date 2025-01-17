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
        try {
            $events = $this->eventRepository->getAll();
            return response()->json($events);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        try {
            $this->eventRepository->create($request->all());
            return response()->json(['message' => 'Event created successfully']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Display the specified event.
     */
    public function show(string $id)
    {
        try {
            $event = $this->eventRepository->getById($id);
            return response()->json($event);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->eventRepository->update($id, $request->all());
            return response()->json(['message' => 'Event updated successfully']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(string $id)
    {
        try {
            $this->eventRepository->delete($id);
            return response()->json(['message' => 'Event deleted successfully']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
