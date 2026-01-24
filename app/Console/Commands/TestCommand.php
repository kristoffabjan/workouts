<?php

namespace App\Console\Commands;

use App\Filament\Admin\Resources\AccessRequests\AccessRequestResource;
use Filament\Facades\Filament;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $url = AccessRequestResource::getUrl('index');

        $this->info("AccessRequestResource URL: $url");
    }
}
