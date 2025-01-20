<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;

/**
 * Event controller class
 * 
 * @package App\Http\Controllers
 */
class EventController extends BaseController
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the events.
     * @Request({
     *     summary: Get all events,
     *     description: Get all events,
     *     tags: Events
     * })
     * @Response({
     *     code: 200,
     *     description: return all events,
     *     ref: Event
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @return JsonResponse response success (all events) or error
     */
    public function index(): JsonResponse
    {   
        try {
            return $this->sendResponse($this->eventRepository->getAll(), "Events retrieved successfully.");
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Store a newly created event.
     * @Request({
     *     summary: Create a new event,
     *     description: Create a new event,
     *     tags: Events
     * })
     * @Response({
     *     code: 200,
     *     description: Event created successfully,
     *     ref: Event
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request Http request.
     * @return JsonResponse response success (event created) or error
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if ($this->eventRepository->get(['name' => $data['name']]) || $this->eventRepository->get(['date' => $data['date']])) {
                return $this->sendResponse([], 'Event already exists', 400);
            }
            return $this->sendResponse([$this->eventRepository->create($request->all())], "Event created successfully.");
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Display the specified event.
     * @Request({
     *     summary: Get an event,
     *     description: Get an event,
     *     tags: Events
     * })
     * @Response({
     *     code: 200,
     *     description: Event retrieved successfully,
     *     ref: Event
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param int $id Event id.
     * @return JsonResponse response success (event) or error
     */
    public function show(int $id): JsonResponse
    {
        try {
            return $this->sendResponse($this->eventRepository->getById($id), "Event retrieved successfully.");
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update the specified event.
     * @Request({
     *     summary: Update an event,
     *     description: Update an event,
     *     tags: Events
     * })
     * @Response({
     *     code: 200,
     *     description: Event updated successfully,
     *     ref: Event
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request Http request.
     * @param int $id Event id.
     * @return JsonResponse response success (event updated) or error
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $this->eventRepository->update($id, $request->all());
            return $this->sendResponse($this->eventRepository->getById($id), 'Event updated successfully');
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Remove the specified event.
     * @Request({
     *     summary: Delete an event,
     *     description: Delete an event,
     *     tags: Events
     * })
     * @Response({
     *     code: 200,
     *     description: Event deleted successfully,
     *     ref: Event
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param int $id Event id.
     * @return JsonResponse response success (event deleted) or error
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->eventRepository->delete($id);
            return $this->sendResponse([], 'Event deleted successfully');
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }
}
