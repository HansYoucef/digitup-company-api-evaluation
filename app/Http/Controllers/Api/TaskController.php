<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::with('user')->get();
        } else {
            $tasks = Task::where('user_id', $user->id)->get();
        }

        $response = Gate::inspect('viewAny', Task::class);

        if(!empty($tasks)) {
            if($response->allowed()) {
                return response()->json($tasks, status: 200);
            } else {
                return response()->json(["error" => "Accès refusé"], 403);
            }
        } else {
            return response()->json(["message" => "Aucune Tâche a afficher"], status: 200);
        }

    }

    public function deleted()
    {
        $response = Gate::inspect('viewDeleted', Task::class);

        if($response->allowed()) {
            $tasks = Task::onlyTrashed()->with('user')->get();
            return response()->json($tasks, status: 200);
        } else {
            return response()->json(["error" => "Accès refusé"], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = Gate::inspect('create', Task::class);

        if($response->allowed()) {
            $rules = [
                'titre'         => 'required',
                'description'   => 'required',
                'statut'        => 'required',
                'date_echeance' => 'required|date',
            ];

            if($request->has('user_id')) {
                $rules['user_id'] = 'required';
                $user_id = $request->user_id;
            } else {
                $user_id = Auth::user()->id;
            }

            $request->validate($rules);

            $task = Task::create([
                'user_id'       => $user_id,
                'titre'         => $request->titre,
                'description'   => $request->description,
                'statut'        => $request->statut,
                'date_echeance' => $request->date_echeance,
            ]);

            return response()->json(['message' => 'Tâche créée', 'task' => $task], status: 201);
        } else {
            return response()->json(["error" => "Accès refusé"], 403);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::find($id);

        if(!empty($task)) {
            $response = Gate::inspect('view', $task);

            if($response->allowed()) {
                return response()->json($task, status: 200);
            }else {
                return response()->json(["error" => "Accès refusé"], 403);
            }
        } else {
            return response()->json(["error" => "Tâche introuvable ou supprimée"], status: 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);
        
        if(!empty($task)) {
            $response = Gate::inspect('update', $task);
            
            if($response->allowed()) {
                $rules = [
                    'titre'         => 'required',
                    'description'   => 'required',
                    'statut'        => 'required',
                    'date_echeance' => 'required|date',
                ];
                
                if($request->has('user_id')) {
                    $rules['user_id'] = 'required';
                    $user_id = $request->user_id;
                } else {
                    $user_id = Auth::user()->id;
                }
                $request->validate($rules);

                $task->update([
                    'user_id'       => $user_id,
                    'titre'         => $request->titre,
                    'description'   => $request->description,
                    'statut'        => $request->statut,
                    'date_echeance' => $request->date_echeance,
                ]);
                return response()->json(["message" => "Tâche modifiée", "task" => $task], status: 200);
            } else {
                return response()->json(["error" => "Accès refusé"], 403);
            }
        } else {
            return response()->json(["error" => "Tâche introuvable ou supprimée"], status: 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        if(!empty($task)) {
            $response = Gate::inspect('update', $task);
    
            if($response->allowed()) {
                $task->delete();
                return response()->json(["message" => "Tâche supprimée"], status: 200);
            } else {
                return response()->json(["error" => "Accès refusé"], 403);
            }
        } else {
            return response()->json(["error" => "Tâche introuvable ou déja supprimée"], status: 404);
        }
    }
}
