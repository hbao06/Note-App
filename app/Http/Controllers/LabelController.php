<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        return Label::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $label = Label::create([
            'name' => $request->name,
            'user_id' => 1
        ]);

        return response()->json($label);
    }

    public function update(Request $request, Label $label)
    {
        $label->update(['name' => $request->name]);
        return $label;
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return response()->json(['status' => 'deleted']);
    }
}