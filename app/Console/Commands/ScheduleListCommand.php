<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $events = $this->laravel->make(Schedule::class)->events();

        if (count($events) === 0) {
            $this->line('No scheduled commands found.');
            return;
        }
    
        $headers = ['Command', 'Interval', 'Next Due'];
        $rows = [];
    
        foreach ($events as $event) {
            $rows[] = [
                $event->command,
                $event->expression,
                $event->nextRunDate()->format('Y-m-d H:i:s'),
            ];
        }
    
        $this->table($headers, $rows);
    }
}
