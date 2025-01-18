<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
/**
 * Event repository class
 */
class EventRepository
{
    /**
     * Get event by id
     *
     * @param int $id Event id
     * @return Event|null instance
     */
    public function getById(int $id): Event|null
    {
        return $this->checkIfEventExists($id);
    }

    /**
     * Get event by conditions
     *
     * @param array $conditions Conditions to get an event
     * @return Event|null instance
     */
    public function get(array $conditions): Event|null
    {
        return Event::where($conditions)->first();
    }

    /**
     * Get all events
     *
     * @return Collection of Event instances
     */
    public function getAll(): Collection
    {
        return Event::all();
    }

    /**
     * Create a new event
     *
     * @param array $data data to create an event
     * @return Event instance
     */
    public function create(array $data): Event
    {
        return Event::create($data);
    }

    /**
     * Update event instance
     *
     * @param int $id Event id
     * @param array|Event $data data to update an event
     * @return bool true if event updated, false otherwise
     */
    public function update(int $id, array|Event $data): bool
    {
        if ($data instanceof Event) {
            return $data->save();
        }

        return $this->checkIfEventExists($id)->update($data);
    }

    /**
     * Delete an event
     *
     * @param int $id Event id
     * @return bool true if event deleted, false otherwise
     */
    public function delete(int $id): bool
    {
        return $this->checkIfEventExists($id)->delete();
    }

    /**
     * Check if event exists
     *
     * @param int $id Event id
     * @return Event instance
     */
    private function checkIfEventExists(int $id): Event
    {
        $event = Event::where('id', $id)->first();
        if (!$event) {
            throw new ModelNotFoundException("Event not found", 404);
        }
        return $event;
    }
}
