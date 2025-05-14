<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjeckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Project::all();
        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'Project found',
                'project' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan project baru
        $project = Project::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Project created successfully',
            'project' => $project
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $data = Project::find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'Project found',
                'project' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update project
        $project = Project::find($id);
        if ($project) {
            $project->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Project updated successfully',
                'project' => $project
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Project deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ]);
        }
    }
}
