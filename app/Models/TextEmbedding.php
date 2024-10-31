<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pgvector\Laravel\Vector;
use Pgvector\Laravel\HasNeighbors;

class TextEmbedding extends Model
{

    use HasNeighbors;

    protected $fillable = ['source_text_id', 'embedding'];

    protected $casts = [
        'embedding' => Vector::class,
    ];

    public function sourceText(): BelongsTo
    {
        return $this->belongsTo(SourceText::class, 'source_text_id');
    }
}
