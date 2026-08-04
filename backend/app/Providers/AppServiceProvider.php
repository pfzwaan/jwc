<?php

namespace App\Providers;

use App\Widgets\AboutBlock;
use App\Widgets\ContactFormBlock;
use App\Widgets\ContactInfoBlock;
use App\Widgets\CtaBannerBlock;
use App\Widgets\DienstBoxesBlock;
use App\Widgets\HeroBlock;
use App\Widgets\LinkListBlock;
use App\Widgets\LogosBlock;
use App\Widgets\ProjectsBlock;
use App\Widgets\ReviewsBlock;
use App\Widgets\RichTextBlock;
use App\Widgets\SolutionCardsBlock;
use App\Widgets\StatsBlock;
use App\Widgets\TextImageBlock;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Page-builder widgets — order here is the order shown in the picker.
        // Homepage section widgets are appended as each section is sliced.
        $this->app->singleton(WidgetRegistry::class);
        $this->app->afterResolving(WidgetRegistry::class, function (WidgetRegistry $registry): void {
            $registry->registerMany([
                // Homepage sections, in design order
                HeroBlock::class,
                LogosBlock::class,
                SolutionCardsBlock::class,
                DienstBoxesBlock::class,
                ReviewsBlock::class,
                ProjectsBlock::class,
                CtaBannerBlock::class,

                // Generic page widgets
                TextImageBlock::class,
                StatsBlock::class,
                ContactFormBlock::class,
                RichTextBlock::class,

                // Footer-scoped widgets
                AboutBlock::class,
                LinkListBlock::class,
                ContactInfoBlock::class,
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
