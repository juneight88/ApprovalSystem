<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Subject;

class Request extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_COORDINATOR_APPROVED = 'coordinator_approved';
    const STATUS_FINAL_APPROVED = 'final_approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PRODUCED = 'produced';

    protected $fillable = [
        'user_id',
        'subject_id',
        'specific_subject',
        'department',
        'type_of_document',
        'test_category',
        'date_request',
        'date_required',
        'date_exam',
        'exam_time_allotment',
        'modality_of_learning',
        'status',
        'producer',
        'claimants',
        'paper_size',
        'printing_mode',
        'number_of_pages',
        'number_of_copies',
        'file_path',
        'coordinator_id',
        'head_office_id',
        'coordinator_comment',
        'head_office_comment'
    ];

    protected $casts = [
        'status' => 'string',
        'date_request' => 'datetime',
        'date_exam' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function headOffice()
    {
        return $this->belongsTo(User::class, 'head_office_id');
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCoordinatorApproved()
    {
        return $this->status === self::STATUS_COORDINATOR_APPROVED;
    }

    public function isFinalApproved()
    {
        return $this->status === self::STATUS_FINAL_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function approveByCoordinator($comment = null)
    {
        $this->update([
            'status' => self::STATUS_COORDINATOR_APPROVED,
            'coordinator_comment' => $comment
        ]);
    }

    public function approveByHead($comment = null)
    {
        $this->update([
            'status' => self::STATUS_FINAL_APPROVED,
            'head_office_comment' => $comment
        ]);
    }

    public function reject($comment)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'head_office_comment' => $comment
        ]);
    }

    public function complete()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function setProducer($producer)
    {
        $this->update([
            'producer' => $producer,
            'status' => self::STATUS_PRODUCED
        ]);
    }
}
