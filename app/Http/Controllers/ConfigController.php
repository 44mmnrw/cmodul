<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Показать форму создания конфигурации или компонента
     */
    public function create()
    {
        $type = request('type', 1); // Тип по умолчанию 1 (конфигурации)
        
        // Получим ВСЕ детали (для фильтрации по product_type_id на frontend)
        // Типы компонентов определяются так:
        // - Если выбран product_type_id=1, показываем компоненты с product_type_id=2
        // - Если выбран product_type_id=2, показываем компоненты с product_type_id=3
        $availableComponents = Detail::whereIn('product_type_id', [2, 3])
            ->orderBy('product_type_id')
            ->orderBy('name')
            ->get();
        
        // Получим все типы продуктов
        $productTypes = \App\Models\ProductType::all();
        
        return view('configurations.create', [
            'availableComponents' => $availableComponents,
            'productType' => $type,
            'productTypes' => $productTypes,
        ]);
    }

    /**
     * Сохранить новую конфигурацию или компонент
     */
    public function store(Request $request)
    {
        // Приоритет: берём product_type_id из формы, если нет - берём type параметр
        $productTypeId = (int)$request->input('product_type_id', $request->input('type', 1));
        
        if ($productTypeId == 2) {
            // Создание компонента (product_type_id = 2)
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'components' => 'nullable|array',
                'components.*.id' => 'integer|exists:products,id',
                'components.*.quantity' => 'integer|min:1',
            ]);

            $component = Detail::create([
                'name' => $request->name,
                'description' => $request->description,
                'scu' => $this->generateSCU(),
                'product_type_id' => 2,
            ]);

            // Добавляем sub-компоненты в таблицу configs
            // master_id = id компонента, slave_id = id sub-компонента
            $components = $request->input('components', []);
            if (!empty($components) && is_array($components)) {
                foreach ($components as $comp) {
                    if (isset($comp['id']) && isset($comp['quantity'])) {
                        Config::create([
                            'master_id' => $component->id,
                            'slave_id' => $comp['id'],
                            'quantity' => $comp['quantity'],
                        ]);
                    }
                }
            }

            return redirect()->route('configurations.index', ['type' => 2])
                ->with('success', 'Компонент успешно создан');
        } else {
            // Создание конфигурации (product_type_id = 1)
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'components' => 'nullable|array',
                'components.*.id' => 'integer|exists:products,id',
                'components.*.quantity' => 'integer|min:1',
            ]);

            // 1. Создаём новую конфигурацию в таблице products (product_type_id = 1)
            $cabinet = Detail::create([
                'name' => $request->name,
                'description' => $request->description,
                'scu' => $this->generateSCU(),
                'product_type_id' => 1,
            ]);

            // 2. Добавляем компоненты в таблицу configs
            // master_id = id конфигурации, slave_id = id компонента
            $components = $request->input('components', []);
            if (!empty($components) && is_array($components)) {
                foreach ($components as $component) {
                    if (isset($component['id']) && isset($component['quantity'])) {
                        Config::create([
                            'master_id' => $cabinet->id,
                            'slave_id' => $component['id'],
                            'quantity' => $component['quantity'],
                        ]);
                    }
                }
            }

            return redirect()->route('configurations.edit', $cabinet->id)
                ->with('success', 'Конфигурация успешно создана');
        }
    }

    /**
     * Генерировать уникальный SCU для конфигурации
     */
    private function generateSCU()
    {
        $timestamp = time();
        return 'CFG-' . $timestamp;
    }

    /**
     * Показать форму редактирования конфигурации или комплектующего
     */
    public function edit($id)
    {
        // Найти деталь по ID (может быть тип 1 или 2)
        $detail = Detail::findOrFail($id);
        
        // Получим ВСЕ компоненты (для фильтрации по product_type_id на frontend)
        $availableComponents = Detail::whereIn('product_type_id', [2, 3])
            ->orderBy('product_type_id')
            ->orderBy('name')
            ->get();
        
        // Получим все типы продуктов
        $productTypes = \App\Models\ProductType::all();
        
        // Получим текущие компоненты в конфигурации/компоненте с их количествами
        // Работает одинаково для Type 1 и Type 2
        $currentComponents = $detail->componentsInConfiguration()
            ->withPivot('quantity')
            ->get();
        
        return view('configurations.edit', [
            'cabinet' => $detail,
            'availableComponents' => $availableComponents,
            'currentComponents' => $currentComponents,
            'productTypes' => $productTypes,
        ]);
    }

    /**
     * Обновить конфигурацию или комплектующее
     */
    public function update(Request $request, $id)
    {
        $detail = Detail::findOrFail($id);
        
        // Если это конфигурация (тип 1)
        if ($detail->product_type_id == 1) {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'components' => 'array',
                'components.*.id' => 'integer|exists:products,id',
                'components.*.quantity' => 'integer|min:1',
            ]);

            // Обновляем основную информацию
            $detail->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Обновляем компоненты конфигурации
            $componentData = [];
            if ($request->components) {
                foreach ($request->components as $component) {
                    if (isset($component['id']) && isset($component['quantity'])) {
                        $componentData[$component['id']] = ['quantity' => $component['quantity']];
                    }
                }
            }

            // Синхронизируем связи
            $detail->componentsInConfiguration()->sync($componentData);
        } else {
            // Если это комплектующее (тип 2), обновляем основные поля И компоненты
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'components' => 'nullable|array',
                'components.*.id' => 'integer|exists:products,id',
                'components.*.quantity' => 'integer|min:1',
            ]);

            $detail->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            // Сохраняем компоненты Type 2
            $componentData = [];
            if ($request->components) {
                foreach ($request->components as $component) {
                    if (isset($component['id']) && isset($component['quantity'])) {
                        $componentData[$component['id']] = ['quantity' => $component['quantity']];
                    }
                }
            }

            // Синхронизируем связи
            $detail->componentsInConfiguration()->sync($componentData);
        }

        // Определяем куда перенаправить
        $type = $detail->product_type_id;
        if ($type == 1) {
            return redirect()->route('configuration-detail', $id)
                ->with('success', 'Конфигурация успешно обновлена');
        } else {
            return redirect()->route('configurations.index', ['type' => 2])
                ->with('success', 'Комплектующее успешно обновлено');
        }
    }

    /**
     * Удалить конфигурацию
     */
    public function destroy($id)
    {
        $cabinet = Detail::where('product_type_id', 1)->findOrFail($id);
        
        // Удаляем все связанные компоненты из таблицы configs
        Config::where('master_id', $cabinet->id)->delete();
        
        // Удаляем саму конфигурацию из products
        $cabinet->delete();

        return redirect()->route('configurations.index')
            ->with('success', 'Конфигурация успешно удалена');
    }
}
