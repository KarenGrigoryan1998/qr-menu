<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return Table::orderBy('number')->get();
    }

    public function show(Table $table)
    {
        return $table;
    }
}
