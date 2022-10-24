<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Doctor;
use App\Models\Reservation;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ReservationController extends Controller
{
    use Batoul;

    public function show()
    {
        $reservation = Reservation::all();
        if (is_null($reservation)) {
            return $this->sendError(404, 'notfound');
        }
        return $this->sendResponse('reservation', $reservation);
    }

    public function addReservation(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'start_time' => 'required|date_format:H:i',
            'date' => 'required|date',
        ]);
        if ($validator->fails()) {
            return $this->sendError(401, $validator->errors());
        }
        if (!$doctor = Doctor::query()
            ->where('state', '=', true)
        ) {
            return $this->sendError('error', 'valid');
        }
        if (Reservation::query()
            ->where('date', '=', $input['date'])
            ->where(
                'start_time',
                '>=',
                date('H:i', strtotime($input['start_time'] . '-15 minutes'))
            )->where(
                'start_time', '<=',
                date('H:i', strtotime($input['start_time']))
            )
            ->exists()
        ) {
            return $this->sendError('error', 'time valid');
        }

        $user = Auth::user();
        $reservation = new Reservation();
        $reservation->fill([
            'start_time' => $input['start_time'],
            'date' => $input['date'],
            'patient_id' => $user->patient['id'],
            'doctor_id'=>$input['doctor_id']
        ]);
        $reservation->save();
        return $this->sendResponse('success', 'reservation added successfully');

    }

    public function patientRes($id)
    {
        $patientRes = Reservation::query()
            ->where('patient_id', '=', $id)
            ->join('doctors as d', 'reservations.doctor_id', '=', 'd.id')
            ->join('departments as t', 'department_id', '=', 't.id')
            ->select('t.name as department','start_time','date')
            ->get();
        return $patientRes;
    }
    public function doctorRes($id)
    {
        $patientRes = Reservation::query()
            ->where('doctor_id', '=', $id)
            ->join('users as u', 'reservations.doctor_id', '=', 'u.id')
            ->select('u.name as name','start_time','date')
            ->get();
        return $patientRes;
    }

}
