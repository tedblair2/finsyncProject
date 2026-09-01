<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class PostXMLContent implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $xmlContent)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            Http::withHeaders(['Content-type' => 'application/xml'])
                ->withBody($this->xmlContent, 'application/xml')
                ->post(env('NCBA_BACKEND_URL'));
        }catch(\Exception $e){
            // Log the error or handle it as needed
            \Log::error('Failed to post XML content: ' . $e->getMessage());
        }
    }
}
