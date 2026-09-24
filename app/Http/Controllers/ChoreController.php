<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChoreRequest;
use App\Models\Chore;

class ChoreController extends Controller
{
    public function index()
    {
        $chores = Chore::all();

        return view('chores.index', compact('chores'));
    }

    public function create()
    {
        return view('chores.create');
    }

    public function store(ChoreRequest $request)
    {
        $validated = $request->validated();

        Chore::create($validated);

        return redirect()->route('chores.index')->with('success', '新しいお手伝いを登録しました');
    }

    public function update(ChoreRequest $request, $id)
    {
        $chore = Chore::findOrfail($id);

        $validated = $request->validated();

        $chore->update($validated);

        return redirect()->route('chores.index')->with('success', 'お手伝いの内容を更新しました');
    }

    public function destroy($id)
    {
        $chore = Chore::findOrfail($id);

        $chore->delete();

        return redirect()->route('chores.index')->with('success', 'お手伝いを削除しました');
    }
}
