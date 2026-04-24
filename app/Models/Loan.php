<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code',
        'user_id',
        'book_id',
        'processed_by',
        'borrowed_at',
        'due_at',
        'returned_at',
        'quantity',
        'status',
        'note',
        'return_note',
        'rejection_reason',
        'return_rejection_reason',
        'fine_amount',
        'fine_paid_at',
        'fine_paid_by',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'date',
            'due_at' => 'date',
            'returned_at' => 'date',
            'fine_amount' => 'integer',
            'fine_paid_at' => 'datetime',
        ];
    }

    public function lateDays(?Carbon $referenceDate = null): int
    {
        if (in_array($this->status, ['pending', 'rejected'], true)) {
            return 0;
        }

        $referenceDate ??= $this->returned_at ?? now();

        if (! $this->due_at || $referenceDate->lessThanOrEqualTo($this->due_at)) {
            return 0;
        }

        return $this->due_at->diffInDays($referenceDate);
    }

    public function calculateFineAmount(?Carbon $referenceDate = null): int
    {
        return $this->lateDays($referenceDate) * (int) config('library.fine_per_day', 1000);
    }

    public function displayFineAmount(): int
    {
        if (in_array($this->status, ['pending', 'rejected'], true)) {
            return 0;
        }

        if ($this->status === 'returned') {
            return (int) $this->fine_amount;
        }

        return $this->calculateFineAmount();
    }

    public function hasFine(): bool
    {
        return $this->displayFineAmount() > 0;
    }

    public function isFinePaid(): bool
    {
        return $this->fine_paid_at !== null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function finePayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fine_paid_by');
    }
}
