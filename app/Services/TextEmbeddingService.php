<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\SourceText;
use App\Models\TextEmbedding;
use Yethee\Tiktoken\EncoderProvider;
use Pgvector\Laravel\Vector;
use Pgvector\Laravel\Distance;

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

    public function searchEmbeddings(string $query): string
    {
        $response = OpenAI::embeddings()->create([
            'model' => 'text-embedding-3-small',
            'input' => $query,
        ]);

        $queryEmbedding = $response->embeddings[0]->embedding;
        $queryVector = new Vector($queryEmbedding);

        $results = TextEmbedding::query()->nearestNeighbors('embedding', $queryVector, Distance::L2)->take(1)->get();

        $contextChunks = $results->map(fn($result) => $result->sourceText->text)->toArray();
        $context = implode("\n", $contextChunks);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a chatbot that should use only the <context> given to answer the <question> given. If you do not find the answer then say that you cannot find the answer in the given context.'],
                ['role' => 'user', 'content' => "<context>$context</context><question>$query</question>"],
            ],
            'max_tokens' => 150,
        ]);

        return $response->choices[0]->message->content;
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
