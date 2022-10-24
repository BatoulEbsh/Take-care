<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/Q', function () {
    return \App\Models\User::query()
        ->select('*')
        ->addSelect(['phone'=> \App\Models\Person::query()
        ->selectRaw(
            "SUM(id)"
        )
        ]
        )
        ->whereIn('id',[3,1])
        ->find(2);
//    return \App\Models\Person::query()
//        ->select(
//            'name as mmm',
//            'id',
//            DB::raw("CASE
//            when id =2
//            then 9
//            else 0
//            end as f")
//        )
//        ->find(1);
});
