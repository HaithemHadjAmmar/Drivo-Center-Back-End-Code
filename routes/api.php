<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\ControllerPaiment;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\SeanceController;

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
//API Pack
Route::get('get/pack', [PackController::class, 'getPack']);
//API of register and login 
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('select/user/{id}', [AuthController::class, 'select']);
Route::put('update/user/{id}', [AuthController::class, 'update']);
Route::get('/users',[AuthController::class, 'getAll']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
// API CRUD Candidats
Route::post('add/candidats/{user_id}', [CandidatController::class, 'store']);
Route::put('update/candidat/{id}', [CandidatController::class, 'update']);
Route::put('update/avance/candidat/{id}', [CandidatController::class, 'updateAvance']);
Route::delete('delete/candidat/{id}', [CandidatController::class, 'destroy']);
Route::get('select/candidat/{id}', [CandidatController::class, 'select']);
Route::get('recherche/candidat/{user_id}', [CandidatController::class, 'recherche']);
Route::get('select/candidats/{user_id}', [CandidatController::class, 'show']);
Route::get('candidats', [CandidatController::class, 'get']);

//API seances
Route::post('/candidats/seances', [SeanceController::class, 'store']);
Route::get('/candidats/seances/{user_id}', [SeanceController::class, 'getSeances']);
Route::get('/candidats/seances4f', [SeanceController::class, 'getSeancesforfiche']);
Route::get('/candidats/prochaines/seances/{id}', [SeanceController::class, 'getProchaineSeances']);
Route::get('/candidats/archive/seance/{user_id}', [SeanceController::class, 'gethistoriqueSeances']);
Route::get('recherche/seance/his/{user_id}', [SeanceController::class, 'rechercheSeancehis']);
Route::get('recherche/seances/fut', [SeanceController::class, 'rechercheSeancefut']);
//API examen
Route::post('/candidats/examen', [ExamenController::class, 'store']);
Route::get('/candidats/select/examen', [ExamenController::class, 'index']);
Route::get('/candidats/exams/{user_id}', [ExamenController::class, 'gethisExamens']);
Route::get('/candidat/exam/{user_id}', [ExamenController::class, 'getfutExamens']);
Route::get('/candidats/examens/{user_id}', [ExamenController::class, 'getExamens']);
Route::get('recherche/examens/{user_id}', [ExamenController::class, 'rechercheHis']);
Route::get('recherche/exam', [ExamenController::class, 'recherchefut']);
Route::get('get/all/exams/{candidat_id}', [ExamenController::class, 'getAllExamens']);

// API paiement
Route::post('/candidat/paiement', [ControllerPaiment:: class, 'store']);
Route::Get('/candidats/select/paiement/{user_id}', [ControllerPaiment::class, 'getPaiement']);
//bech ntasti biha w bara 
Route::get('candidats/seance/{type}', [SeanceController::class, 'getSeance']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
