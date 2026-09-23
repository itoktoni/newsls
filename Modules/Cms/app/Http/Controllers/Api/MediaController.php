<?php

namespace Modules\Cms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Cms\Http\Controllers\Controller;
use Modules\Cms\Models\Media;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::images()->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                    ->orWhere('alt', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 24);
        $media = $query->paginate($perPage);

        $media->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'filename' => $item->filename,
                'original_filename' => $item->original_filename,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'url' => $item->url,
                'thumbnail' => $item->thumbnail,
                'alt' => $item->alt,
                'title' => $item->title,
                'width' => $item->width,
                'height' => $item->height,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json($media);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|file|mimes:jpeg,png,gif,webp,svg|max:10240',
        ]);

        @ini_set('memory_limit', '256M');

        $uploadedImages = [];
        $disk = 'public';

        foreach ($request->file('images', []) as $file) {
            $originalFilename = $file->getClientOriginalName();
            $filename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)).'_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('media/'.date('Y/m'), $filename, $disk);

            $width = null;
            $height = null;
            $thumbnailPath = null;

            if (str_starts_with($file->getMimeType(), 'image/') && $file->getMimeType() !== 'image/svg+xml') {
                try {
                    $imageSize = getimagesize($file->getPathname());
                    if ($imageSize) {
                        $width = $imageSize[0];
                        $height = $imageSize[1];
                    }

                    $thumbDir = 'media/'.date('Y/m').'/thumbs';
                    if (! Storage::disk($disk)->exists($thumbDir)) {
                        Storage::disk($disk)->makeDirectory($thumbDir);
                    }

                    $thumbnailPath = $thumbDir.'/'.$filename;
                    $this->createThumbnail($file->getPathname(), Storage::disk($disk)->path($thumbnailPath), 300, 300);
                } catch (\Exception $e) {
                    $thumbnailPath = null;
                }
            }

            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'disk' => $disk,
                'path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'width' => $width,
                'height' => $height,
                'user_id' => auth()->id(),
            ]);

            $uploadedImages[] = [
                'id' => $media->id,
                'filename' => $media->filename,
                'url' => $media->url,
                'thumbnail' => $media->thumbnail,
                'width' => $media->width,
                'height' => $media->height,
                'size' => $media->human_size,
            ];
        }

        return response()->json([
            'success' => true,
            'images' => $uploadedImages,
        ], 201);
    }

    public function destroy(Media $media)
    {
        Storage::disk($media->disk)->delete($media->path);
        if ($media->thumbnail_path) {
            Storage::disk($media->disk)->delete($media->thumbnail_path);
        }

        $media->delete();

        return response()->json(['success' => true]);
    }

    protected function createThumbnail(string $source, string $destination, int $maxWidth, int $maxHeight): bool
    {
        $imageSize = getimagesize($source);
        if (! $imageSize) {
            return false;
        }

        [$origWidth, $origHeight, $type] = $imageSize;

        if ($origWidth * $origHeight > 25000000) {
            return false;
        }

        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
        $newWidth = (int) ($origWidth * $ratio);
        $newHeight = (int) ($origHeight * $ratio);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($source);
                break;
            case IMAGETYPE_WEBP:
                $sourceImage = imagecreatefromwebp($source);
                break;
            default:
                return false;
        }

        if (! $sourceImage) {
            return false;
        }

        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }

        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        imagedestroy($sourceImage);
        $sourceImage = null;

        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($thumbnail, $destination, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($thumbnail, $destination, 8);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($thumbnail, $destination);
                break;
            case IMAGETYPE_WEBP:
                $result = imagewebp($thumbnail, $destination, 85);
                break;
            default:
                $result = false;
        }

        imagedestroy($thumbnail);

        return $result ?? false;
    }
}
