<?php

namespace App\Observers;

use App\Models\BillboardImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BillboardImageObserver
{
    /**
     * Handle the BillboardImage "created" event.
     */
    public function created(BillboardImage $billboardImage): void
    {
        // Log::info('Billboard Image Created: ' . $billboardImage->id);
    }

    /**
     * Handle the BillboardImage "updated" event.
     */
    public function updated(BillboardImage $billboardImage): void
    {
        //
    }

    /**
     * Handle the BillboardImage "deleted" event.
     */
    public function deleted(BillboardImage $billboardImage): void
    {
        //
    }

    /**
     * Handle the BillboardImage "restored" event.
     */
    public function restored(BillboardImage $billboardImage): void
    {
        //
    }

    /**
     * Handle the BillboardImage "force deleted" event.
     */
    public function forceDeleted(BillboardImage $billboardImage): void
    {
        //
        try {
            Storage::disk('do')->delete($billboardImage->image);
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
        }
    }
}
