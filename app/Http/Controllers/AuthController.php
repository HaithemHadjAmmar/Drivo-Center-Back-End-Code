<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidat;

class AuthController extends Controller
{
    //register
    public function register(Request $req)
{
    //validate
    $rules = [
        'nom_agence' => 'required|string',
        'code_agence' => 'required|int',
        'adresse' => 'required|string',
        'num_tel' => 'required|string|digits:8',
        'email' => 'required|string|unique:users',
        'matri_fisc' =>'required|string',
        'password' => 'required|string|min:6', 
        'confirm_pass' => 'required|string|min:6|same:password',
        
    ];
    $validator = Validator::make($req->all(), $rules);
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    //create new user in users table
    $user = User::create([
        'nom_agence' => $req->nom_agence,
        'code_agence' => $req->code_agence,
        'adresse' => $req->adresse,
        'num_tel' => $req->num_tel,
        'email' => $req->email,
        'matri_fisc' => $req->matri_fisc,
        'password' => Hash::make($req->password),
    ]);

    $token = $user->createToken('Personal Access Token')->plainTextToken;
    $response = ['user' => $user, 'token' => $token];
    return response()->json($response, 200);
}

//login 
public function login(Request $req): JsonResponse
{
    $rules = [
        'email' => 'required',
        'password' => 'required|string'
    ];
    $req->validate($rules);

    $user = User::where('email', $req->email)->first();

    if ($user && Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $response = ['user_id' => $user->id, 'token' => $token];
        return response()->json($response, 200);
    }

    $response = ['message' => 'Email ou mot de passe incorrect.'];
    return response()->json($response, 400);
}


    //select user by id 
    public function select($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json([
                "ok" => false,
                "erreur" => "L'utilisateur n'existe pas."
            ]);
        }
        
        return response()->json([
            "ok" => true,
            "data" => $user
        ]);
    }
    //update profil user
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nom_agence' => 'string',
            'code_agence' => 'int',
            'adresse' => 'string|unique:users,adresse,'.$id,
            'num_tel' => 'string|digits:8',
            'email' => 'string|unique:users,email,'.$id,
            'matri_fisc' => 'string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'erreur' => $validator->errors(),
            ]);
        }
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'ok' => false,
                    'erreur' => 'L\'utilisateur n\'existe pas.',
                ]);
            }
            $user->nom_agence = $input['nom_agence'] ?? $user->nom_agence;
            $user->code_agence = $input['code_agence'] ?? $user->code_agence;
            $user->adresse = $input['adresse'] ?? $user->adresse;
            $user->num_tel = $input['num_tel'] ?? $user->num_tel;
            $user->email = $input['email'] ?? $user->email;
            $user->matri_fisc = $input['matri_fisc'] ?? $user->matri_fisc;
            $user->save();
    
            return response()->json([
                'ok' => true,
                'message' => 'Utilisateur mis à jour avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'erreur' => $e->getMessage(),
            ]);
        }
    }

    //get all users
public function getAll()
{
    $users = User::all();

    return response()->json([
        "ok" => true,
        "data" => $users
    ]);
}

    
}