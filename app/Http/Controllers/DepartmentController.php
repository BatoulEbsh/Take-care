<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{

    use Batoul;

    public function show($id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            return $this->sendError(404, 'notfound');
        }
        return $this->sendResponse('department', $department);
    }

    public function showAll()
    {
        $department = Department::query()->select('*')->get();
        if (is_null($department)) {
            return $this->sendError(404, 'notfound');
        }
        return $this->sendResponse('department', $department);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time'
        ]);
        if ($validator->fails()) {
            return $this->sendError(401, $validator->errors());
        }
        $department = new Department();
        $department->fill([
            'name' => $input['name'],
            'open_time' => $input['open_time'],
            'close_time' => $input['close_time'],
        ]);
        $department->save();
        return $this->sendResponse('success', 'department added successfully');
    }

    public function update(Request $request, int $id)
    {
        $department = Department::find($id);
        if (is_null($department)) {
            return $this->sendError(404, 'notfound');
        }
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'desc' => 'required|string',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time'
        ]);
        if ($validator->fails()) {
            return $this->sendError(401, $validator->errors());
        }
        $department['name'] = $input['name'];
        $department['desc'] = $input['desc'];
        $department['open_time'] = $input['open_time'];
        $department['close_time'] = $input['close_time'];
        $department->save();
        return $this->sendResponse('success', 'department updated successfully');
    }
}
