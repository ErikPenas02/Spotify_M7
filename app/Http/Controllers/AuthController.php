<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->only(['login', 'password']);
        $errors = [];

        if (empty($data['login'])) {
            $errors['login'] = 'El campo es obligatorio.';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'El campo es obligatorio.';
        }
        if ($errors) {
            return response()->json(['errors' => $errors], 422);
        }

        $user = User::where('email', $data['login'])
            ->orWhere('username', $data['login'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['errors' => ['general' => 'Credenciales incorrectas.']], 401);
        }

        Auth::login($user);
        return response()->json(['success' => true]);
    }

    public function register(Request $request)
    {
        $data = $request->only(['username', 'name', 'surname', 'email', 'password', 'password_confirmation']);
        $errors = $this->validateRegisterData($data);
        if ($errors) {
            return response()->json(['errors' => $errors], 422);
        }
        $user = User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        Auth::login($user);
        return response()->json(['success' => true]);
    }

    public function validateLogin(Request $request)
    {
        $data = $request->only(['login', 'password']);
        $errors = [];
        if (empty($data['login'])) {
            $errors['login'] = 'El campo es obligatorio.';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'El campo es obligatorio.';
        }
        return $errors ? response()->json(['errors' => $errors], 422) : response()->json(['success' => true]);
    }

    public function validateRegister(Request $request)
    {
        $data = $request->only(['username', 'name', 'surname', 'email', 'password', 'password_confirmation']);
        $errors = $this->validateRegisterData($data);
        return $errors ? response()->json(['errors' => $errors], 422) : response()->json(['success' => true]);
    }

    private function validateRegisterData($data)
    {
        $errors = [];
        if (empty($data['username'])) {
            $errors['username'] = 'El nombre de usuario es obligatorio.';
        } elseif (User::where('username', $data['username'])->exists()) {
            $errors['username'] = 'El nombre de usuario ya está en uso.';
        }
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio.';
        } elseif (strlen($data['name']) > 15){
            $errors['name'] = 'Máx: 15 caracteres';
        }

        if (empty($data['surname'])) {
            $errors['surname'] = 'El apellido es obligatorio.';
        } elseif (strlen($data['surname']) > 15){
            $errors['surname'] = 'Máx: 15 caracteres';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no es válido.';
        } elseif (User::where('email', $data['email'])->exists()) {
            $errors['email'] = 'El correo electrónico ya está en uso.';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'La contraseña es obligatoria.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        if (empty($data['password_confirmation'])) {
            $errors['password_confirmation'] = 'Debes repetir la contraseña.';
        } elseif ($data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'] = 'Las contraseñas no coinciden.';
        }
        return $errors;
    }
}
