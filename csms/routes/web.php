<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ProjectRequestController as ClientProjectRequestController;
use App\Http\Controllers\Client\ProposalResponseController;
use App\Http\Controllers\Engineer\EngineerDashboardController;
use App\Http\Controllers\Engineer\TechnicalReportController;
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\ProjectRequestController as ManagerProjectRequestController;
use App\Http\Controllers\Manager\ProposalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) return view('welcome');
    return redirect()->route(match (Auth::user()->role) {
        'client' => 'client.dashboard', 'project_manager' => 'project.manager.dashboard',
        'engineer' => 'engineer.dashboard', default => 'home',
    });
})->name('home');

Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

//client

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');
    Route::get('/client/request-project', [ClientProjectRequestController::class, 'create'])->name('client.request.project');
    Route::post('/project-request/store', [ClientProjectRequestController::class, 'store'])->name('project.request.store');
    Route::post('/proposal/{proposal}/respond', ProposalResponseController::class)->name('proposal.respond');
});

//projectmanger

Route::middleware(['auth', 'role:project_manager'])->group(function () {
    Route::get('/project-manager/dashboard', [ManagerDashboardController::class, 'index'])->name('project.manager.dashboard');
    Route::post('/project-request/{projectRequest}/status', [ManagerProjectRequestController::class, 'updateStatus'])
        ->name('project.request.status.update');
    Route::post('/manager/requests/{projectRequest}/assign', [ManagerProjectRequestController::class, 'assign'])
        ->name('manager.requests.assign');
    Route::post('/project-request/{projectRequest}/proposal', [ProposalController::class, 'store'])->name('proposal.store');
    Route::post('/manager/proposal/{proposal}/send-client', [ProposalController::class, 'sendToClient'])
        ->name('proposal.send.client');
});

//engineer

Route::middleware(['auth', 'role:engineer'])->prefix('engineer')->name('engineer.')->group(function () {

    Route::get('/dashboard', [EngineerDashboardController::class, 'index'])->name('dashboard');
    Route::post('/status-update', [EngineerDashboardController::class, 'updateStatus'])->name('status.update');
    Route::get('/technical_report', [TechnicalReportController::class, 'create'])->name('technical_report');
    Route::get('/technical-report/{project_request_id}', [TechnicalReportController::class, 'create'])->name('technicalreport.create');
    Route::post('/technical-report/store', [TechnicalReportController::class, 'storeTechnicalReport'])->name('technical_report.store');
    Route::get('/technical-report/{id}/excel', [TechnicalReportController::class, 'downloadExcel'])->name('technical_report.excel');
});

Route::middleware(['auth', 'role:client,project_manager'])->group(function () {
    Route::get('/proposal/{proposal}/pdf', [ProposalController::class, 'showPdf'])
        ->name('proposal.pdf');

    Route::get('/proposal/{proposal}/excel', [ProposalController::class, 'downloadExcel'])
        ->name('proposal.excel');
});
Route::middleware(['auth', 'role:engineer,project_manager'])->get('/view-technical-report-pdf/{id}',
    [TechnicalReportController::class, 'generatePDF'])->name('view.technical_report.pdf');
