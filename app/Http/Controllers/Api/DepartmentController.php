<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Department::class);

        $departments = Department::all();
        return DepartmentResource::collection($departments)->additional([
            'message' => 'Departments successfully retrieved',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $departamento
     * @return \Illuminate\Http\Response
     */
    public function show($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        $this->authorize('view', $department);

        return (new DepartmentResource($department))->additional([
            'message' => 'Department successfully retrieved',
            'success' => true,
        ]);
    }
}
