<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Textarea;

/**
 * Raw HTML, rendered where it sits.
 *
 * WordPress calls this the Custom HTML block and gates it behind the
 * unfiltered_html capability, which on a site with more than one writer means
 * administrators only. The same rule applies here, and it is applied where the
 * blocks are registered rather than here, so this class never has to be trusted
 * to enforce it.
 *
 * What limits the damage is not this block, it is the content security policy.
 * script-src is 'self', so a pasted <script> or an inline onclick does not run;
 * frame-src is YouTube and Vimeo, so an arbitrary iframe renders nothing. Both
 * fail silently in the browser, which is why the form says so plainly: markup
 * that will never work should be refused at the point somebody writes it, not
 * discovered on the live page.
 */
class HtmlBlock extends RichContentCustomBlock
{
    /** Hosts the content security policy will frame. Keep in step with it. */
    private const FRAMEABLE = ['www.youtube-nocookie.com', 'youtube-nocookie.com', 'player.vimeo.com'];

    public static function getId(): string
    {
        return 'html';
    }

    public static function getLabel(): string
    {
        return 'Custom HTML';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Custom HTML')
            ->modalDescription('Rendered exactly as written. Scripts and frames are blocked by the site\'s security policy, so anything depending on them will be silently dead on the page.')
            ->schema([
                Textarea::make('html')
                    ->label('HTML')
                    ->required()
                    ->rows(14)
                    ->extraInputAttributes([
                        'spellcheck' => 'false',
                        'style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.82rem; line-height: 1.6;',
                    ])
                    ->helperText('Saved as written. The block shows both the rendered result and the source, so it can be checked before publishing.')
                    ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                        foreach (self::deadOnArrival((string) $value) as $problem) {
                            $fail($problem);
                        }
                    }),
            ]);
    }

    /**
     * Markup the browser will refuse to run, so the author hears it now.
     *
     * @return array<int,string>
     */
    public static function deadOnArrival(string $html): array
    {
        $problems = [];

        if (preg_match('/<script\b/i', $html)) {
            $problems[] = 'A <script> tag will not run: the site\'s content security policy only allows scripts it serves itself. Remove it, or ask for what it does to be built into the site properly.';
        }

        if (preg_match('/\son[a-z]+\s*=/i', $html)) {
            $problems[] = 'An inline event handler such as onclick will not run, for the same reason a <script> tag will not.';
        }

        // Every src is pulled out and checked against the list, rather than
        // asking one pattern to mean "an iframe whose source is not allowed".
        // A negative lookahead there backtracks past the opening quote and ends
        // up matching the frames it was meant to permit.
        preg_match_all('/<iframe\b[^>]*\ssrc\s*=\s*["\']([^"\']*)["\']/i', $html, $frames);

        foreach ($frames[1] ?? [] as $src) {
            $host = parse_url($src, PHP_URL_HOST);

            if (! in_array($host, self::FRAMEABLE, true)) {
                $problems[] = 'Only YouTube and Vimeo frames are permitted, and the Video embed block already handles both. A frame pointing at '
                    .($host ? e($host) : 'anywhere else').' renders as an empty box.';
            }
        }

        return $problems;
    }

    /**
     * What the editor shows.
     *
     * The rendered markup first, because seeing it is the point of the block,
     * then the source behind a native disclosure so it can be read without a
     * round trip through the modal. <details> rather than a toggle button: the
     * editor preview is static HTML, and script would not run in it either.
     */
    public static function toPreviewHtml(array $config): string
    {
        $html = (string) ($config['html'] ?? '');

        return view('filament.blocks.html-preview', [
            'html' => $html,
            'problems' => self::deadOnArrival($html),
        ])->render();
    }

    public static function toHtml(array $config, array $data): string
    {
        return (string) ($config['html'] ?? '');
    }
}
