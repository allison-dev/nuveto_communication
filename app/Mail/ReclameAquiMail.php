<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReclameAquiMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->data['complaint_title']);
        $this->to('sigmain@nuveto.com.br', 'Sigma - ' . $this->data['network']);
        $this->replyTo('sigmaout@nuveto.com.br', 'Sigma - '.$this->data['network']);
        $this->from('sigmaout@nuveto.com.br', 'Sigma - ' . $this->data['network']);
        return $this->view('mail.ReclameAqui', [
            'icon'              => $this->data['icon'],
            'header_img'        => $this->data['header_img'],
            'footer_img'        => $this->data['footer_img'],
            'customer_name'     => isset($this->data['customer_name']) ? $this->data['customer_name'] : '',
            'customer_email'    => isset($this->data['customer_email']) ? $this->data['customer_email'] : '',
            'complaint_title'   => isset($this->data['complaint_title']) ? $this->data['complaint_title'] : '',
            'complaint_content' => isset($this->data['complaint_content']) ? $this->data['complaint_content'] : '',
            'salute'            => isset($this->data['salute']) ? $this->data['salute'] : '',
            'network'           => isset($this->data['network']) ? $this->data['network'] : '',
            'ticket_id'         => isset($this->data['ticket_id']) ? $this->data['ticket_id'] : ''
        ]);
    }
}
