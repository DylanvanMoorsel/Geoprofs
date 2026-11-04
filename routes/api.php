<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Normal login (employees). Managers must use the 2FA login.
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Wrong email or password'], 422);
    }

    if ($user->role === 'manager') {
        return response()->json(['message' => 'Use the manager login'], 403);
    }

    return [
        'token' => $user->createToken('frontend')->plainTextToken,
    ];
})->middleware('throttle:5,1');

// Manager login, step 1: check password and email a 6-digit code
Route::post('/manager/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password) || $user->role !== 'manager') {
        return response()->json(['message' => 'Wrong email or password'], 422);
    }

    $code = (string) random_int(100000, 999999);

    Cache::put('otp:' . $user->id, Hash::make($code), now()->addMinutes(10));

    Mail::raw(
        "Uw inlogcode voor GeoProfs is: {$code}\n\nDeze code is 10 minuten geldig.",
        fn ($message) => $message->to($user->email)->subject('Uw GeoProfs inlogcode')
    );

    return response()->noContent();
})->middleware('throttle:5,1');

// Manager login, step 2: check the code and hand out the token
Route::post('/manager/verify-otp', function (Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'code' => 'required|digits:6',
    ]);

    $user = User::where('email', $data['email'])->where('role', 'manager')->first();
    $hash = $user ? Cache::get('otp:' . $user->id) : null;

    if (! $hash || ! Hash::check($data['code'], $hash)) {
        return response()->json(['message' => 'Code invalid or expired'], 422);
    }

    Cache::forget('otp:' . $user->id);

    return [
        'token' => $user->createToken('manager')->plainTextToken,
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
