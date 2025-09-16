<?php

namespace PreviewLinks\Commands;

use Illuminate\Console\Command;
use PreviewLinks\Services\PreviewLinkService;

class CleanupExpiredLinksCommand extends Command
{
    protected $signature = 'preview-links:cleanup';
    protected $description = 'Clean up expired preview links';

    protected PreviewLinkService $previewService;

    public function __construct(PreviewLinkService $previewService)
    {
        parent::__construct();
        $this->previewService = $previewService;
    }

    public function handle(): int
    {
        $this->info('Cleaning up expired preview links...');
        
        $deletedCount = $this->previewService->cleanupExpiredLinks();
        
        if ($deletedCount > 0) {
            $this->info("Cleaned up {$deletedCount} expired preview link(s).");
        } else {
            $this->info('No expired preview links found.');
        }

        return 0;
    }
}