<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function categories()
    {
        return MenuCategory::query()->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $lang = $request->get('lang', 'hy');
        $q = Menu::query();
        if ($request->has('category_id')) {
            $q->where('category_id', $request->integer('category_id'));
        }
        if ($request->boolean('available_only', true)) {
            $q->where('available', true);
        }
        $items = $q->orderBy('name')->get();

        $mapped = $items->map(function (Menu $m) use ($lang) {
            $name = $m->name;
            $desc = $m->description;
            if ($lang === 'hy') { $name = $m->name_hy ?: $name; $desc = $m->description_hy ?: $desc; }
            elseif ($lang === 'en') { $name = $m->name_en ?: $name; $desc = $m->description_en ?: $desc; }
            elseif ($lang === 'ru') { $name = $m->name_ru ?: $name; $desc = $m->description_ru ?: $desc; }

            return [
                'id' => $m->id,
                'name' => $name,
                'description' => $desc,
                'price' => $m->price,
                'category_id' => $m->category_id,
                'image_url' => $m->image_url,
                'available' => $m->available,
            ];
        });

        return response()->json($mapped);
    }

    public function show(Menu $menu, Request $request)
    {
        $lang = $request->get('lang', 'hy');
        $name = $menu->name;
        $desc = $menu->description;
        if ($lang === 'hy') { $name = $menu->name_hy ?: $name; $desc = $menu->description_hy ?: $desc; }
        elseif ($lang === 'en') { $name = $menu->name_en ?: $name; $desc = $menu->description_en ?: $desc; }
        elseif ($lang === 'ru') { $name = $menu->name_ru ?: $name; $desc = $menu->description_ru ?: $desc; }

        return response()->json([
            'id' => $menu->id,
            'name' => $name,
            'description' => $desc,
            'price' => $menu->price,
            'category_id' => $menu->category_id,
            'image_url' => $menu->image_url,
            'available' => $menu->available,
        ]);
    }
}
