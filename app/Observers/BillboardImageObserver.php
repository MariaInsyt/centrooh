<?php

namespace App\Observers;

use App\Models\BillboardImage;
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
        $billboardId = $billboardImage->billboard_id;
        $activeImages = BillboardImage::active()->where('billboard_id', $billboardId)->get();

        if (!empty($activeImages)) {
            foreach ($activeImages as $image) {
                if ($image->id !== $billboardImage->id) {
                    $image->update(['is_active' => 0]);
                }
            }
        }
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
    }
}
