<?php

namespace App\Observers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaObserver
{
    /**
     * Handle the Media "updated" event.
     *
     * This method is triggered when a media item is updated. 
     * It checks if the 'converted' media conversion has been generated, 
     * and if so, deletes the original file to save storage space.
     *
     * @param Media $media
     * @return void
     */
    public function updated(Media $media): void
    {
        if ($media->hasGeneratedConversion('converted') && file_exists($path = $media->getPath())) {
            unlink($path);
        }
    }
}
