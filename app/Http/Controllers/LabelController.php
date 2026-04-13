<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    public function index()
    {
        return Label::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $name = trim($request->name);

        $label = Label::firstOrCreate([
            'name' => $name,
            'user_id' => Auth::id()
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