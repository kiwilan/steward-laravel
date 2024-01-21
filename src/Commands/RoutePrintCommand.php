<?php

namespace Kiwilan\Steward\Commands;

use Illuminate\Console\Command;
use Kiwilan\Steward\Services\RouteService;
use Kiwilan\Steward\Utils\Converter;

class RoutePrintCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:print';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print all routes into JSON file.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $list = RouteService::make();
        Converter::saveAsJson($list->toArray(), storage_path('app/routes.json'));

        return Command::SUCCESS;
    }
}
