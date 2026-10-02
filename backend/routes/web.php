<?php

use App\Http\Controllers\PrototypeDashboardController;
use App\Http\Controllers\Lks\ChecklistController;
use App\Http\Controllers\Lks\AccountController;
use App\Http\Controllers\Lks\DashboardController;
use App\Http\Controllers\Lks\PeriodController;
use App\Http\Controllers\Lks\OrganizationController;
use App\Http\Controllers\Lks\PeriodConfigurationController;
use App\Http\Controllers\Lks\RecapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', PrototypeDashboardController::class)->name('dashboard');
    Route::get('lks-prototype/{asset}', [PrototypeDashboardController::class, 'asset'])
        ->where('asset', 'styles\.css|overrides\.css|app\.js')
        ->name('lks-prototype.asset');

    Route::prefix('api/lks')->name('api.lks.')->group(function (): void {
        Route::get('account', [AccountController::class, 'show'])->name('account.show');
        Route::put('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::put('account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('recap', RecapController::class)->name('recap');
        Route::get('department-trends', [RecapController::class, 'departmentTrends'])->name('department-trends');
        Route::get('periods/history', [PeriodController::class, 'history'])->name('periods.history');
        Route::put('checklists', [ChecklistController::class, 'store'])->name('checklists.store');
        Route::post('periods/{period}/activate', [PeriodController::class, 'activate'])->name('periods.activate');
        Route::post('periods/{period}/close', [PeriodController::class, 'close'])->name('periods.close');
        Route::post('periods/{period}/participants/{profile}', [PeriodController::class, 'addParticipant'])
            ->name('periods.participants.store');

        Route::prefix('admin')->name('admin.')->group(function (): void {
            Route::get('organization', [OrganizationController::class, 'index'])->name('organization.index');
            Route::post('departments', [OrganizationController::class, 'storeDepartment'])->name('departments.store');
            Route::patch('departments/{department}', [OrganizationController::class, 'updateDepartment'])->name('departments.update');
            Route::post('teams', [OrganizationController::class, 'storeTeam'])->name('teams.store');
            Route::patch('teams/{team}', [OrganizationController::class, 'updateTeam'])->name('teams.update');
            Route::post('santri', [OrganizationController::class, 'storeSantri'])->name('santri.store');
            Route::patch('santri/{profile}', [OrganizationController::class, 'updateSantri'])->name('santri.update');
            Route::get('configuration', [PeriodConfigurationController::class, 'index'])->name('configuration.index');
            Route::post('periods', [PeriodConfigurationController::class, 'storePeriod'])->name('periods.store');
            Route::patch('periods/{period}', [PeriodConfigurationController::class, 'updatePeriod'])->name('periods.update');
            Route::post('activities', [PeriodConfigurationController::class, 'storeActivity'])->name('activities.store');
            Route::patch('activities/{activity}', [PeriodConfigurationController::class, 'updateActivity'])->name('activities.update');
            Route::post('periods/{period}/activities', [PeriodConfigurationController::class, 'storePeriodActivity'])
                ->name('periods.activities.store');
            Route::patch('periods/{period}/activities', [PeriodConfigurationController::class, 'updatePeriodActivities'])
                ->name('periods.activities.update');
        });
    });
});

require __DIR__.'/settings.php';
