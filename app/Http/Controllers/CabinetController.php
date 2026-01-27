<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\ProductType;
use Illuminate\Http\Request;

class CabinetController extends Controller
{
    public function index()
    {
        // Получаем все кабинеты (Готовые изделия - product_type_id = 1)
        $cabinets = Detail::where('product_type_id', 1)
            ->with('productType')
            ->get();
        return view('cabinets.index', compact('cabinets'));
    }

    public function create()
    {
        return view('cabinets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'scu' => 'required|unique:details,scu',
            'name' => 'required|string|max:255',
        ]);

        $validated['product_type_id'] = 1; // Готовое изделие
        
        Detail::create($validated);
        return redirect('/cabinets')->with('success', 'Кабинет создан успешно');
    }

    public function show($id)
    {
        $cabinet = Detail::where('product_type_id', 1)->findOrFail($id);
        $cabinet->load('componentsInConfiguration', 'productType');
        return view('cabinets.show', compact('cabinet'));
    }

    public function edit($id)
    {
        $cabinet = Detail::where('product_type_id', 1)->findOrFail($id);
        return view('cabinets.edit', compact('cabinet'));
    }

    public function update(Request $request, $id)
    {
        $cabinet = Detail::where('product_type_id', 1)->findOrFail($id);
        
        $validated = $request->validate([
            'scu' => 'required|unique:details,scu,' . $cabinet->id,
            'name' => 'required|string|max:255',
        ]);

        $cabinet->update($validated);
        return redirect('/cabinets')->with('success', 'Кабинет обновлён');
    }

    public function destroy($id)
    {
        $cabinet = Detail::where('product_type_id', 1)->findOrFail($id);
        $cabinet->delete();
        return redirect('/cabinets')->with('success', 'Кабинет удалён');
    }
}

