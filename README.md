# Rag Test

## Setup
1. `cp .env.example .env`
2. Postgresql: https://dbngin.com/ (update your .env)
3. Get your OpenAI key, put it in the .env file
4. `composer install`
5. `composer run dev` (or however you boot your Laravel locally)
6. Add some data `php artisan db:seed --class=SourceTextSeeder`

## Usage
If you want to add more source material paste text into the textarea and add it.

If you want to search, query, then see the embeddings used
