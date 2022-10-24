<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\Code;
use App\Models\User;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MailController extends Controller
{
    use Batoul;

    public function sendEmail(Request $request)
    {
        $email = $request->all();
        $validator = Validator::make($email, [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->sendError('error');
        }
        if (!User::query()->where('email', $email)->exists()) {
            return $this->sendError('not found');
        }
        $user = User::query()->where('email', $email)->first();
        $code = new Code();
        $code['code'] = Str::random(6);
        $code['user_id'] = $user['id'];
        $code->save();
        Mail::to($email)->send(new TestMail($code['code']));
        return $this->sendResponse('message sent', 'message sent to mail successfully');
    }
}
