<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class CandidatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $candidats = Candidat::all();
        return response()->json(['data' => $candidats], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     //ajouter candidat
     public function store(Request $request, $userId)
     {
         $validator = Validator::make($request->all(), [
             'nom' => 'required|string',
             'prenom' => 'required|string',
             'date_naissance' => 'required|date|date_format:Y-m-d|after:'.date('Y-m-d', strtotime('-65 years')).'|before:'.date('Y-m-d', strtotime('-18 years')),
             'cin' => 'required|string|unique:candidats',
             'num_tel' => 'required|int|digits:8',
             'email' => 'required|email|unique:candidats',
             'adresse' => 'required|string',
             'prix_heure_code' =>'required|string',
             'prix_heure' => 'required|string',
             'prix_heure_park' => 'required|string',
             'avance' => 'required|string',
             'nbr_heure_total_code' => 'required|int',
             'nbr_heure_total' => 'required|int',
             'nbr_heure_total_park' => 'required|int',
             'password' => 'required|string|min:6',
             
         ]);
     
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 400);
         }
     
         $candidat = new Candidat([
             'user_id' => $userId,
             'nom' => $request->input('nom'),
             'prenom' => $request->input('prenom'),
             'date_naissance' => $request->input('date_naissance'),
             'cin' => $request->input('cin'),
             'num_tel' =>$request->input('num_tel'),
             'email' => $request->input('email'),
             'adresse' => $request->input('adresse'),
             'prix_heure_code' => $request->input('prix_heure_code'),
             'prix_heure' => $request->input('prix_heure'),
             'prix_heure_park' => $request->input('prix_heure_park'),
             'avance' => $request->input('avance'),
             'nbr_heure_total_code' => $request->input('nbr_heure_total_code'),
             'nbr_heure_total' => $request->input('nbr_heure_total'),
             'nbr_heure_total_park' => $request->input('nbr_heure_total_park'),
             'password' => Hash::make($request->password),
         ]);
     
         $candidat->save();
     
         return response()->json(['message' => 'Candidat ajouté avec succès!!', 'data' => $candidat], 201);
     }
     
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     //select all by user_id 
     public function show($user_id)
     {
         $candidat = Candidat::where('user_id', $user_id)->get();
     
         if ($candidat->isEmpty()) {
             return response()->json(['error' => 'Candidat non trouvé pour cet Auto-école'], 404);
         }
     
         return response()->json(['data' => $candidat], 200);
     }
     
    //select by id 
    public function select($id)
    {
        $candidat = Candidat::find($id);

        if (!$candidat) {
            return response()->json(['error' => 'Candidat non trouvé'], 404);
        }

        return response()->json(['data' => $candidat], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //update candidat
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date|date_format:Y-m-d|after:'.date('Y-m-d', strtotime('-65 years')).'|before:'.date('Y-m-d', strtotime('-18 years')),
            'cin' => 'required|string|unique:candidats',
            'num_tel' => 'required|int|digits:8',
            'email' => 'required|string|email|max:255|unique:candidats,email,'.$id,
            'adresse' => 'required|string|max:255',
            'prix_heure_code' => 'required|string',
            'prix_heure' => 'required|string',
            'prix_heure_park' => 'required|string',
            'avance' => 'required|string',
            'nbr_heure_total_code' => 'required|int',
            'nbr_heure_total' => 'required|int',
            'nbr_heure_total_park' => 'required|int',
            'password' => 'sometimes|required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        $candidat = Candidat::findOrFail($id);
    
        $candidat->nom = $request->nom;
        $candidat->prenom = $request->prenom;
        $candidat->date_naissance = $request->date_naissance;
        $candidat->cin = $request->cin;
        $candidat->num_tel = $request->num_tel;
        $candidat->email = $request->email;
        $candidat->adresse = $request->adresse;
        $candidat->prix_heure_code = $request->prix_heure_code;
        $candidat->prix_heure = $request->prix_heure;
        $candidat->prix_heure_park = $request->prix_heure_park;
        $candidat->avance = $request->avance;
        $candidat->nbr_heure_total_code = $request->nbr_heure_total_code;
        $candidat->nbr_heure_total = $request->nbr_heure_total;
        $candidat->nbr_heure_total_park = $request->nbr_heure_total_park;
        if ($request->has('password')) {
            $candidat->password = Hash::make($request->password);
        }
    
        $candidat->save();
    
        return new JsonResponse(['data' => $candidat], 200);
    }
/**
 * Update the specified resource in storage.
 *for gérer paiement 
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function updateAvance(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'avance' => 'required|numeric',
    ], [
        'avance.required' => 'Le champ avance est requis.',
        'avance.numeric' => 'Le champ avance doit être un nombre.',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()->first('avance')], 400);
    }

    $candidat = Candidat::find($id);

    if (!$candidat) {
        return response()->json(['error' => 'Candidat not found'], 404);
    }

    $newAvance = $request->input('avance');

    $candidat->avance += $newAvance;
    $candidat->save();

    return response()->json(['message' => 'Montant ajoutée avec succes!!', 'data' => $candidat], 200);
}



//supprimer candidat
public function destroy($id)
{
    $candidat = Candidat::findOrFail($id);
    $candidat->delete();

    return response()->json([
        'message' => 'Candidat deleted successfully',
    ]);
}

//recherche candidat
public function recherche(Request $request, $userId)
{
    $query = $request->input('query');
    $candidats = Candidat::where('user_id', $userId)
        ->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('nom', 'LIKE', '%'.$query.'%')
                ->orWhere('prenom', 'LIKE', '%'.$query.'%')
                ->orWhere('email', 'LIKE', '%'.$query.'%')
                ->orWhere('cin', 'LIKE', '%'.$query.'%')
                ->orWhere('num_tel', 'LIKE', '%'.$query.'%');
        })
        ->get();

    return response()->json(['data' => $candidats], 200);
}


}