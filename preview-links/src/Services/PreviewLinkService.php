<?php

namespace PreviewLinks\Services;

use PreviewLinks\Models\PreviewLink;
use Statamic\Facades\Entry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PreviewLinkService
{
    public function createPreviewLink(string $collection, string $entryId, int $expiryDays = 7): PreviewLink
    {
        $entry = Entry::find($entryId);
        
        if (!$entry) {
            throw new \Exception("Entry not found");
        }

        // Delete any existing preview link for this entry
        $this->deleteExistingLink($collection, $entryId);

        return PreviewLink::create([
            'token' => PreviewLink::generateToken(),
            'collection' => $collection,
            'entry_id' => $entryId,
            'entry_slug' => $entry->slug(),
            'entry_title' => $entry->get('title'),
            'entry_data' => $entry->data()->toArray(),
            'expires_at' => Carbon::now()->addDays($expiryDays),
            'created_by' => Auth::user()?->email()
        ]);
    }

    public function getPreviewLink(string $token): ?PreviewLink
    {
        $link = PreviewLink::where('token', $token)->first();

        if (!$link || $link->isExpired()) {
            return null;
        }

        $link->incrementAccess();
        return $link;
    }

    public function deleteExistingLink(string $collection, string $entryId): void
    {
        PreviewLink::where('collection', $collection)
            ->where('entry_id', $entryId)
            ->delete();
    }

    public function cleanupExpiredLinks(): int
    {
        return PreviewLink::expired()->delete();
    }

    public function getAllActiveLinks()
    {
        return PreviewLink::active()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deleteLink(int $id): bool
    {
        return PreviewLink::destroy($id) > 0;
    }
}