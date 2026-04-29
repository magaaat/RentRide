<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestUpdateRequest;
use App\Models\TestUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class TestUpdateController extends Controller
{
    public function index(): Response
    {
        return response('THIS IS TESTUPDATE INDEX', 200)
            ->header('Content-Type', 'text/plain');
    }

    public function create()
    {
        abort(404);
    }

    public function store(TestUpdateRequest $request): RedirectResponse
    {
        TestUpdate::create($request->validated());

        return redirect()->route('test-updates.index');
    }

    public function show(TestUpdate $testUpdate)
    {
        abort(404);
    }

    public function edit(TestUpdate $testUpdate)
    {
        abort(404);
    }

    public function update(TestUpdateRequest $request, TestUpdate $testUpdate): RedirectResponse
    {
        $testUpdate->update($request->validated());

        return redirect()->route('test-updates.index');
    }

    public function destroy(TestUpdate $testUpdate): RedirectResponse
    {
        $testUpdate->delete();

        return redirect()->route('test-updates.index');
    }
}
