<?php

namespace App\Http\Controllers;
use App\Models\Seance;
use App\models\Candidat;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class SeanceController extends Controller
{
 public function store(Request $request)
{
    $validated = $request->validate([
        'candidat_id' => 'required|int',
        'date' => 'required|date',
        'heure_debut' => 'required|date_format:H:i:s',
        'nbr_heure' => 'required|int',
        'type' => 'required|in:code,conduite,park',
    ]);

    // Convert date format
    $date = date('Y-m-d', strtotime($validated['date']));
    $heure_debut = strtotime($validated['heure_debut']);
    $nbr_heure = $validated['nbr_heure'];

    // Check if any seance already exists at the same hour
    $existingSeance = Seance::where('date', $date)
        ->whereTime('heure_debut', date('H:i:s', $heure_debut))
        ->exists();

    if ($existingSeance) {
        return response()->json(['message' => 'Une Séance pour ce candidat a déjà été programmée à cette heure.'], 400);
    }

    // Retrieve the candidate's information
    $candidat = Candidat::findOrFail($validated['candidat_id']);
    $nom = $candidat->nom;
    $prenom = $candidat->prenom;

    if ($validated['type'] !== 'code') {
        // Check if the candidate already has a 'conduite' or 'park' seance on the same day
        $existingSeance = Seance::where('candidat_id', $validated['candidat_id'])
            ->whereDate('date', $date)
            ->where(function ($query) {
                $query->where('type', 'conduite')
                    ->orWhere('type', 'park');
            })
            ->exists();

        if ($existingSeance) {
            return response()->json(['message' => 'Le candidat '.$nom.' '.$prenom.' a déjà une Séance programmée pour cette journée.'], 400);
        }
    }

    $seance = new Seance([
        'candidat_id' => $validated['candidat_id'],
        'date' => $date,
        'heure_debut' => $validated['heure_debut'],
        'nbr_heure' => $validated['nbr_heure'],
        'type' => $validated['type'],
    ]);
    $seance->save();

    return response()->json(['message' => 'Séance ajoutée avec succès']);
}

    

 // Select Seance date = date d'aujourd'hui
public function getSeances(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }

    $seances = Seance::whereHas('candidat', function ($query) {
        $query->with('user');
    })
    ->whereDate('date', Carbon::today())
    ->orderBy('heure_debut', 'asc')
    ->get();

    if ($seances->isEmpty()) {
        throw new Exception('Il n\'y a aucune séance aujourd\'hui.');
    }

    return response()->json($seances);
}


// Select all the Seance for fiche_candidat & archive
public function getSeancesforfiche()
{
    $seances = Seance::orderBy('date', 'asc')
                     ->orderBy('heure_debut', 'asc')
                     ->get();

    return response()->json($seances);
}


// select prochaine Seance 
public function getprochaineSeances(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }

    $seances = Seance::where('candidat_id', $candidat->id)
                     ->where('date', '>', date('Y-m-d'))
                     ->orderBy('date', 'desc')
                     ->orderBy('heure_debut', 'desc')
                     ->get();

    return response()->json($seances);
}


//select les historique seances by Candidats id 
public function gethistoriqueSeances(Request $request, $user_id)
{
    $user = User::find($user_id);

    if (!$user) {
        throw new Exception('User not found.');
    }

    $candidat = Candidat::where('user_id', $user->id)->first();

    if (!$candidat) {
        throw new Exception('Candidat not found for the user.');
    }

    $seances = Seance::where('candidat_id', $candidat->id)
                     ->where('date', '<', date('Y-m-d'))
                     ->orderBy('date', 'desc')
                     ->orderBy('heure_debut', 'desc')
                     ->get();

    return response()->json($seances);
}





//bech ntasti biha w bara 
public function getSeance(Request $request, $type = null)
{
    $query = Seance::whereDate('date', '=', date('Y-m-d'));

    if ($type) {
        $query->where('type', $type);
    }

    $seances = $query->orderBy('heure_debut', 'asc')->get();

    return response()->json($seances);
}

public function getCodeSeances(Request $request)
{
    return $this->getSeance($request, 'code');
}

public function getConduiteSeances(Request $request)
{
    return $this->getSeance($request, 'conduite');
}


//recherche selon the getarchiveSeances function
public function rechercheSeancehis(Request $request, $user_id)
{
    $query = $request->input('query');

    $user = User::find($user_id);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $seances = Seance::where('date', '<', date('Y-m-d'))
        ->whereIn('candidat_id', function ($subquery) use ($user_id) {
            $subquery->select('candidats.id')
                ->from('candidats')
                ->join('users', 'candidats.user_id', '=', 'users.id')
                ->where('users.id', $user_id);
        })
        ->where(function ($q) use ($query) {
            $q->whereRaw("DAY(date) = ?", [$query])
              ->orWhereRaw("MONTH(date) = ?", [$query]);
        })
        ->orderBy('date', 'asc')
        ->orderBy('heure_debut', 'asc')
        ->get();

    return response()->json(['data' => $seances], 200);
}


//recherche selon the getProchaineSeances function
public function rechercheSeancefut(Request $request)
{
    $query = $request->input('query');
    $seance = Seance::where('date', '>', date('Y-m-d'))
                     ->where(function ($q) use ($query) {
                         $q->whereRaw("DAY(date) = ?", [$query])
                           ->orWhereRaw("MONTH(date) = ?", [$query]);
                     })
                     ->orderBy('date', 'asc')
                     ->orderBy('heure_debut', 'asc')
                     ->get();

                     return response()->json(['data' => $seance], 200);
}


}
