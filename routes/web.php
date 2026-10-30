<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Department;
use App\Services\OccupancyService;
use Carbon\CarbonImmutable;

Route::view('/manager-dashboard', 'manager-dashboard');

Route::get('/api/afdelingsbezetting', function (OccupancyService $service) {
    return $service->forWeek(Department::first(), CarbonImmutable::now());
});