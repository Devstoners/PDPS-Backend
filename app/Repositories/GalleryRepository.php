<?php

namespace App\Repositories;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
class GalleryRepository
{
    public function addGallery(Request $request)
    {
        // Create gallery
        $gallery = new Gallery();
        $gallery->topic_en = $request->topicEn;
        $gallery->topic_si = $request->topicSi;
        $gallery->topic_ta = $request->topicTa;
        $gallery->save();

        foreach ($request->file() as $key => $file) {
            // Check if the current field is a file input
            if ($request->hasFile($key)) {
                // Generate a unique filename for the image
                $imageName = uniqid() . '_' . $key . '.' . $file->getClientOriginalExtension();

                // Store the image in the 'public' disk under the 'gallery_images' directory
                $file->storeAs('gallery_images', $imageName, 'public');

                // Save the image path to the database
                $galleryImage = new GalleryImage();
                $galleryImage->gallery_id = $gallery->id;
                $galleryImage->image_path = 'gallery_images/' . $imageName;
                $galleryImage->save();
            }
        }

        return response()->json(['message' => 'Gallery created successfully'], 201);
    }

    public function updateGallery($id, $request)
    {
        $existGallery = Gallery::findOrFail($id);

        // Update other fields along with the file paths
        $existGallery->update([
            'topic_en' => $request->input('topicEn'),
            'topic_si' => $request->input('topicSi'),
            'topic_ta' => $request->input('topicTa'),
        ]);

        return response(['message' => 'Gallery updated successfully.'], 200);
    }



    public function deleteGallery($id)
    {
        $gallery = Gallery::find($id);

        if ($gallery) {
            $images = GalleryImage::where('gallery_id',$id)->get();

            GalleryImage::where('gallery_id',$id)->delete();

            foreach ($images as $image){
                Storage::disk('public')->delete($image->image_path);
            }

            $gallery->delete();

            return response()->noContent(); // Send 204 upon successful delete
        }else {
            return response()->noContent()->setStatusCode(404); // Send 404 if gallery not found
        }

    }

}


