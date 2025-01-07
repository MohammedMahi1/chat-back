<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserAuthRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['login', 'store']),
        ];
    }

    public function login(Request $request)
    {

        $user = $request->validate([
            'username' => [
                'required',
                'regex:/^(?=.*[0-9])(?=.*[A-Za-z])[A-Za-z0-9]{8,12}$/', // At least one letter, one number, length 8-12
            ],
            'password' => [
                'required',
                'regex:/^(?=.*[0-9])(?=.*[A-Za-z])[A-Za-z0-9-]{6,12}$/',
            ],
        ]);

        if (!Auth::guard('web')->attempt($user)) {
            return Response([
                'message' => 'Your data is incorect'
            ], 422);
        }
        $user = User::where('username', $request->username)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $device = $request->userAgent();
            $token = $user->createToken($device)->plainTextToken;
            return Response([
                'token' => $token
            ], 200);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $request->validate(rules: [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'confirmed',
            'password_confirmation' => [
                'required_with:password',
                'same:password'
            ]
        ]);
        $user_store = User::create(attributes: [
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        $user_store->save();
        return response()->json(data: [
            'message' => 'New user created successfully'
        ], status: 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($token = null)
    {
        $user = Auth::guard('sanctum')->user();
        if (null == $token) {
            $user->currentAccessToken()->delete();
            return;
        }
        $personaleToken = PersonalAccessToken::findToken($token);
        if ($user->id == $personaleToken->tokenable_id && get_class($user) == $personaleToken->tokenable_type) {
            $personaleToken->delete();
            return response()->json([
                'status' => 200,
                'message' => 'logout successful',
            ]);
        }
        return response()->json([
            'status' => 403,
            'message' => 'Forbidden, invalid token',
        ], 403);
    }

    public function updateImgProfile(Request $request){
        $request->validate([
            'image_profile' => 'nullable',
            'image_url' => 'sometimes',
        ]);


        $user = Auth::user();
        if ($request->hasFile("image_profile")) {
            $exist = Storage::disk('public')->exists("user/image/{$user->image_profile}");
            if ($exist) {
                Storage::disk('public')->delete("user/image/{$user->image_profile}");
                $img = $request->file("image_profile");// Uploadedfile;
                $imageName = Str::random() . '.' . $img->getClientOriginalName();

                $path = Storage::disk('public')->putFileAs('user/image', $img, $imageName);
                $exis = $user->update([
                    'image_profile' => $imageName,
                    'image_url' => asset("storage/" . $path)
                ]);
                if ($exis) {
                    return response()->json([
                        'message' => 'image add successfully'
                    ],200);
                }
            } else {
                $img = $request->file("image_profile");// Uploadedfile;
                $imageName = Str::random() . '.' . $img->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('user/image', $img, $imageName);
                $exis = $user->update([
                    'image_profile' => $imageName,
                    'image_url' => asset("storage/" . $path)
                ]);
                if ($exis) {
                    return response()->json([
                        'message' => 'image add successfully'
                    ],200);
                }
            }

        }
        return response()->json([
            'message' => 'not good'
        ],500);
    }
}
