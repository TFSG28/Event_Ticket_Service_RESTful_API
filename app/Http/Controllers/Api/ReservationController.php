<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
class ReservationController extends Controller
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
            return response()->json($this->reservation_repository->getByQueryParams($query_params));
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Store a newly created reservation.
     * 
     * @param Request $request Http request.
     * @return JsonResponse response success (reservation created) or error
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Start a transaction
            DB::beginTransaction();
            $reservations = [];

            // Iterate over one or multiple reservations
            foreach ($request->all() as $ticket) {
                $this->validateBodyKeys($ticket);
                $this->validateReservation($ticket);
                $reservations[] = $this->reservation_repository->create($ticket);
            }

            // Commit the transaction
            DB::commit();

            // Return the reservation and a success message
            return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservations]);
        } catch (\Exception $exception) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Display the specified reservation.
     * 
     * @param int $id Reservation id.
     * @return JsonResponse response success (reservation) or error
     */
    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->reservation_repository->getById($id));
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Update the specified reservation.
     * 
     * @param Request $request Http request.
     * @param int $id Reservation id.
     * @return JsonResponse response success (reservation updated) or error
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
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
                return response()->json(['Not enough tickets available'], 400);
            }

            // Update the event available tickets
            $event->availability -= $difference;
            $event->save();

            // Update the reservation and return a success message
            return response()->json([$this->reservation_repository->update($id, 
                $request->all()) ? 'Reservation updated successfully' : 'Reservation not found', 'reservation' => $reservation]);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified reservation.
     * 
     * @param int $id The reservation ID.
     * @return Response The response.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Find the reservation and event
            $reservation = $this->reservation_repository->getById($id);
            $event = $this->event_repository->getById($reservation->event_id);

            // Update the event available tickets
            $event->availability += $reservation->number_of_tickets;
            $event->save();

            // Delete the reservation and return a success message
            return response()->json([$this->reservation_repository->delete($id) ? 'Reservation deleted successfully' : 'Reservation not found'], 200);
        } catch (\Exception $exception) {
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Validate the reservation.
     * 
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
     * 
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

