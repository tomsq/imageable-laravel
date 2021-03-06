<?php

namespace Gause\ImageableLaravel;

use Gause\ImageableLaravel\Models\Image;
use Illuminate\Support\Facades\Storage;

class Imageable
{
    /**
     * Save image in storage.
     *
     * @param $imageFile
     * @return array
     */
    public function saveImage($imageFile): array
    {
        $img = \Intervention\Image\Facades\Image::make($imageFile); //TODO size, extension, original name

        $fileName = uniqid();

        $exploded = explode('.', $imageFile->getClientOriginalName());
        $fileExtension = end($exploded);

        $filePath = $fileName.'.'.$fileExtension;

        $result = Storage::put(
            $filePath,
            $img->encode($fileExtension, 100)
        );

        if (config('imageable-laravel.thumbnails_enabled')) {
            $img->resize(320, null);
            $thumbnailPath = $fileName.'_thumbnail.'.$fileExtension;

            $result = Storage::put(
                $thumbnailPath,
                $img->encode($fileExtension, 100)
            );
        }

        return [
            'path' => $filePath,
            'fileName' => $fileName,
            'extension' => $fileExtension,
        ];
    }

    /**
     * Saves image file to storage and Creates Image model representation of it.
     *
     * @param $imageFile
     * @param string|null $name
     * @param string|null $shortDescription
     * @param string|null $description
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return \Gause\ImageableLaravel\Models\Image
     */
    public function createImage($imageFile, string $name = null, string $shortDescription = null, string $description = null, \Illuminate\Database\Eloquent\Model $model = null): \Gause\ImageableLaravel\Models\Image
    {
        $fileSize = $imageFile->getSize();
        $originalFileName = $imageFile->getClientOriginalName();

        $savedImageDetails = $this->saveImage($imageFile);

        return Image::create([
            'name' => $name,
            'short_description' => $shortDescription,
            'description' => $description,
            'file_name' => $savedImageDetails['fileName'],
            'file_extension' => $savedImageDetails['extension'],
            'file_size' => $fileSize,
            'original_file_name' => $originalFileName,
            'model_id' => $model ? $model->id : null,
            'model_type' => $model ? get_class($model) : null,
        ]);
    }
}
