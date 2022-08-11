<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalletRadarEmail extends Mailable implements ShouldQueue
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
        $user = User::where('email', $this->params['to'])->first();
        if (!empty($user)) {
            if (date('Y-m-d', strtotime($user->activity_sent_at)) == date('Y-m-d')) {
                return false;
            }
            $user->update(['activity_sent_at' => date('Y-m-d H:i:s')]);
        }

        $subject = 'Activities Detected From Your Theta Wallet';
        return $this->subject($subject)->view('emails.wallet_radar')->with([
            'params' => $this->params
        ]);
    }
}
