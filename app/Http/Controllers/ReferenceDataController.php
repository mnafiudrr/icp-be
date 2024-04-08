<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class ReferenceDataController extends Controller
{
    public function index(Request $request)
    {
        switch ($request->type) {
            case 'project':
                $data = Project::select('id', 'name')->get();
                break;
            case 'user':
                $data = User::select('id', 'name')->get();
                break;
            case 'ticket-label':
                $data = Ticket::LABELS;
                break;
            case 'ticket-type':
                $data = Ticket::TYPES;
                break;
            default:
                $data = [];
        }

        return response()->json([
            'message' => 'success',
            'type' => $request->type,
            'count' => count($data),
            'data' => $data
        ]);
    }
}
