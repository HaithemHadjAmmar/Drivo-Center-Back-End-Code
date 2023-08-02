<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiment;
use App\Models\Candidat;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;

class ControllerPaiment extends Controller
{

    // ajouter paiement 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidat_id' => 'required|int',
            'montant' => 'required|numeric|min:0',
            'date_paiement' => 'required|date_format:Y-m-d',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        $validated = $validator->validated();
        $montant = (double) $validated['montant'];
    
        if ($montant <= 0) {
            return response()->json(['error' => 'Montant doit être supérieur à zéro.'], 400);
        }
    
        $date = date('Y-m-d', strtotime($validated['date_paiement']));
    
        $payment = Paiment::create([
            'candidat_id' => $validated['candidat_id'],
            'montant' => $montant,
            'date_paiement' => $date,
        ]);
    
        // Update candidat's avance
        $candidat = Candidat::findOrFail($validated['candidat_id']);
        $candidat->avance += $montant;
        $candidat->save();
    
        return response()->json(['message' => 'Payment stored successfully!', 'payment' => $payment], 201);
    }
    
    // select all the paiements by auto-école id
public function getPaiement(Request $request, $user_id)
    {
        $user = User::find($user_id);
    
        if (!$user) {
            throw new Exception('User not found.');
        }
    
        $candidats = Candidat::where('user_id', $user->id)->get();

if ($candidats->isEmpty()) {
    throw new Exception('No candidat found for the user.');
}

$paiements = [];

foreach ($candidats as $candidat) {
    $candidatPaiements = Paiment::where('candidat_id', $candidat->id)
        ->orderBy('date_paiement', 'asc')
        ->get();

    $paiements = array_merge($paiements, $candidatPaiements->toArray());
}

return response()->json($paiements);
}

}