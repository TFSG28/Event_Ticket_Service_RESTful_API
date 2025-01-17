<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Event;
use App\Repositories\ReservationRepository;
use Exception;

class ReservationController extends Controller
{
    protected $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Display a listing of the reservations.
     */
    public function index(Request $request)
    {
        try {
            $query_params = $request->query();
            $valid_inputs = ['event_id', 'user_id'];
            $query_params = array_filter($query_params, function($key) use ($valid_inputs) {
                return in_array($key, $valid_inputs);
            }, ARRAY_FILTER_USE_KEY);
            $reservations = $this->reservationRepository->getByQueryParams($query_params);
            return response()->json($reservations);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request)
    {
        try {
            $reservations = [];
            $events = [];

            // Iterate if multiple reservations are being made
            if (is_array($request->all())) {
                foreach ($request->all() as $ticket) {
                    $event = $this->validateReservation($ticket);
                    if ($event) {
                        $events[] = $event;
                        $reservations[] = $ticket;
                    }
                }
            }
            else {
                $event = $this->validateReservation($request->all());
                if ($event) {
                    $events[] = $event;
                    $reservations[] = $request->all();
                }
            }

            $this->reservationRepository->create($reservations);

            // Return the reservation and a success message
            return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservations]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Display the specified reservation.
     */
    public function show(string $id)
    {
        try {
            $reservation = $this->reservationRepository->getById($id);
            return response()->json($reservation);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    /**
     * Update the specified reservation.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'number_of_tickets' => 'required|integer|min:1',
            ]);

            // Find the reservation and event
            $reservation = Reservation::find($id);
            $event = Event::find($reservation->event_id);

            $newNumberOfTickets = $validated['number_of_tickets'];
            $difference = $newNumberOfTickets - $reservation->number_of_tickets;

            // Check if the event has enough tickets available
            if ($event->availability < $difference) {
                return response()->json(['message' => 'Not enough tickets available'], 400);
            }

            // Update the event available tickets
            $event->availability -= $difference;
            $event->save();

            // Update the reservation
            $reservation->update($request->all());

        // Return a success message
            return response()->json(['message' => 'Reservation updated successfully', 'reservation' => $reservation]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Remove the specified reservation.
     */
    public function destroy(string $id)
    {  
        try {
            // Find the reservation and event
            $reservation = Reservation::find($id);
            $event = Event::find($reservation->event_id);

            // Update the event available tickets
            $event->availability += $reservation->number_of_tickets;
            $event->save();

            // Delete the reservation
            $this->reservationRepository->delete($id);

            // Return a success message
            return response()->json(['message' => 'Reservation deleted successfully']);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
    }

    private function validateReservation($ticket)
    {
        // Validate the request
        //$validated = $ticket->validate([
        //    'event_id' => 'required|exists:events,id',
        //    'name' => 'required|string',
        //    'user_id' => 'required|exists:users,id',
        //    'number_of_tickets' => 'required|integer|min:1',
        //    ]);

        // Check if the event is available
        $event = Event::find($ticket['event_id']);

        // Check if the event has tickets available
        if ($event->availability < $ticket['number_of_tickets']) {
            throw new Exception("Not enough tickets available for event '{$event->name}'");
        }

        // Check if the event has ended
        if ($event->date < now()) { 
           throw new Exception("Event has ended for event '{$event->name}'");
        }
    
        // Update the event available tickets
        $event->availability -= $ticket['number_of_tickets'];

        return $event;
    }
    
}
