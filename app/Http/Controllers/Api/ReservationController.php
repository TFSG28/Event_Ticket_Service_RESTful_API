<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Repositories\ReservationRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\EventRepository;

/**
 * Reservation controller class
 * 
 * @package App\Http\Controllers
 */
class ReservationController extends BaseController
{
    private ReservationRepository $reservation_repository;
    private EventRepository $event_repository;

    /**
     * Constructor
     * 
     * @param ReservationRepository $reservationRepository Reservation repository.
     */
    public function __construct(ReservationRepository $reservation_repository)
    {
        $this->reservation_repository = $reservation_repository;
        $this->event_repository = new EventRepository;
    }

    /**
     * Display a listing of the reservations.
     * 
     * @Request({
     *     summary: Get all reservations,
     *     description: Get all reservations,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Reservations retrieved successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request Http request.
     * @return JsonResponse response success (all reservations) or error
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $valid_inputs = ['event_id', 'user_id'];
            $query_params = array_filter($request->query(), function ($key) use ($valid_inputs) {
                return in_array($key, $valid_inputs);
            }, ARRAY_FILTER_USE_KEY);
            return $this->sendResponse($this->reservation_repository->getByQueryParams($query_params), "Reservations retrieved successfully.");
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Store a newly created reservation.
     * 
     * @Request({
     *     summary: Create a reservation,
     *     description: Create a reservation,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Reservation created successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request Http request.
     * @return JsonResponse response success (reservation created) or error
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        try {
            // Start a transaction
            DB::beginTransaction();

            // Iterate over one or multiple reservations
            $this->validateBodyKeys($data);
            $this->validateReservation($data);
            $reservation = $this->reservation_repository->create($data);

            // Commit the transaction
            DB::commit();

            // Return the reservation and a success message
            return $this->sendResponse($reservation, "Reservation created successfully.");
        } catch (\Exception $exception) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Display the specified reservation.
     * 
     * @Request({
     *     summary: Get a reservation,
     *     description: Get a reservation,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Reservation retrieved successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param int $id Reservation id.
     * @return JsonResponse response success (reservation) or error
     */
    public function show(int $id): JsonResponse
    {
        try {
            return $this->sendResponse($this->reservation_repository->getById($id), "Reservation retrieved successfully.");
        } catch (\Exception $exception) {
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Update the specified reservation.
     * 
     * @Request({
     *     summary: Update a reservation,
     *     description: Update a reservation,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Reservation updated successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param Request $request Http request.
     * @param int $id Reservation id.
     * @return JsonResponse response success (reservation updated) or error
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Start a transaction
            DB::beginTransaction();

            // Validate the request
            $validated = $request->validate([
                'number_of_tickets' => 'required|integer|min:1',
            ]);

            // Find the reservation and event
            $reservation = $this->reservation_repository->getById($id);
            $event = $this->event_repository->getById($reservation->event_id);

            $newNumberOfTickets = $validated['number_of_tickets'];
            $difference = $newNumberOfTickets - $reservation->number_of_tickets;

            // Check if the event has enough tickets available
            if ($event->availability < $difference) {
                return $this->sendResponse([], 'Not enough tickets available', 400);
            }

            // Update the event available tickets
            $event->availability -= $difference;
            $event->save();

            // Update the reservation
            $this->reservation_repository->update($id, $request->all());

            // Commit the transaction
            DB::commit();

            // Return a success message
            return $this->sendResponse([], 'Reservation updated successfully');
        } catch (\Exception $exception) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Remove the specified reservation.
     * 
     * @Request({
     *     summary: Delete a reservation,
     *     description: Delete a reservation,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Reservation deleted successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param int $id The reservation ID.
     * @return Response The response.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Start a transaction
            DB::beginTransaction();
            // Find the reservation and event
            $reservation = $this->reservation_repository->getById($id);
            $event = $this->event_repository->getById($reservation->event_id);

            // Update the event available tickets
            $event->availability += $reservation->number_of_tickets;
            $event->save();

            // Delete the reservation
            $this->reservation_repository->delete($id);

            // Commit the transaction
            DB::commit();

            // Return a success message
            return $this->sendResponse([], 'Reservation deleted successfully');
        } catch (\Exception $exception) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return $this->sendResponse([], $exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Validate the reservation.
     * @Request({
     *     summary: Validate a reservation,
     *     description: Validate a reservation,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Reservation validated successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param array $ticket The reservation data.
     * @return Event The event object.
     * @throws Exception If the reservation is invalid.
     */
    private function validateReservation(array $ticket): Event
    {
        
        // Check if the event is available
        $event = $this->event_repository->getById($ticket['event_id']);

        // Check if the event has ended or is in the future
        if ($event->date < now()) {
            throw new Exception("Event has ended for event '{$event->name}'", 400);
        } elseif (strtotime($event->date) > strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + 5 years")))) {
            throw new Exception("Tickets available at a later date for event '{$event->name}'", 400);
        }

        // Check if the event has tickets available
        if ($event->availability < $ticket['number_of_tickets']) {
            throw new Exception("Not enough tickets available for event '{$event->name}'", 400);
        } elseif ($ticket['number_of_tickets'] <= 0) {
            throw new Exception("Please enter a valid number of tickets", 400);
        }

        // Update the event available tickets
        $event->availability -= $ticket['number_of_tickets'];
        $this->event_repository->update($event->id, $event);

        return $event;
    }

    /**
     * Validate the body keys.
     * @Request({
     *     summary: Validate the body keys,
     *     description: Validate the body keys,
     *     tags: Reservations
     * })
     * @Response({
     *     code: 200,
     *     description: Body keys validated successfully,
     *     ref: Reservation
     * })
     * @Response({
     *     code: 500,
     *     description: Internal server error
     * })
     * @param array $data The data.
     * @return void
     */
    private function validateBodyKeys(array $data): void
    {
        $valid_keys = ['event_id', 'user_id', 'number_of_tickets'];
        $body_keys = array_filter($data, function ($key) use ($valid_keys) {
            return in_array($key, $valid_keys);
        }, ARRAY_FILTER_USE_KEY);
        
        $missing_keys = array_diff(array_values($valid_keys), array_keys($body_keys));

        if (count($body_keys) !== count($valid_keys)) {
            throw new Exception("Missing required fields: " . implode(", ", $missing_keys), 400);
        }
    }
}

