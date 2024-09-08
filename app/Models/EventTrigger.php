<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTrigger extends Model
{
    use HasFactory;

    protected $fillable = [
        'dependency',
        'dependencyLink',
        'licenses',
        'cve',
        'cveLink',
        'cvss2',
        'cvss3',
    ];

    public function ruleTrigger(): BelongsTo
    {
        return $this->belongsTo(RuleTrigger::class);
    }
}
