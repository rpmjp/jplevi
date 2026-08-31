<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Comment;
use App\Models\PageView;
use App\Models\Post;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReadingStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $last30 = PageView::where('viewed_on', '>=', now()->subDays(30)->toDateString())->sum('views');
        $prev30 = PageView::whereBetween('viewed_on', [
            now()->subDays(60)->toDateString(),
            now()->subDays(31)->toDateString(),
        ])->sum('views');

        $change = $prev30 > 0 ? round((($last30 - $prev30) / $prev30) * 100) : null;

        return [
            Stat::make('Reads, last 30 days', number_format($last30))
                ->description($change === null ? 'No prior period yet' : $change.'% on the month before')
                ->color($change === null ? 'gray' : ($change >= 0 ? 'success' : 'danger')),

            Stat::make('Confirmed subscribers', number_format(Subscriber::mailable()->count()))
                ->description(Subscriber::whereNull('confirmed_at')->count().' never confirmed')
                ->color('primary'),

            Stat::make('Comments waiting', number_format(Comment::where('status', 'pending')->count()))
                ->description(Comment::where('status', 'approved')->count().' approved')
                ->color(Comment::where('status', 'pending')->count() > 0 ? 'warning' : 'gray'),

            Stat::make('Published posts', number_format(Post::published()->count()))
                ->description(Post::where('status', 'draft')->count().' in draft')
                ->color('gray'),
        ];
    }
}
