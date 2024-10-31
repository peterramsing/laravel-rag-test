<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceText extends Model
{

    protected $fillable = ['text'];

    public function textEmbeddings(): HasMany
    {
        return $this->hasMany(TextEmbedding::class);
    }
}
