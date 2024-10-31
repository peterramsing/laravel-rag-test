<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextEmbedding extends Model
{

    protected $fillable = ['source_text_id', 'embedding'];

    public function sourceText(): BelongsTo
    {
        return $this->belongsTo(SourceText::class, 'source_text_id');
    }
}
