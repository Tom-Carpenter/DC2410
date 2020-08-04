<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
	private $username ="Friend of aston book store";
	private $order = null;
    public function __construct($username,$order)
    {
	//set up OrderReceipt attributes
        $this->username= $username;
    	$this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.receipt')->with(['username'=>$this->username,'order'=>$this->order]); //create email
    }
}
