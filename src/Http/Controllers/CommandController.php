<?php

namespace Webkul\Terminal\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandController extends Controller
{
    public function getCommands()
    {
        $commands = [];
        foreach (Artisan::all() as $name => $command) {
            $commands[] = [
                'name'        => $name,
                'description' => $command->getDescription(),
                'arguments'   => $command->getDefinition()->getArguments(),
                'options'     => $command->getDefinition()->getOptions(),
            ];
        }
        return response()->json($commands);
    }

    public function executeCommand(Request $request)
    {
        $command = $request->input('params.command');
        $arguments = $request->input('params.arguments', []);
        $options = $request->input('params.options', []);

        $output = new BufferedOutput;
        
        $params = [];
        
        foreach ($arguments as $key => $value) {
            $params[$key] = $value;
        }

        foreach ($options as $key => $value) {
            if ($value === true) {
                $params[$key] = true;
            } else {
                $params['--'.$key] = $value;
            }
        }

        Artisan::call($command, $params, $output);
        
        return response()->json([
            'success' => true,
            'output'  => $output->fetch(),
        ]);
    }
}