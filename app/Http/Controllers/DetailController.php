<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Detail::where('product_type_id', 3)
            ->with('productType', 'category')
            ->paginate(10);
        
        return view('details.list', [
            'items' => $items,
            'pageTitle' => $items->first()?->productType?->name ?? 'Детали',
            'pageSubtitle' => 'Управление деталями и компонентами',
            'addButtonText' => 'Добавить деталь',
            'addButtonUrl' => route('details.create'),
            'emptyMessage' => 'Детали не найдены.'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        $sources = \App\Models\Source::all();
        return view('details.create', ['categories' => $categories, 'sources' => $sources]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'depth' => 'required|numeric|min:0',
            'scu' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'description' => 'nullable|string',
            'material' => 'nullable|string|max:255',
        ]);

        $detail = Detail::create($validated);
        return redirect()->route('details.show', $detail)
            ->with('success', 'Деталь успешно создана');
    }

    /**
     * Display the specified resource.
     */
    public function show(Detail $detail)
    {
        $detail->load('category', 'source');
        return view('details.show', compact('detail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Detail $detail)
    {
        $categories = \App\Models\Category::all();
        $sources = \App\Models\Source::all();
        return view('details.edit', [
            'detail' => $detail,
            'categories' => $categories,
            'sources' => $sources
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Detail $detail)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'depth' => 'required|numeric|min:0',
            'scu' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'description' => 'nullable|string',
            'material' => 'nullable|string|max:255',
        ]);

        $detail->update($validated);
        return redirect()->route('details.show', $detail)
            ->with('success', 'Деталь успешно обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Detail $detail)
    {
        $detail->delete();
        return redirect()->route('details.index')
            ->with('success', 'Деталь успешно удалена');
    }
}
