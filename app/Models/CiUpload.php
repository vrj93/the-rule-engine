<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CiUpload extends Model
{
    use HasFactory;

    public $table = 'ci_uploads';

    public function fileUpload (): HasMany
    {
        return $this->hasMany(FileUploads::class);
    }

    public function fileScan (): HasMany
    {
        return $this->hasMany(FileScan::class);
    }
}
