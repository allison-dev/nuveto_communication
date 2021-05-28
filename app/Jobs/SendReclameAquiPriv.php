<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReclameAquiPriv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['Response', 'id:' . $this->data['externalId']];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $raSend = (object)[
            'externalId'    => $this->data['externalId'],
            'text'          => $this->data['text'],
            'sender'        => $this->data['sender']
        ];

        sendMessageReclameAqui($raSend, 'priv');
    }
}
