<?php

namespace App\Jobs;

use App\Http\Controllers\PC\StaticController;
use App\Models\Tag\TagRelation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class TagStatic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sv_id;
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct($type,$sv_id)
    {
        $this->sv_id = $sv_id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('execute queue TagStatic');
        StaticController::staticDetail($this->type,$this->sv_id);
        Log::info('end queue TagStatic');
    }

}
