<?php

namespace App\Repositories;

use App\Models\Event;

/**
 * Event repository class
 */
class EventRepository
{
    /**
     * Get event by id
     *
     * @param int $id Event id
     * @return Event instance
     */
    public function getById($id)
    {
        return Event::findOrFail($id);
    }

    /**
     * Get all events
     *
     * @return Collection of Event instances
     */
    public function getAll()
    {
        return Event::all();
    }

    /**
     * Create a new event
     *
     * @param array $data data to create an event
     * @return Event instance
     */
    public function create($data)
    {
        return Event::create($data);
    }

    /**
     * Update event instance
     *
     * @param int $id Event id
     * @param array $data data to update an event
     * @return Event
     */
    public function update($id, $data): Event
    {
        return Event::findOrFail($id)->update($data);
    }

    /**
     * Delete an event
     *
     * @param int $id Event id
     */
    public function delete($id)
    {
        Event::findOrFail($id)->delete();
    }
}
