<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\models\Candidat;
use App\Models\Examen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;


class ExamenController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'candidat_id' => 'required|int',
        'date' => 'required|date',
        'heure' => 'required|date_format:H:i:s',
        'type' => 'required|in:code,conduite,park',
    ]);

    // Retrieve the candidate's information
    $candidat = Candidat::findOrFail($validated['candidat_id']);
    $nom = $candidat->nom;
    $prenom = $candidat->prenom;

    // Check if there is already an exam for this candidate on the given date
    $existingExam = Examen::where('candidat_id', $validated['candidat_id'])
        ->whereDate('date', $validated['date'])
        ->exists();

    if ($existingExam) {
        return response()->json(['message' => 'Le candidat '.$nom.' '.$prenom.' a déjà un examen programmé pour cette journée.'], 422);
    }

    $seance = new Examen([
        'candidat_id' => $validated['candidat_id'],
        'date' => $validated['date'],
        'heure' => $validated['heure'],
        'type' => $validated['type'],
    ]);
    $seance->save();

    return response()->json(['message' => 'Examen ajouté avec succès!!']);
}


    // select all historique exams
    public function gethisExamens(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }

    $seances = Examen::where('candidat_id', $candidat->id)
                     ->where('date', '<', date('Y-m-d'))
                     ->orderBy('date', 'desc')
                     ->orderBy('heure', 'desc')
                     ->get();

    return response()->json($seances);
}

    // select all future exams
    public function getfutExamens(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }

    $seances = Examen::where('candidat_id', $candidat->id)
                     ->where('date', '>', date('Y-m-d'))
                     ->orderBy('date', 'desc')
                     ->orderBy('heure', 'desc')
                     ->get();

    return response()->json($seances);
}


// select exams by date = date d'aujourd'hui
public function getExamens(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }

    $seances = Examen::whereHas('candidat', function ($query) {
        $query->with('user');
    })
    ->whereDate('date', Carbon::today())
    ->orderBy('heure', 'asc')
    ->get();

    if ($seances->isEmpty()) {
        throw new Exception('Il n\'y a aucune séance aujourd\'hui.');
    }

    return response()->json($seances);
}


// recherche selon la fonction historique examen
//bech tetaawed
public function rechercheHis(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }
 $query = $request->input('query');

    $examens = Examen::where('date', '<', date('Y-m-d'))
    ->where(function ($q) use ($query) {
        $q->whereRaw("DAY(date) = ?", [$query])
          ->orWhereRaw("MONTH(date) = ?", [$query]);
    })
    ->orderBy('date', 'asc')
    ->orderBy('heure_debut', 'asc')
    ->get();
    return response()->json($examens);

}


//recherche selon the getafutExamens function
public function recherchefut(Request $request)
{
    $query = $request->input('query');
    $examens = Examen::where('date', '>', date('Y-m-d'))
                     ->where(function ($q) use ($query) {
                         $q->whereRaw("DAY(date) = ?", [$query])
                           ->orWhereRaw("MONTH(date) = ?", [$query]);
                     })
                     ->orderBy('date', 'asc')
                     ->orderBy('heure', 'asc')
                     ->get();

    return response()->json(['data' => $examens], 200);
}

// select all the exams 
public function getAllExamens(Request $request, $candidat_id)
{
    $candidat = Candidat::find($candidat_id);

    if (!$candidat) {
        throw new Exception('Candidat not found.');
    }

    $today = Carbon::today();
    $examens = Examen::where('candidat_id', $candidat->id)
        ->whereDate('date', $today)
        ->get();

    if ($examens->isEmpty()) {
        return response()->json([]);
    }

    return response()->json($examens);
}
  
}
