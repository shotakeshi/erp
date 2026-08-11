<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Services\Shared\FormOptionService;
use Illuminate\Http\Request;
use App\Http\Requests\PositionRequest;

class PositionController extends Controller
{
    public function __construct(
        protected FormOptionService $formOptionService
    ){

    }

    public function index(Request $request){
        $positions = Position::query()
            ->with('department')
            ->withCount('employees')
            ->when($request->department_id, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->get();
        return view('positions.index', [
            'positions' => $positions,
            'departments' => $this->formOptionService->departmentOptions()
        ]);
    }

    public function create(){
        return view('positions.create',[
            'departments' => $this->formOptionService->departmentOptions()
        ]);
    }

    public function store(PositionRequest $request){
        try {
            Position::create($request->all());
            return redirect()
                ->route('positions.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    public function edit(Position $position){
        return view('positions.edit',[
            'position' => $position,
            'departments' => $this->formOptionService->departmentOptions()
        ]);
    }

    public function update(PositionRequest $request, Position $position){
        try {
            $position->fill($request->all());
            if (! $position->isDirty()) {
                return back()->with('warning', __('common.messages.not_changed'));
            }
            $position->update($request->all());
            return redirect()
                ->route('positions.index')
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }
    }

    public function destroy(Position $position){
        try {
            $position->delete();
            return redirect()
                ->route('positions.index')
                ->with(
                    'success',
                    __('common.messages.deleted')
                );

        } catch (\Throwable $e) {
            report($e);
            return back()->with(
                'error',
                __('common.messages.delete_failed')
            );
        }
    }
}