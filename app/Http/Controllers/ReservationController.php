<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Event;
use App\Repositories\ReservationRepository;

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
    public function index()
    {
        $reservations = $this->reservationRepository->getAll();
        return response()->json($reservations);
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'number_of_tickets' => 'required|integer|min:1',
        ]);

        // Check if the event is available
        $event = Event::find($validated['event_id']);

        // Check if the event has tickets available
        if ($event->availability < $validated['number_of_tickets']) {
            return response()->json(['message' => 'Not enough tickets available'], 400);
        }

        // Check if the event has ended
        if ($event->date < now()) {
            return response()->json(['message' => 'Event has ended'], 400);
        }

        // Update the event available tickets
        $event->availability -= $validated['number_of_tickets'];
        $event->save();

        // Create the reservation
        $reservation = $this->reservationRepository->create($request->all());

        // Return the reservation and a success message
        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation]);
    }

    /**
     * Display the specified reservation.
     */
    public function show(string $id)
    {
        $reservation = $this->reservationRepository->getById($id);
        return response()->json($reservation);
    }

    /**
     * Update the specified reservation.
     */
    public function update(Request $request, string $id)
    {
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
        return response()->json(['message' => 'Reservation updated successfully']);
    }

    /**
     * Remove the specified reservation.
     */
    public function destroy(string $id)
    {
        // Delete the reservation
        $this->reservationRepository->delete($id);

        // Find the reservation and event
        $reservation = Reservation::find($id);
        $event = Event::find($reservation->event_id);

        // Update the event available tickets
        $event->availability += $reservation->number_of_tickets;
        $event->save();

        // Return a success message
        return response()->json(['message' => 'Reservation deleted successfully']);
    }
}
