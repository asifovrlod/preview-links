<?php

namespace PreviewLinks\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use PreviewLinks\Services\PreviewLinkService;
use Statamic\Facades\Site;
use Statamic\View\View;

class PreviewController extends Controller
{
    protected PreviewLinkService $previewService;

    public function __construct(PreviewLinkService $previewService)
    {
        $this->previewService = $previewService;
    }

    public function show(string $token)
    {
        $previewLink = $this->previewService->getPreviewLink($token);

        if (!$previewLink) {
            return $this->renderErrorPage('Preview link not found or has expired.');
        }

        try {
            // Prepare data for rendering
            $data = array_merge($previewLink->entry_data, [
                'is_preview' => true,
                'preview_token' => $token
            ]);

            // Use the collection's default template or fall back to a generic one
            $template = $this->getTemplate($previewLink->collection);

            // Use Laravel's view helper for Blade templates
            return view($template, $data);

        } catch (\Exception $e) {
            return $this->renderErrorPage('Unable to render preview: ' . $e->getMessage());
        }
    }

    protected function getTemplate(string $collection): string
    {
        // Try collection-specific template first, fall back to generic
        $templates = [
            "collections/{$collection}/show",
            'collections/entry',
            'default'
        ];

        foreach ($templates as $template) {
            if (view()->exists($template)) {
                return $template;
            }
        }

        return 'preview-links::preview';
    }

    protected function renderErrorPage(string $message): Response
    {
        return response()->view('preview-links::error', [
            'message' => $message
        ], 404);
    }
}