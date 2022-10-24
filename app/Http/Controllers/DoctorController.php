<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\User;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\Fluent\Concerns\Has;

class DoctorController extends Controller
{
    use Batoul;

    public function show($id)
    {
        $doctor = Doctor::find($id);
        if (is_null($doctor)) {
            return $this->sendError(404, 'notfound');
        }
        return $this->sendResponse('doctors', $doctor);
    }

    public function showAll()
    {
        $doctor = Doctor::query()
            ->join('users as u', 'u.id', '=', 'user_id')
            ->select('*')
            ->get();
        if (is_null($doctor)) {
            return $this->sendError(404, 'notfound');
        }

        return $this->sendResponse('success', $doctor);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'speciality' => 'required',
            'desc' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'img' => 'image|max:2400',
        ]);
        if ($validator->fails()) {
            return $this->sendError(401, $validator->errors());
        }
        $newPassword = Hash::make($input['password']);
        $Image = time() . $input['img']->getClientOriginalName();
        $input['img']->move("images", $Image);
        $input['img'] = URL::to('/images') . "/" . $Image;
        $user = new User();
        $user->fill([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $newPassword,
            'img' => $input['img']
        ]);
        $user->save();
        $doctor = new Doctor();
        $doctor->fill([
            'speciality' => $input['speciality'],
            'desc' => $input['desc'],
            'user_id' => $user['id'],
            'department_id' => $input['department_id']
        ]);
        $doctor->save();
        return $this->sendResponse('success', 'doctor added successfully');
    }

    public function showDoctor()
    {
        $doctor = Doctor::query()
            ->join('contracts as c', 'doctors.id', '=', 'c.doctor_id')
            ->where('c.start_date', '<=', date("Y-m-d"))
            ->where('c.end_date', '>=', date("Y-m-d"))->get();
        return $this->sendResponse('doctors', $doctor);
    }

    public function search(Request $request)
    {
        $search = $request->header('name');
        $searchName = Doctor::query()
            ->join('users as u', 'doctors.user_id', '=', 'u.id')
            ->where('u.name', 'like', '%' . $search . '%')
            ->get();
        return $this->sendResponse('success', $searchName);
    }

    public function doctorDepartment($id)
    {
        $doctorDepartment = Doctor::query()
            ->where('department_id', '=', $id)
            ->where('state', '=', true)
            ->join('users as u', 'user_id', '=', 'u.id')
            ->get();
        return $this->sendResponse('success', $doctorDepartment);
    }

    public function docMe()
    {
        $user = Doctor::query()
            ->join('users as u', 'user_id', '=', 'u.id')
            ->select('doctors.id', 'user_id', 'name', 'phone', 'img')->get();

        return response()->json($user);
    }
    public function price()
    {
        $price = Diagnosis::query()
            ->join('doctors as d','doctor_id','=','d.id')
            ->join('users as u','user_id','=','u.id')
            ->groupBy('doctor_id','u.name')
            ->sum('price');
        return $price;
    }

}
