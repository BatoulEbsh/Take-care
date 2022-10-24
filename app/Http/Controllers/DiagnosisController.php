<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Doctor;
use App\Models\Reservation;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DiagnosisController extends Controller
{
    use Batoul;

    public function store(Request $request)
    {
        $input = $request->all();
        $user = Auth::user();
        if (!$user->doctor) {
            return $this->sendError('error', 'you are not doctor');
        }
        $validator = Validator::make($input, [
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'doctorName' => 'required|string',
            'patientName' => 'required|string',
            'diagnosis' => 'required|string',
            'price' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/'
        ]);
        if ($validator->fails()) {
            return $this->sendError(401, $validator->errors());
        }
        $diagnosis = new Diagnosis();
        $diagnosis->fill([
            'doctor_id' => $input['doctor_id'],
            'patient_id' => $input['patient_id'],
            'doctorName' => $input['doctorName'],
            'patientName' => $input['patientName'],
            'diagnosis' => $input['diagnosis'],
            'price' => $input['price']
        ]);
        $diagnosis->save();
        return $this->sendResponse('success', 'diagnosis added successfully');
    }

    public function showAll(){
        $diagnosis = Diagnosis::query()->select('*')->get();
        if (is_null($diagnosis)) {
            return $this->sendError(404, 'notfound');
        }

        return $this->sendResponse('success', $diagnosis);
    }
    public function diagnosis(int $id)
    {
        $doctor_id = Doctor::find($id);
        $user = Auth::user();
        if (!$user->admin) {
            return $this->sendError('error', 'you are not admin');
        }
        $diagnosis = Diagnosis::query()
            ->select('diagnosis')
            ->where('doctor_id', '=', $doctor_id)
            ->get();
        return $this->sendResponse('success', $diagnosis);
    }

    public function patient($id)
    {
        $patient = Reservation::query()
            ->where('doctor_id', '=', $id)
            ->join('patients as p', 'patient_id', '=', 'p.id')
            ->join('users as u','p.user_id','=','u.id')
            ->select('patient_id','name')
            ->get();
        return $this->sendResponse('success', $patient);
    }
}
