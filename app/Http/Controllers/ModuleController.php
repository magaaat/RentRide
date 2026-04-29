<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ModuleController extends Controller
{
    public function index(): Response
    {
        return response('THIS IS MODULE INDEX', 200)
            ->header('Content-Type', 'text/plain');
    }

    public function create()
    {
        abort(404);
    }

    public function store(ModuleRequest $request): RedirectResponse
    {
        Module::create($request->validated());

        return redirect()->route('modules.index');
    }

    public function show(Module $module)
    {
        abort(404);
    }

    public function edit(Module $module)
    {
        abort(404);
    }

    public function update(ModuleRequest $request, Module $module): RedirectResponse
    {
        $module->update($request->validated());

        return redirect()->route('modules.index');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();

        return redirect()->route('modules.index');
    }
}
