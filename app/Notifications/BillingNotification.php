<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BillingNotification extends Notification
{
    use Queueable;

    private $receivable;
    private $type;

    public function __construct($receivable, $type)
    {
        $this->receivable = $receivable;
        $this->type = $type; // 'overdue' atau 'due_soon'
    }

    // Gunakan channel database
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    // Data apa saja yang mau disimpan ke tabel database?
    public function toArray(object $notifiable): array
    {
        return [
            'receivable_id' => $this->receivable->id,
            'customer_name' => $this->receivable->customer->name,
            'amount' => $this->receivable->amount,
            'type' => $this->type,
            'due_date' => $this->receivable->due_date, // Kolom baru ditambahkan
        ];
    }
}