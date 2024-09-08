<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileUploads extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function ciUploadStatus (): BelongsTo
    {
        return $this->belongsTo(CiUpload::class);
    }
}
