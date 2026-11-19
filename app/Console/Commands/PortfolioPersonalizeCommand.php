<?php

namespace App\Console\Commands;

use App\Support\PortfolioPersonalization;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('portfolio:personalize {--force : Replace existing photo or resume files on disk}')]
#[Description('Sync profile from config, install placeholder photo/resume, unpublish sample testimonials')]
class PortfolioPersonalizeCommand extends Command
{
    public function handle(PortfolioPersonalization $personalization): int
    {
        $force = (bool) $this->option('force');

        $personalization->syncProfileFromConfig();
        $personalization->installDefaultAssets($force);
        $personalization->unpublishSampleTestimonials();

        $this->components->info('Profile synced from config/portfolio.php (.env PORTFOLIO_*).');
        $this->components->info($force
            ? 'Placeholder photo and resume installed (forced).'
            : 'Placeholder photo/resume installed where missing.');
        $this->components->info('Sample testimonials unpublished — add real quotes in Admin when ready.');

        return self::SUCCESS;
    }
}
