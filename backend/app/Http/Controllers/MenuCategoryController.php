<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->get('lang', 'hy');

        $cats = MenuCategory::query()
            ->withCount('menus')
            ->orderBy('name')
            ->get();

        $mapped = $cats->map(function (MenuCategory $c) use ($lang) {
            $name = $c->name;
            if ($lang === 'hy' && $c->name_hy) $name = $c->name_hy;
            elseif ($lang === 'en' && $c->name_en) $name = $c->name_en;
            elseif ($lang === 'ru' && $c->name_ru) $name = $c->name_ru;

            return [
                'id' => $c->id,
                'name' => $name,
                'image_url' => $c->image_url,
                'menus_count' => $c->menus_count,
            ];
        });

        return response()->json($mapped);
    }
}
