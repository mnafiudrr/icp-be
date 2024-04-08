<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $model = new Ticket();
        $tickets = $model->search($request->all());

        $data = $tickets->map(function ($ticket) {
            return $this->ticketResource($ticket);
        });

        return response()->json([
            'status' => 'success',
            'count' => count($data),
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required|in:bug,task',
            'description' => 'required',
            'label' => 'required|in:To Do,Doing',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::create(
                $request->only(['title', 'type', 'description', 'label', 'project_id'])
            );

            if ($request->has('assigned_to')) {
                $ticket->assignedTo()->attach($request->get('assigned_to'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->ticketResource($ticket)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket = Ticket::where('id', $id)->first();

        if (!$ticket)
            return response()->json(['message' => 'Ticket not found'], 404);

        return response()->json([
            'status' => 'success',
            'data' => $this->ticketResource($ticket)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required|in:bug,task',
            'description' => 'required',
            'label' => 'required|in:To Do,Doing',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'exists:users,id',
        ]);

        $ticket = Ticket::where('id', $id)->first();

        if (!$ticket)
            return response()->json(['message' => 'Ticket not found'], 404);

        DB::beginTransaction();
        try {
            $ticket->update(
                $request->only(['title', 'type', 'description', 'label', 'project_id'])
            );

            if ($request->has('assigned_to')) {
                $ticket->assignedTo()->sync($request->get('assigned_to'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->ticketResource($ticket)
        ]);
    }

    public function updateField(Request $request, string $id)
    {
        $request->validate([
            'field' => 'required',
            'value' => 'required',
        ]);

        $ticket = new Ticket();

        $fillable = $ticket->getFillable();
        $key = array_search('created_by', $fillable);
        if ($key !== false)
            unset($fillable[$key]);

        if (!in_array($request->get('field'), $fillable)) {
            return response()->json(['message' => 'Field not found'], 404);
        }

        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket)
            return response()->json(['message' => 'Ticket not found'], 404);

        DB::beginTransaction();
        try {
            $ticket->update([$request->get('field') => $request->get('value')]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => 'Value is not valid'], 400);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->ticketResource($ticket)
        ]);
    }

    public function assignUser(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket)
            return response()->json(['message' => 'Ticket not found'], 404);

        if ($ticket->isAssigned($request->get('user_id')))
            return response()->json(['message' => 'User already assigned'], 400);

        DB::beginTransaction();
        try {
            $ticket->assignedTo()->attach($request->get('user_id'));
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
        return response()->json([
            'status' => 'success',
            'data' => $this->ticketResource($ticket)
        ]);
    }

    public function unassignUser(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket)
            return response()->json(['message' => 'Ticket not found'], 404);

        if (!$ticket->isAssigned($request->get('user_id')))
            return response()->json(['message' => 'User not assigned'], 400);

        DB::beginTransaction();
        try {
            $ticket->assignedTo()->detach($request->get('user_id'));
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
        return response()->json([
            'status' => 'success',
            'data' => $this->ticketResource($ticket)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::where('id', $id)->first();

        if (!$ticket)
            return response()->json(['message' => 'Ticket not found'], 404);

        $ticket->delete();
        return response()->json(['status' => 'success'], 200);
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
