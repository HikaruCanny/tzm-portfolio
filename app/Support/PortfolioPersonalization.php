<?php

namespace App\Support;

use App\Models\Profile;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;

final class PortfolioPersonalization
{
    /** @var list<string> */
    private const SAMPLE_TESTIMONIAL_AUTHORS = [
        'Aung Min Ko',
        'Sarah Chen',
    ];

    public function syncProfileFromConfig(): void
    {
        $profile = Profile::instance();

        $profile->update([
            'name' => config('portfolio.name'),
            'title' => config('portfolio.title'),
            'email' => config('portfolio.contact_email'),
            'phone' => config('portfolio.phone'),
            'github_url' => config('portfolio.github_url'),
            'linkedin_url' => config('portfolio.linkedin_url'),
            'location' => config('portfolio.location'),
        ]);
    }

    public function installDefaultAssets(bool $force = false): void
    {
        $profile = Profile::instance()->fresh();

        $this->installPhoto($profile, $force);
        $this->installResume($profile, $force);
    }

    public function unpublishSampleTestimonials(): void
    {
        Testimonial::query()
            ->whereIn('author_name', self::SAMPLE_TESTIMONIAL_AUTHORS)
            ->update(['is_published' => false]);
    }

    private function installPhoto(Profile $profile, bool $force): void
    {
        $hasPhoto = is_string($profile->photo_path)
            && $profile->photo_path !== ''
            && Storage::disk('public')->exists($profile->photo_path);

        if ($hasPhoto && ! $force) {
            return;
        }

        $source = public_path('images/defaults/profile-avatar.svg');

        if (! is_file($source)) {
            return;
        }

        if ($hasPhoto) {
            Storage::disk('public')->delete($profile->photo_path);
        }

        $path = 'profile/site-avatar.svg';
        Storage::disk('public')->put($path, (string) file_get_contents($source));

        $profile->update(['photo_path' => $path]);
    }

    private function installResume(Profile $profile, bool $force): void
    {
        if ($profile->hasResume() && ! $force) {
            return;
        }

        if ($profile->hasResume()) {
            Storage::disk('public')->delete($profile->resume_path);
        }

        $path = 'resumes/tzm-resume-placeholder.pdf';
        Storage::disk('public')->put($path, $this->placeholderResumePdf());

        $profile->update(['resume_path' => $path]);
    }

    private function placeholderResumePdf(): string
    {
        return <<<'PDF'
%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj
4 0 obj<</Length 81>>stream
BT /F1 18 Tf 72 700 Td (Thant Zin Moe - resume placeholder) Tj ET
endstream
endobj
5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj
xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000052 00000 n 
0000000101 00000 n 
0000000242 00000 n 
0000000372 00000 n 
trailer<</Size 6/Root 1 0 R>>
startxref
449
%%EOF
PDF;
    }
}
