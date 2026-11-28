<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveTypeRequest;
use App\Models\LeaveType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->formView(new LeaveType);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeaveTypeRequest $request): RedirectResponse
    {
        try {
            LeaveType::create($request->validated());

            return redirect()
                ->route('leave-types.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType) : View
    {
        return $this->formView($leaveType);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeaveTypeRequest $request, LeaveType $leaveType): RedirectResponse
    {
        try {
            $leaveType->update($request->validated());

            return redirect()
                ->route('leave-types.index')
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        try {
            $leaveType->delete();

            return redirect()
                ->route('leave-types.index')
                ->with('success', __('common.messages.deleted'));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', __('common.messages.delete_failed'));
        }
    }

    private function formView(LeaveType $leaveType): View
    {
        return view('leave-types.index', [
            'leaveTypes' => LeaveType::all(),
            'leaveType' => $leaveType,
        ]);
    }
}
