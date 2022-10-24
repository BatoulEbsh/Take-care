<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::group([

    'middleware' => 'authUser:api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('refresh', [UserController::class, 'refresh']);
    Route::post('me', [UserController::class, 'me']);
});


Route::group([
    'middleware' => 'authUser:api,authAdmin:api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('addDoctor', [DoctorController::class, 'store']);


    Route::post('addArticle', [ArticleController::class, 'addArticle']);
    Route::get('showArticle/{id}', [ArticleController::class, 'show']);
    Route::post('deleteArticle/{id}', [ArticleController::class, 'destroy']);
    Route::get('showAllArticle', [ArticleController::class, 'showAll']);
    Route::get('showAllDepartment', [DepartmentController::class, 'showAll']);

});


Route::get('sendmail', [MailController::class, 'sendEmail']);
Route::post('check password', [PasswordController::class, 'password']);
Route::post('reset password', [PasswordController::class, 'resetPassword'])
    ->middleware('authUser:api');
Route::post('change password', [PasswordController::class, 'changePassword'])
    ->middleware('authUser:api');

Route::get('showDoctor/{id}', [DoctorController::class, 'show']);
Route::post('deleteDoctor/{id}', [DoctorController::class, 'destroy']);

Route::get('search', [DoctorController::class, 'search']);
Route::get('searchP', [UserController::class, 'search']);

Route::post('addDepartment', [DepartmentController::class, 'store']);
Route::get('showDepartment/{id}', [DepartmentController::class, 'show']);
Route::post('updateDepartment/{id}', [DepartmentController::class, 'update']);


Route::get('showAllDoctor', [DoctorController::class, 'showAll'])
    ->middleware(['authUser:api', 'authAdminDoctor:api']);

Route::post('addReservation', [ReservationController::class, 'addReservation'])
    ->middleware('authUser:api');
Route::get('showReservation', [ReservationController::class, 'show']);
Route::get('showDoctor', [DoctorController::class, 'showDoctor']);
Route::post('addDiagnosis', [DiagnosisController::class, 'store'])
    ->middleware(['authUser:api', 'authDoctor:api']);
Route::get('doctorDepartment/{id}', [DoctorController::class, 'doctorDepartment']);

Route::get('price', [DoctorController::class, 'price']);

Route::get('patient/{id}', [DiagnosisController::class, 'patient'])
    ->middleware('authUser:api');
Route::get('patientRes/{id}', [ReservationController::class, 'patientRes'])
    ->middleware('authUser:api');
Route::get('doctorRes/{id}', [ReservationController::class, 'doctorRes'])
    ->middleware(['authUser:api', 'authDoctor:api']);
Route::get('docMe', [DoctorController::class, 'docMe'])
    ->middleware(['authUser:api', 'authDoctor:api']);
Route::get('showAllDiagnosis',[DiagnosisController::class,'showAll'])
    ->middleware(['authUser:api', 'authDoctor:api']);
