<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileScan extends Model
{
    use HasFactory;

    public function ciUploadStatus (): BelongsTo
    {
        return $this->belongsTo(CiUpload::class);
    }

    public function ruleTrigger(): HasMany
    {
        return $this->hasMany(RuleTrigger::class);
    }
}
