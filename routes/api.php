<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Wrong email or password'], 422);
    }

    return [
        'token' => $user->createToken('frontend')->plainTextToken,
    ];
})->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    });

    Route::get('/user', function (Request $request) {
        $user = $request->user()->load(['department', 'manager']);

        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $user->role,
            'department' => $user->department->name,
            'manager' => $user->manager
                ? $user->manager->first_name . ' ' . $user->manager->last_name
                : null,
            'vacation_hours' => $user->vacation_hours,
        ];
    });

    // Only managers may see their team
    Route::get('/team', function (Request $request) {
        $user = $request->user();

        abort_unless($user->role === 'manager', 403);

        return $user->employees()->with('department')->get()->map(fn ($employee) => [
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'department' => $employee->department->name,
            'vacation_hours' => $employee->vacation_hours,
        ]);
    });
});