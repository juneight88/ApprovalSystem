<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    // Table name is automatically inferred from the model name, but you can define it if needed.
    // protected $table = 'document_requests';

    // Mass assignable attributes (columns)
    protected $fillable = [
        'date_request',
        'type_of_document',
        'other_document',
        'date_required',
        'paper_size',
        'mode',
        'number_of_pages',
        'number_of_copies',
        'file',
        'user_id',
        'status',
    ];

    // Optionally, you can define relationships, like user relationship (if needed)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

