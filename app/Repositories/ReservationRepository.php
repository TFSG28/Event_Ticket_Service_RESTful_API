<?php

namespace App\Repositories;

use App\Models\Reservation;

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
    public function getById($id)
    {
        return Reservation::findOrFail($id);
    }

    /**
     * Get all reservations
     *
     * @return Collection of Reservation instances
     */
    public function getAll()
    {
        return Reservation::all();
    }

    /**
     * Create a new reservation
     *
     * @param array $data data to create a reservation
     * @return Reservation instance
     */
    public function create($data)
    {
        return Reservation::create($data);
    }

    /**
     * Update reservation instance
     *
     * @param int $id Reservation id
     * @param array $data data to update a reservation
     * @return Reservation instance
     */
    public function update($id, $data): Reservation
    {
        return Reservation::findOrFail($id)->update($data);
    }

    /**
     * Delete a reservation
     *
     * @param int $id Reservation id
     */
    public function delete($id)
    {
        Reservation::findOrFail($id)->delete();
    }

    /**
     * Get all reservations by query parameters
     *
     * @param array $query_params query parameters
     * @return Collection of Reservation instances
     */
    public function getByQueryParams($query_params)
    {
        return Reservation::where($query_params)->get();
    }
}
