<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'user_id',
        'client_project_id',
        'project_offer_id',
        'email_type',
        'subject',
        'to_email',
        'body',
        'status',
        'opened_at',
        'message_id',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function offer()
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }

    /**
     * Log an email that was sent.
     */
    public static function record(
        int $userId,
        string $emailType,
        string $subject,
        string $toEmail,
        ?int $projectId = null,
        ?int $offerId = null,
        ?string $messageId = null,
        ?string $body = null,
    ): static {
        return static::create([
            'user_id' => $userId,
            'client_project_id' => $projectId,
            'project_offer_id' => $offerId,
            'email_type' => $emailType,
            'subject' => $subject,
            'to_email' => $toEmail,
            'body' => $body,
            'message_id' => $messageId,
        ]);
    }
}
