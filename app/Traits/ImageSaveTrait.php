<?php


namespace App\Traits;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use File;
use Intervention\Image\Facades\Image;
use Vimeo\Vimeo;

trait ImageSaveTrait
{
    private function saveImage($destination, $attribute , $width, $height): string
    {
        if (!File::isDirectory(base_path().'/public/uploads/'.$destination)){
            File::makeDirectory(base_path().'/public/uploads/'.$destination, 0777, true, true);
        }

        if ($attribute->extension() == 'svg'){
            $file_name = time() . '-' . $attribute->getClientOriginalName();
            $path = 'uploads/'. $destination .'/' .$file_name;
            $attribute->move(public_path('uploads/' . $destination .'/'), $file_name);
            return $path;
        }

        $img = Image::make($attribute);
        if ($width != null && $height != null && is_int($width) && is_int($height)) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $returnPath = 'uploads/'. $destination .'/' . time().'-'. Str::random(10) . '.' . $attribute->extension();
        $savePath = base_path().'/public/'.$returnPath;
        $img->save($savePath);
        return $returnPath;
    }

    private function updateImage($destination, $new_attribute, $old_attribute , $width, $height): string
    {
        if (!File::isDirectory(base_path().'/public/uploads/'.$destination)){
            File::makeDirectory(base_path().'/public/uploads/'.$destination, 0777, true, true);
        }

        if ($new_attribute->extension() == 'svg'){
            $file_name = time() . '-' . $new_attribute->getClientOriginalName();
            $path = 'uploads/'. $destination .'/' .$file_name;
            $new_attribute->move(public_path('uploads/' . $destination .'/'), $file_name);
            File::delete($old_attribute);
            return $path;
        }

        $img = Image::make($new_attribute);
        if ($width != null && $height != null && is_int($width) && is_int($height)) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $returnPath = 'uploads/'. $destination .'/' . time().'-'. Str::random(10) . '.' . $new_attribute->extension();
        $savePath = base_path().'/public/'.$returnPath;
        $img->save($savePath);
        File::delete($old_attribute);
        return $returnPath;
    }

    private function uploadFile($destination, $attribute)
    {
        if (!File::isDirectory(base_path().'/public/uploads/'.$destination)){
            File::makeDirectory(base_path().'/public/uploads/'.$destination, 0777, true, true);
        }

        $file_name = time() . '-' . $attribute->getClientOriginalName();
        $path = 'uploads/'. $destination .'/' .$file_name;

        if (env('STORAGE_DRIVER') == 's3') {
            $path = Storage::disk('s3')->put($path, file_get_contents($attribute->getRealPath()));
        } else {
            $attribute->move(public_path('uploads/' . $destination .'/'), $file_name);
        }

        return $path;
    }

    private function uploadFileWithDetails($destination, $attribute)
    {
        if (!File::isDirectory(base_path().'/public/uploads/'.$destination)){
            File::makeDirectory(base_path().'/public/uploads/'.$destination, 0777, true, true);
        }

        $data['original_filename'] = $attribute->getClientOriginalName();
        $file_name = time() . '-' . $attribute->getClientOriginalName();
        $data['path'] = 'uploads/'. $destination .'/' .$file_name;

        if (env('STORAGE_DRIVER') == 's3') {
            Storage::disk('s3')->put($data['path'], file_get_contents($attribute->getRealPath()));
        } else {
            $attribute->move(public_path('uploads/' . $destination .'/'), $file_name);
        }

        return $data;
    }


    private function deleteFile($path)
    {
        File::delete($path);
    }

    private function deleteVideoFile($path)
    {
        if (env('STORAGE_DRIVER') == 's3')
        {
            Storage::disk('s3')->delete($path);
        } else {
            File::delete($path);
        }


    }

    private function deleteVimeoVideoFile($file)
    {
        $client = new Vimeo(env('VIMEO_CLIENT'), env('VIMEO_SECRET'),env('VIMEO_TOKEN_ACCESS'));
        $path = '/videos/' . $file;
        $client->request($path, [], 'DELETE');
    }

    private function uploadVimeoVideoFile($title, $file)
    {
        $client = new Vimeo(env('VIMEO_CLIENT'), env('VIMEO_SECRET'),env('VIMEO_TOKEN_ACCESS'));

        $uri = $client->upload($file, array(
            "name" => $title,
            "description" => "The description goes here."
        ));

        $response = $client->request($uri . '?fields=link');
        $response = $response['body']['link'];

        $str = $response;
        $vimeo_video_id = explode("https://vimeo.com/",$str);
        $path = null;
        if(count($vimeo_video_id))
        {
            $path = $vimeo_video_id[1];
        }

        return $path;

    }
}
