<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\SourceText;
use App\Models\TextEmbedding;
use Yethee\Tiktoken\EncoderProvider;

class TextEmbeddingService
{
    private \Yethee\Tiktoken\Encoder $encoder;

    public function __construct()
    {
        $provider = new EncoderProvider();
        $this->encoder = $provider->getForModel('text-embedding-3-small');
    }

    public function addEmbeddingsToText(SourceText $sourceText): void
    {
        $text = $sourceText->text;

        // Check if embeddings already exist
        $existingEmbeddings = TextEmbedding::where('source_text_id', $sourceText->id)->exists();

        if ($existingEmbeddings) {
            return;
        }

        $chunks = $this->chunkText($text);

        foreach ($chunks as $chunkText) {
            $response = OpenAI::embeddings()->create([
                'model' => 'text-embedding-3-small',
                'input' => $chunkText,
            ]);

            $embedding = $response->embeddings[0]->embedding;

            TextEmbedding::create([
                'source_text_id' => $sourceText->id,
                'embedding' => json_encode($embedding),
            ]);
        }
    }

    private function chunkText(string $text): array
    {
        $maxTokens = 8191;
        $overlapTokens = 200;
        $tokens = $this->tokenize($text);
        $tokensCount = count($tokens);

        $chunks = [];
        $start = 0;

        while ($start < $tokensCount) {
            $end = min($start + $maxTokens, $tokensCount);
            $chunkTokens = array_slice($tokens, $start, $end - $start);
            $chunkText = $this->detokenize($chunkTokens);
            $chunks[] = $chunkText;
            $start += $maxTokens - $overlapTokens;
        }

        return $chunks;
    }

    private function tokenize(string $text): array
    {
        return $this->encoder->encode($text);
    }

    private function detokenize(array $tokens): string
    {
        return $this->encoder->decode($tokens);
    }
}
