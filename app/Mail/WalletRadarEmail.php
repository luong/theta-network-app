<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalletRadarEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $params;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Activities of Your Theta Wallet';
        $message = 'We just detected a ' . $this->params['activity'] . ' from your theta wallet. Please <a href="https://explorer.thetatoken.org/account/' . $this->params['account'] . '">check it here</a>';
        return $this->subject($subject)->view('emails.wallet_radar')->with([
            'notif' => $message
        ]);
    }
}
