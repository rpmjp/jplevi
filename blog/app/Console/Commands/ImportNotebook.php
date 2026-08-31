<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Turns a Jupyter notebook into a draft post.
 *
 * Reads the .ipynb JSON directly rather than shelling out to nbconvert, which
 * is not installed on shared hosting. Markdown cells become prose, code cells
 * become highlighted blocks, and image outputs are inlined as data URIs so a
 * chart survives without a separate upload step.
 */
class ImportNotebook extends Command
{
    protected $signature = 'blog:import-notebook {path} {--title=} {--author=}';

    protected $description = 'Import a Jupyter notebook as a draft post';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! is_file($path)) {
            $this->error("No notebook at {$path}");

            return self::FAILURE;
        }

        $notebook = json_decode((string) file_get_contents($path), true);

        if (! is_array($notebook) || ! isset($notebook['cells'])) {
            $this->error('That does not look like a notebook.');

            return self::FAILURE;
        }

        $html = [];

        foreach ($notebook['cells'] as $cell) {
            $source = is_array($cell['source'] ?? null) ? implode('', $cell['source']) : (string) ($cell['source'] ?? '');

            if (trim($source) === '') {
                continue;
            }

            if (($cell['cell_type'] ?? '') === 'markdown') {
                // Left as written: maths delimiters and headings are handled
                // downstream by the editor and KaTeX rather than converted here.
                $html[] = '<div class="nb-md">'.nl2br(e($source)).'</div>';

                continue;
            }

            if (($cell['cell_type'] ?? '') === 'code') {
                $html[] = '<pre><code class="language-python">'.e($source).'</code></pre>';
                $html[] = $this->outputs($cell['outputs'] ?? []);
            }
        }

        $title = $this->option('title') ?: Str::headline(pathinfo($path, PATHINFO_FILENAME));
        $author = $this->option('author')
            ? User::where('email', $this->option('author'))->firstOrFail()
            : User::firstOrFail();

        $post = Post::create([
            'user_id' => $author->id,
            'title' => $title,
            'body' => implode("\n", array_filter($html)),
            'status' => 'draft',
        ]);

        $this->info("Imported as draft: {$post->title}");
        $this->line('  Preview: '.route('blog.show', $post).'?preview='.$post->preview_token);

        return self::SUCCESS;
    }

    private function outputs(array $outputs): string
    {
        $parts = [];

        foreach ($outputs as $output) {
            $png = data_get($output, 'data.image/png');

            if ($png) {
                $data = is_array($png) ? implode('', $png) : $png;
                $parts[] = '<img src="data:image/png;base64,'.trim($data).'" alt="Notebook output">';

                continue;
            }

            $text = data_get($output, 'data.text/plain') ?? data_get($output, 'text');

            if ($text) {
                $flat = is_array($text) ? implode('', $text) : $text;
                $parts[] = '<pre><code>'.e(Str::limit($flat, 2000)).'</code></pre>';
            }
        }

        return implode("\n", $parts);
    }
}
