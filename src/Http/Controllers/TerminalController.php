<?php

namespace Webkul\Terminal\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('terminal::terminal.index');
    }

    /**
     * Run Cmd
     */
    public function runCommand(Request $request)
    {
        $command = $request->input('command');

        // Security Check: Only allow safe commands
        if (!in_array($command, ['vendor:publish', 'cache:clear', 'config:clear'])) {
            return response()->json(['error' => 'Unauthorized command'], 403);
        }

        // Execute the command
        Artisan::call($command);
        return response()->json(['success' => Artisan::output()]);
    }
}