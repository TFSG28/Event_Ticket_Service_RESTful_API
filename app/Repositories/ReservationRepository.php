<?php

namespace App\Repositories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
/**
 * Reservation repository class
 */
class ReservationRepository
{
    /**
     * Get reservation by id
     *
     * @param int $id Reservation id
     * @return Reservation instance
     */
    public function getById(int $id): Reservation
    {
        return $this->checkReservationExists($id);
    }

    /**
     * Get all reservations
     *
     * @return Collection of Reservation instances
     */
    public function getAll(): Collection
    {
        return Reservation::all();
    }

    /**
     * Create a new reservation
     *
     * @param array $data data to create a reservation
     * @return Reservation instance
     */
    public function create(array $data): Reservation
    {
        return Reservation::create($data);
    }

    /**
     * Update reservation instance
     *
     * @param int $id Reservation id
     * @param array $data data to update a reservation
     * @return bool true if reservation updated, false otherwise
     */
    public function update(int $id, array $data): bool
    {
        return $this->checkReservationExists($id)->update($data);
    }

    /**
     * Delete a reservation
     *
     * @param int $id Reservation id
     * @return bool true if reservation deleted, false otherwise
     */
    public function delete(int $id): bool
    {
        return $this->checkReservationExists($id)->delete();
    }

    /**
     * Get all reservations by query parameters
     *
     * @param array $query_params query parameters
     * @return Collection of Reservation instances
     */
    public function getByQueryParams(array $query_params): Collection
    {
        return Reservation::where($query_params)->get();
    }

    /**
     * Check if reservation exists
     *
     * @param int $id Reservation id
     * @return Reservation instance
     */
    private function checkReservationExists(int $id): Reservation
    {
        $reservation = Reservation::where('id', $id)->first();
        if (!$reservation) {
            throw new ModelNotFoundException("Reservation not found", 404);
        }
        return $reservation;
    }
}
