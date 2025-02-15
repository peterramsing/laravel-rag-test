<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SourceText;
use App\Services\TextEmbeddingService;

class RagTestController extends Controller
{
    public function index()
    {
        return view('ragtest');
    }

    public function store(Request $request, TextEmbeddingService $textEmbeddingService)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $sourceText = SourceText::create(['text' => $request->input('text')]);

        $textEmbeddingService->addEmbeddingsToText($sourceText);

        return redirect()->back()->with('success', 'Text and embeddings saved successfully.');
    }

    public function search(Request $request, TextEmbeddingService $textEmbeddingService)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $query = $request->input('query');
        [$response, $contextChunks] = $textEmbeddingService->searchEmbeddings($query);

        return view('ragtest', ['response' => $response, 'contextChunks' => $contextChunks]);
    }
}
