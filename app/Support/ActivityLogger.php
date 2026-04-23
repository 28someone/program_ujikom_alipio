<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ActivityLogger
{
    public static function log(?User $actor, string $action, string $description, ?Model $subject = null, array $properties = []): void
    {
        if (! $actor) {
            return;
        }

        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
