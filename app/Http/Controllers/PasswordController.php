<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\User;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class PasswordController extends Controller
{
    use Batoul;

    public function password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->sendError('error', 'email invalid');
        }
        $s = $request->all();
        $code = Code::query()->where('code', $s['code'])->first();
        if (!$code) {
            return $this->sendError('not found ');
        }
        $user = $code->user;
        if ($user['email'] != $s['email']) {
            return $this->sendError('email invalid');
        }
        $user['reset_password'] = true;
        $token = auth()->login($user);
        return $this->sendResponse($token, 'success');
    }

    public function resetPassword(Request $request)
    {
        $password = $request->all();
        $validate = Validator::make($password,[
            'password'=>'required|min:8',
            'c_password'=>'required|same:password'
        ]);
        if($validate->fails()){
            return $this->sendError($validate->errors());
        }
        $user = Auth::id();
        User::query()->find($user)->update([
            'password'=>Hash::make($password['password'])
        ]);
        return $this->sendResponse('success','password reset successfully');
    }

    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password'=>'required',
            'new_password' => 'required|min:8',
            'c_new_password'=>'required|same:new_password',
        ]);
        if ($validator->fails()){
            return $this->sendError('password invalid');
        }
        if (!(Hash::check($request['old_password'], Auth::user()['password']))) {
            return $this->sendError('Your current password does not matches with the password');
        }
        if(strcmp($request['old_password'], $request['new_password']) == 0){
            return $this->sendError("error","New Password cannot be same as your current password.");
        }
        $user = Auth::id();
        User::query()->find($user)->update([
            'password'=>Hash::make($request['new_password'])
        ]);
        return $this->sendResponse('success','password changed successfully');
    }
}
