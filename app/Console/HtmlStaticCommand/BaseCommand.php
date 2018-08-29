<?php

namespace App\Console\HtmlStaticCommand;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

abstract class BaseCommand extends Command
{
    /**
     * Create a new command instance.
     * HotMatchCommand constructor.
     */
    public function __construct()
    {
        $this->signature = $this->command_name().':run {type}';
        $this->description = $this->description();

        parent::__construct();
    }

    protected abstract function command_name();

    protected abstract function description();

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $request = new Request();

        $type = $this->argument('type');

        switch ($type) {
            case "pc":
                $this->onPcHandler($request);
                break;
            case "mobile":
                $this->onMobileHandler($request);
                break;
            case "mip":
                $this->onMipHandler($request);
                break;
            case "all":
                $this->onPcHandler($request);
                $this->onMobileHandler($request);
                $this->onMipHandler($request);
                break;
        }
        $this->onCommonHandler($request);
    }

    protected function onPcHandler(Request $request) {

    }

    protected function onMobileHandler(Request $request) {

    }

    protected function onMipHandler(Request $request){

    }

    protected function onCommonHandler(Request $request) {

    }
}