<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller

{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginRequest $request){
        $request->validated();

        $credentials = $request->only('email','password');

        if(!Auth::attempt($credentials)){
            throw ValidationException::withMessages([
                'email' => ['Girilen kimlik bilgileri geçersizdir.'],
            ]);
        }

        $user = $this->userRepository->whereEmail($request->email)->first();
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token],200);
    }

    public function logout(){
        Auth::logout();
    }
}
