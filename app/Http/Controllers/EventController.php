<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;

/**
 * Event controller class
 * 
 * @package App\Http\Controllers
 */
class EventController extends Controller
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the events.
     * 
     * @return JsonResponse response success (all events) or error
     */
    public function index(): JsonResponse
    {
        try {
            return response()->json($this->eventRepository->getAll());
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Store a newly created event.
     * 
     * @param Request $request Http request.
     * @return JsonResponse response success (event created) or error
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if ($this->eventRepository->get(['name' => $data['name']], ['date' => $data['date']])) {
                return response()->json(['message' => 'Event already exists'], 400);
            }
            return response()->json([$this->eventRepository->create($request->all())]);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Display the specified event.
     * 
     * @param int $id Event id.
     * @return JsonResponse response success (event) or error
     */
    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->eventRepository->getById($id));
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update the specified event.
     * 
     * @param Request $request Http request.
     * @param int $id Event id.
     * @return JsonResponse response success (event updated) or error
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json($this->eventRepository->update($id, 
                $request->all()) ? 'Event updated successfully' : 'Event not found');
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }

    /**
     * Remove the specified event.
     * 
     * @param int $id Event id.
     * @return JsonResponse response success (event deleted) or error
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->eventRepository->delete($id);
            return response()->json([$this->eventRepository->delete($id) ? 'Event deleted successfully' : 'Event not found']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
