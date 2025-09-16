<?php

namespace PreviewLinks\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PreviewLinks\Services\PreviewLinkService;
use Statamic\Facades\Collection;

class CPController extends Controller
{
    protected PreviewLinkService $previewService;

    public function __construct(PreviewLinkService $previewService)
    {
        $this->previewService = $previewService;
    }

    public function index()
    {
        $previewLinks = $this->previewService->getAllActiveLinks();
        $collections = Collection::all()->map(function ($collection) {
            return [
                'handle' => $collection->handle(),
                'title' => $collection->title(),
            ];
        })->values();

        return view('preview-links::cp.index', [
            'previewLinks' => $previewLinks,
            'collections' => $collections
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'collection' => 'required|string',
            'entry_id' => 'required|string',
            'expiry_days' => 'integer|min:1|max:30'
        ]);

        try {
            $previewLink = $this->previewService->createPreviewLink(
                $request->collection,
                $request->entry_id,
                $request->expiry_days ?? 7
            );

            return response()->json([
                'success' => true,
                'preview_url' => $previewLink->getPreviewUrl(),
                'expires_at' => $previewLink->expires_at->format('Y-m-d H:i:s'),
                'message' => 'Preview link generated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        if ($this->previewService->deleteLink($id)) {
            return response()->json([
                'success' => true,
                'message' => 'Preview link deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Preview link not found.'
        ], 404);
    }
}