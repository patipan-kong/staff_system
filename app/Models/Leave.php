<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    const TYPE_VACATION = 'vacation';
    const TYPE_SICK = 'sick';
    const TYPE_SICK_WITH_CERTIFICATE = 'sick_with_certificate';
    const TYPE_PERSONAL = 'personal';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'medical_certificate',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'days' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculateDays()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        $days = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $days++;
            }
            $start->addDay();
        }
        
        return $days;
    }

    public function getTypeLabel()
    {
        switch ($this->type) {
            case self::TYPE_VACATION:
                return 'Vacation';
            case self::TYPE_SICK:
                return 'Sick Leave';
            case self::TYPE_SICK_WITH_CERTIFICATE:
                return 'Sick Leave (with Certificate)';
            case self::TYPE_PERSONAL:
                return 'Personal Leave';
            default:
                return ucfirst($this->type);
        }
    }

    public function getStatusLabel()
    {
        return ucfirst($this->status);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }
}