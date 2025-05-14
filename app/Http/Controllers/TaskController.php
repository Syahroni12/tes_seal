<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Task::with(['project', 'user'])->get();
        if ($data->count() > 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task found',
                'task' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Tidak Tersedia'
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
            'title' => 'required',
            'description' => 'required',
            'status' => 'required|in:on_going,in_progress,completed',
            'dari_tgl' => 'required|date',
            'sampai_tgl' => 'required|date|after_or_equal:dari_tgl',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan task baru
        $tanggal_awal = date('Y-m-d', strtotime($request->dari_tgl));
        $tanggal_akhir = date('Y-m-d', strtotime($request->sampai_tgl));
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'dari_tgl' => $tanggal_awal,
            'sampai_tgl' => $tanggal_akhir,
            'project_id' => $request->project_id,
            'user_id' => $request->user_id
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'task' => $task
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Task::with(['project', 'user'])->find($id);
        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task found',
                'task' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
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
            'title' => 'required',
            'description' => 'required',
            'status' => 'required|in:on_going,in_progress,completed',
            'dari_tgl' => 'required|date',
            'sampai_tgl' => 'required|date|after_or_equal:dari_tgl',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update task
        $task = Task::find($id);
        if ($task) {

            $tanggal_awal = date('Y-m-d', strtotime($request->dari_tgl));
            $tanggal_akhir = date('Y-m-d', strtotime($request->sampai_tgl));
            $task->title = $request->title;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->dari_tgl = $tanggal_awal;
            $task->sampai_tgl = $tanggal_akhir;
            $task->project_id = $request->project_id;
            $task->user_id = $request->user_id;

            $task->save();
            // $task->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'task' => $task
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }
    }

    public function task_saya($id_user)
    {
        $data = Task::with(['project', 'user'])->where('user_id', $id_user)->get();
        if ($data->count() > 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task found',
                'task' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Tidak Tersedia'
            ]);
        }
    }
    public function task_saya_by_id($id_user, $id_task)
    {
        $data = Task::with(['project', 'user'])->where('user_id', $id_user)->where('id', $id_task)->first();
        if ($data) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task found',
                'task' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Tidak Tersedia'
            ]);
        }
    }

    public function ubah_tasksaya(Request $request, $id_task)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:on_going,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = auth()->user();

        $task = Task::where('id', $id_task)->where('user_id', $user->id)->first();

        if ($task) {
            $task->status = $request->status;
            $task->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Task status updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Task bukan milik anda'

            ], 404);
        }
    }
}
