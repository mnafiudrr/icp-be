<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('name', 'asc')->get();

        if ($projects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No projects found',
            ], 404);
        }

        $data = $projects->map(function ($project) {
            return $this->projectResource($project);
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $project = Project::create($request->all());

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create project',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->projectResource($project),
        ], 201);
    }

    public function show(string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->projectResource($project),
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $project->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $this->projectResource($project),
        ], 200);
    }

    public function destroy(string $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found',
            ], 404);
        }

        $project->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Project deleted successfully',
        ], 200);
    }

    private function projectResource($project)
    {
        return array_merge($project->toArray(), [
            'created_by' => $project->createdBy->only(['id', 'name']),
        ]);
    }
}
