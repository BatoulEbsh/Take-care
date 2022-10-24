<?php

namespace App\Http\Controllers;


use App\Http\Middleware\AuthPatient;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;

use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\This;

class UserController extends Controller
{
    use Batoul;

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'gender' => 'required|integer|min:0|max:1',
            'birth_date' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'required|min:6',
            'c_password' => 'required|same:password',
            'img' => 'image|max:2400',
        ]);
        if ($validator->fails()) {
            return $this->sendError('validate error', $validator->errors(), 401);
        }
        $input = $request->all();
        if (isset($input['img'])) {
            $Image = time() . $input['img']->getClientOriginalName();
            $input['img']->move("images", $Image);
            $input['img'] = '/images' . "/" . $Image;
        }
        $user = new User();
        $input['password'] = Hash::make($input['password']);
        $input['birth_date'] = date_create(date('Y-m-d', strtotime($input['birth_date'])));
        $user->fill($input);
        $user->save();
        $patient = new Patient();
        $patient->fill([
            'user_id'=>$user['id']
        ]);
        $patient->save();
        $token = Auth::login($user);
        return $this->sendResponse($token, 'user  created successfully');

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('validate error', $validator->errors(), 401);
        }
        $input = $request->all();

        if (!$token = auth()->attempt($input)) {
            return $this->sendError('validate error', 'Unauthorized', 401);
        }
        $user =User::query()
            ->select('id','name', 'phone', 'img')->find(Auth::id());
        if(Doctor::query()->where('user_id','=',$user['id'])->exists()){
            $user['is_doctor']=true ;
        }
        else
        {
            $user['is_doctor']=false ;
        }
        if(Admin::query()->where('user_id','=',$user['id'])->exists())
        {
            $user['is_admin']=true ;
        }
        else
        {
            $user['is_admin']=false ;
        }
        if(Patient::query()->where('user_id','=',$user['id'])->exists())
        {
            $user['is_patient']=true ;
        }
        else
        {
            $user['is_patient']=false ;
        }
        return $this->sendResponse('success',[$token,$user]);
    }

    public function search(Request $request)
    {
        $search = $request->header('name');
        $searchName = User::query()
            ->where('name', 'like', '%' . $search . '%')
            ->get();
        return $searchName;
    }

    public function me()
    {
        $user =User::query()
            ->select('id','name', 'phone', 'img')->find(Auth::id());

        return response()->json($user);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
}
