<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketSortController extends Controller
{
    public function list()
    {
        $data = $this->getAll();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function updateSort(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array',
            'label' => 'required|in:To Do,Doing',
        ]);
        
        $this->configurePriority(array_reverse($request->tickets), $request->label);

        $data = $this->getAll();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket successfully sorted',
            'data' => $data,
        ]);
    }

    private function getAll()
    {
        $tickets = Ticket::orderBy('priority', 'desc')->get();
        $data = [];
        foreach (Ticket::LABELS as $index => $label) {
            $labelTickets = $tickets->filter(function ($ticket) use ($label) {
                return $ticket->label === $label;
            });
            $data[$index]['label'] = $label;
            $data[$index]['tickets'] = $labelTickets->map(function ($ticket) {
                return $this->ticketResource($ticket);
            });
        }

        return $data;
    }

    private function configurePriority($ids, $label)
    {
        $tickets = Ticket::orderBy('priority', 'asc')->get();

        $otherPriority = 0;
        $otherLabel = $label === 'To Do' ? 'Doing' : 'To Do';

        foreach ($tickets as $ticket) {
            if (!in_array($ticket->id, $ids)) {
                $ticket->update([
                    'priority' => $otherPriority,
                    'label' => $otherLabel
                ]);
                $otherPriority += 1;
            } else {
                $priority = array_search($ticket->id, $ids);
                $ticket->update([
                    'priority' => $priority,
                    'label' => $label
                ]);
            }
        }
    }

    private function ticketResource($ticket)
    {
        return array_merge($ticket->toArray(), [
            'created_by' => $ticket->createdBy->only(['id', 'name']),
            'assigned_to' => $ticket->assignedTo->map(function ($user) {
                return $user->only(['id', 'name']);
            }),
            'project' => $ticket->project->only(['id', 'name']),
        ]);
    }
}
