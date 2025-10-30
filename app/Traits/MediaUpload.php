<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

trait MediaUpload
{
    /**
     * Upload media files to a specified collection.
     *
     * @param Request|UploadedFile|array $fileOrRequest The file(s) or request containing files.
     * @param string $collection The name of the media collection.
     * @return void
     */
    public function uploadMedia($fileOrRequest, $collection)
    {
        // Check if the input is a Request instance
        if ($fileOrRequest instanceof Request) {
            // Retrieve files from the request if the collection key is present
            if ($fileOrRequest->has($collection)) {
                $files = $fileOrRequest->$collection;
            } else {
                // Clear the media collection if no files are provided
                $this->clearMediaCollection($collection);
                return;
            }
        } else {
            // If it's not a request, assume it's a file or an array of files
            $files = $fileOrRequest;
        }

        // Handle multiple file uploads
        if (is_array($files)) {
            // Clear the existing media collection before adding new files
            $this->clearMediaCollection($collection);
            foreach ($files as $file) {
                // Validate the file and add it to the media collection
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $this->addMedia($file)->toMediaCollection($collection);
                }
            }
        } else {
            // Handle single file upload
            if ($files instanceof UploadedFile && $files->isValid()) {
                $this->addMedia($files)->toMediaCollection($collection);
            }
        }
    }

    /**
     * Retrieve URLs for the media associated with the model, grouped by collection.
     *
     * @return string JSON-encoded array of media URLs.
     */
    public function getModelMediaUrls()
    {
        try {
            $mediaGrouped = $this->getMedia('*')->groupBy('collection_name');

            $result = $mediaGrouped->map(function ($media) {
                if ($media->isEmpty()) {
                    return [];
                }

                if ($media->count() === 1) {
                    $firstMedia = $media->first();
                    if ($firstMedia->hasGeneratedConversion('converted')) {
                        return $firstMedia->getUrl('converted');
                    } else {
                        return $firstMedia->getUrl();
                    }
                } else {
                    return $media->map(function ($item) {
                        if ($item->hasGeneratedConversion('converted') && $item->getUrl('converted')) {
                            return $item->getUrl('converted');
                        } else {
                            return $item->getUrl();
                        }
                    })->toArray();
                }
            });

            return json_encode($result);
        } catch (\Throwable $e) {
            Log::error('MediaService Error: ' . $e->getMessage());
            return json_encode([]);
        }
    }
}
