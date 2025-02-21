<?php

use Illuminate\Support\Facades\Route;
use Webkul\Terminal\Http\Controllers\CommandController;
use Webkul\Terminal\Http\Controllers\TerminalController;

Route::group(['middleware' => ['admin'], 'prefix' => config('app.admin_url')], function () {
    Route::controller(TerminalController::class)->prefix('terminal')->group(function () {
        Route::get('', 'index')->name('admin.terminal.index');

        Route::post('/run-command', 'runCommand')->name('admin.terminal.run');
    });

    Route::controller(CommandController::class)->prefix('commands')->group(function () {
        Route::get('', 'getCommands')->name('admin.commands.index');

        Route::post('execute-command', 'executeCommand')->name('admin.commands.execute');
    });  
});