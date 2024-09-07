<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RuleTrigger extends Model
{
    use HasFactory;

    public function fileScan(): BelongsTo
    {
        return $this->belongsTo(FileScan::class);
    }

    public function eventTrigger(): HasMany
    {
        return $this->hasMany(EventTrigger::class);
    }
}
