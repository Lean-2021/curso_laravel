<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IdeaController extends Controller
{

    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $ideas = Idea::myIdeas($request->filtro)->theBest($request->filtro)->get();

        return view(
            'ideas.index',
            [
                'ideas' => $ideas
            ]
        );
    }

    public function create(): View
    {
        return view('ideas.create_or_edit');
    }

    public function edit(Idea $idea): View
    {
        $this->authorize('update', $idea);

        return view('ideas.create_or_edit', [
            'idea' => $idea
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'max:100'
                ],
                'description' => [
                    'required',
                    'string',
                    'max:300',
                ]
            ],
            [
                'title.required' => 'EL título es requerido'
            ]
        );


        try {
            Idea::create([
                'user_id' => Auth::user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            return redirect()->route('idea.index')
                ->with(
                    [
                        'message' => 'La idea se creó correctamente',
                        'color' => 'green-800'
                    ]
                );
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->route('idea.index')
                ->with(
                    [
                        'message' => 'No se pudo crear la idea',
                        'color' => 'red-800'
                    ]
                );
        }
    }

    public function update(Request $request, Idea $idea): RedirectResponse
    {
        $this->authorize('update', $idea);

        $validated = $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'max:100'
                ],
                'description' => [
                    'required',
                    'string',
                    'max:300',
                ]
            ],
            [
                'title.required' => 'EL título es requerido'
            ]
        );

        try {
            $idea->update($validated);
            return redirect()->route('idea.index')
                ->with(
                    [
                        'message' => 'La idea se actualizó correctamente',
                        'color' => 'green-800'
                    ]
                );
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->route('idea.index')
                ->with(
                    [
                        'message' => 'No se pudo actualizar la idea',
                        'color' => 'red-800'
                    ]
                );
        }
    }

    // Mostrar detalle de la idea
    public function show(Idea $idea): View
    {
        return view('ideas.show')->with('idea', $idea);
    }

    // Eliminar una idea
    public function destroy(Idea $idea): RedirectResponse
    {
        $this->authorize('delete', $idea);

        try {
            $idea->delete();
            return redirect(route('idea.index'))
                ->with(
                    [
                        'message' => 'La idea se eliminó correctamente',
                        'color' => 'red-800'
                    ]
                );
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    // Sincronizar likes

    public function syncLikes(Request $request, Idea $idea): RedirectResponse
    {
        // dd($request->user());

        $this->authorize('updateLikes', $idea);

        $request->user()->ideasLiked()->toggle([$idea->id]);


        $idea->update(['likes' => $idea->users()->count()]);
        return redirect()->back();
    }
}
