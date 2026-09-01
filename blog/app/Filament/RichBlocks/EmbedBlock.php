<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

/**
 * Paste a URL, get the embed.
 *
 * Only providers the content security policy already frames are accepted. A
 * provider that is not on that list would render an empty box on the public
 * page and nowhere would say why, so it is refused here where the author can
 * see the reason.
 */
class EmbedBlock extends RichContentCustomBlock
{
    /** Provider to the frame URL builder. Keep in step with the CSP. */
    private const PROVIDERS = [
        'youtube' => '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/',
        'vimeo' => '/vimeo\.com\/(\d+)/',
    ];

    public static function getId(): string
    {
        return 'embed';
    }

    public static function getLabel(): string
    {
        return 'Video embed';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalHeading('Embed a video')
            ->schema([
                TextInput::make('url')
                    ->label('YouTube or Vimeo URL')
                    ->url()
                    ->required()
                    ->helperText('Paste the address from the browser bar.')
                    ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                        if (! self::parse($value)) {
                            $fail('Only YouTube and Vimeo are allowed, because those are the only frames the site permits.');
                        }
                    }),

                TextInput::make('caption')->maxLength(160),
            ]);
    }

    /** @return array{provider:string,id:string}|null */
    public static function parse(?string $url): ?array
    {
        foreach (self::PROVIDERS as $provider => $pattern) {
            if ($url && preg_match($pattern, $url, $m)) {
                return ['provider' => $provider, 'id' => $m[1]];
            }
        }

        return null;
    }

    public static function toPreviewHtml(array $config): string
    {
        $parsed = self::parse($config['url'] ?? null);

        return '<p><strong>'.ucfirst($parsed['provider'] ?? 'Video').'</strong> embed'
            .(filled($config['caption'] ?? null) ? ': '.e($config['caption']) : '').'</p>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        $parsed = self::parse($config['url'] ?? null);

        if (! $parsed) {
            return null;
        }

        return view('blocks.embed', [
            'src' => $parsed['provider'] === 'youtube'
                ? "https://www.youtube-nocookie.com/embed/{$parsed['id']}"
                : "https://player.vimeo.com/video/{$parsed['id']}",
            'caption' => $config['caption'] ?? null,
        ])->render();
    }
}
