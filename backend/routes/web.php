<?php

use App\Http\Controllers\PrototypeDashboardController;
use App\Http\Controllers\Lks\ChecklistController;
use App\Http\Controllers\Lks\AccountController;
use App\Http\Controllers\Lks\DashboardController;
use App\Http\Controllers\Lks\DocumentSignatoryController;
use App\Http\Controllers\Lks\LksExportController;
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

Route::middleware(['auth', 'active-user'])->group(function () {
    Route::get('dashboard', PrototypeDashboardController::class)->name('dashboard');
    Route::get('lks-prototype/{asset}', [PrototypeDashboardController::class, 'asset'])
        ->where('asset', 'styles\.css|overrides\.css|app\.js')
        ->name('lks-prototype.asset');

    Route::prefix('api/lks')->middleware('password-changed')->name('api.lks.')->group(function (): void {
        Route::get('account', [AccountController::class, 'show'])->name('account.show');
        Route::put('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::put('account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('recap', RecapController::class)->name('recap');
        Route::get('department-trends', [RecapController::class, 'departmentTrends'])->name('department-trends');
        Route::get('role-trends', [RecapController::class, 'roleTrends'])->name('role-trends');
        Route::get('exports/personal/{period}', [LksExportController::class, 'personal'])->name('exports.personal');
        Route::get('periods/history', [PeriodController::class, 'history'])->name('periods.history');
        Route::put('checklists', [ChecklistController::class, 'store'])->name('checklists.store');
        Route::post('checklists/bulk', [ChecklistController::class, 'storeBulk'])->name('checklists.bulk');
        Route::post('periods/{period}/activate', [PeriodController::class, 'activate'])->name('periods.activate');
        Route::post('periods/{period}/close', [PeriodController::class, 'close'])->name('periods.close');
        Route::get('periods/{period}/participants', [PeriodController::class, 'participants'])->name('periods.participants.index');
        Route::post('periods/{period}/participants/{profile}', [PeriodController::class, 'addParticipant'])
            ->name('periods.participants.store');
        Route::delete('periods/{period}/participants/{participant}', [PeriodController::class, 'removeParticipant'])
            ->name('periods.participants.destroy');

        Route::prefix('admin')->name('admin.')->group(function (): void {
            Route::get('periods/{period}/participants/{participant}/checklists', [ChecklistController::class, 'correctionContext'])
                ->name('participants.checklists.context');
            Route::put('periods/{period}/participants/{participant}/checklists', [ChecklistController::class, 'correct'])
                ->name('participants.checklists.correct');
            Route::get('exports/periods/{period}', [LksExportController::class, 'summary'])->name('exports.periods.summary');
            Route::get('exports/periods/{period}/participants/{participant}', [LksExportController::class, 'participant'])->name('exports.participants.show');
            Route::get('organization', [OrganizationController::class, 'index'])->name('organization.index');
            Route::get('document-signatories', [DocumentSignatoryController::class, 'index'])->name('document-signatories.index');
            Route::post('document-signatories/{signatory}', [DocumentSignatoryController::class, 'update'])->name('document-signatories.update');
            Route::get('document-signatories/{signatory}/signature', [DocumentSignatoryController::class, 'signature'])->name('document-signatories.signature');
            Route::post('departments', [OrganizationController::class, 'storeDepartment'])->name('departments.store');
            Route::patch('departments/{department}', [OrganizationController::class, 'updateDepartment'])->name('departments.update');
            Route::delete('departments/{department}', [OrganizationController::class, 'destroyDepartment'])->name('departments.destroy');
            Route::post('teams', [OrganizationController::class, 'storeTeam'])->name('teams.store');
            Route::patch('teams/{team}', [OrganizationController::class, 'updateTeam'])->name('teams.update');
            Route::delete('teams/{team}', [OrganizationController::class, 'destroyTeam'])->name('teams.destroy');
            Route::post('santri', [OrganizationController::class, 'storeSantri'])->name('santri.store');
            Route::patch('santri/{profile}', [OrganizationController::class, 'updateSantri'])->name('santri.update');
            Route::delete('santri/{user}', [OrganizationController::class, 'destroySantri'])->name('santri.destroy');
            Route::get('configuration', [PeriodConfigurationController::class, 'index'])->name('configuration.index');
            Route::post('periods', [PeriodConfigurationController::class, 'storePeriod'])->name('periods.store');
            Route::patch('periods/{period}', [PeriodConfigurationController::class, 'updatePeriod'])->name('periods.update');
            Route::delete('periods/{period}', [PeriodConfigurationController::class, 'destroyPeriod'])->name('periods.destroy');
            Route::post('periods/{period}/holidays', [PeriodConfigurationController::class, 'storePeriodHoliday'])->name('periods.holidays.store');
            Route::post('periods/{period}/holidays/import', [PeriodConfigurationController::class, 'importPeriodHolidays'])->name('periods.holidays.import');
            Route::post('periods/{period}/holidays/import-reference', [PeriodConfigurationController::class, 'importReferenceCalendar'])->name('periods.holidays.import-reference');
            Route::delete('periods/{period}/holidays/{holiday}', [PeriodConfigurationController::class, 'destroyPeriodHoliday'])->name('periods.holidays.destroy');
            Route::post('activities', [PeriodConfigurationController::class, 'storeActivity'])->name('activities.store');
            Route::patch('activities/{activity}', [PeriodConfigurationController::class, 'updateActivity'])->name('activities.update');
            Route::delete('activities/{activity}', [PeriodConfigurationController::class, 'destroyActivity'])->name('activities.destroy');
            Route::post('calendar-holidays', [PeriodConfigurationController::class, 'storeCalendarHoliday'])->name('calendar-holidays.store');
            Route::post('calendar-holidays/import', [PeriodConfigurationController::class, 'importCalendarHolidays'])->name('calendar-holidays.import');
            Route::delete('calendar-holidays/{holiday}', [PeriodConfigurationController::class, 'destroyCalendarHoliday'])->name('calendar-holidays.destroy');
            Route::post('periods/{period}/activities', [PeriodConfigurationController::class, 'storePeriodActivity'])
                ->name('periods.activities.store');
            Route::patch('periods/{period}/activities', [PeriodConfigurationController::class, 'updatePeriodActivities'])
                ->name('periods.activities.update');
        });
    });
});

require __DIR__.'/settings.php';
