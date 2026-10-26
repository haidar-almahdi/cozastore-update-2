<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminWalletConversionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $customer,
        public float $amount,
        public int $blocks
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Customer reached 100+ points',
            'message' => "{$this->customer->name} received \${$this->amount} wallet credit from loyalty conversion.",
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'amount' => $this->amount,
            'blocks' => $this->blocks,
            'type' => 'admin_loyalty_alert',
        ];
    }
}
