<?php

namespace App\Models\Concerns;

use App\Support\PortfolioCover;

trait HasPortfolioCover
{
    public function initializeHasPortfolioCover(): void
    {
        if (! in_array('cover_url', $this->appends, true)) {
            $this->append('cover_url');
        }
    }

    public function getCoverUrlAttribute(): string
    {
        return PortfolioCover::assetUrl($this->cover_image, $this->portfolioCoverDefault());
    }

    abstract protected function portfolioCoverDefault(): string;
}
