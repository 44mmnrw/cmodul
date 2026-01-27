<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::all();
        return view('places.index', compact('places'));
    }

    public function create()
    {
        return view('places.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'place_id' => 'required|unique:places,place_id',
            'name' => 'required|string',
        ]);

        Place::create($validated);
        return redirect('/places')->with('success', 'Место создано успешно');
    }

    public function show(Place $place)
    {
        return view('places.show', compact('place'));
    }

    public function edit(Place $place)
    {
        return view('places.edit', compact('place'));
    }

    public function update(Request $request, Place $place)
    {
        $validated = $request->validate([
            'place_id' => 'required|unique:places,place_id,' . $place->id,
            'name' => 'required|string',
        ]);

        $place->update($validated);
        return redirect('/places')->with('success', 'Место обновлено');
    }

    public function destroy(Place $place)
    {
        $place->delete();
        return redirect('/places')->with('success', 'Место удалено');
    }
}

