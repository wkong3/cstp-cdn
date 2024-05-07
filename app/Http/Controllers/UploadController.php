<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $error = false;

        if ($request->images) {
            $allowedFileExtension = ['jpeg', 'jpg', 'png'];

            foreach ($request->images as $key => $value) {
                if (preg_match('/^data:image\/(\w+);base64,/', $value['image'])) {
                    $data = substr($value['image'], strpos($value['image'], ',') + 1);
                    $extension = explode('/', mime_content_type($value['image']))[1];
                    $check = in_array($extension, $allowedFileExtension);
                    
                    if ($check) {
                        $image_name = md5(rand(1000, 10000));
                        $extension = strtolower($extension);
                        $image_full_name = $image_name . '.' . $extension;
                        $uploade_path = 'public/uploads/images/' . $request->module . '/' . $request->id . '/';
                        $image_url = $uploade_path . $image_full_name;

                        $storage_path = 'storage/uploads/images/' . $request->module . '/' . $request->id . '/';
                        $storage_url = $storage_path . $image_full_name;

                        $image[$key]['url'] = $storage_url;
                        $image[$key]['default'] = $value['default'] ?? 0;

                        $data = base64_decode($data);

                        // $data = $this->resize($data);

                        Storage::disk('local')->put($image_url, $data);
                    } else {
                        $error = true;
                        break;
                    }
                }else{
                    $image = [];
                }
            }
        } else {
            $error = true;
        }

        if ($error) {
            return response()->json([
                'error' => $error,
                'message' => 'Fail to upload',
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return response()->json([
            'error' => false,
            'data' => $image,
            'message' => 'Upload success',
        ], Response::HTTP_OK);
    }

    private function resize($file){
        return Image::make($file)->resize(500, 500, function($constraint){ $constraint->aspectRatio(); $constraint->upsize(); })->stream('jpg', 100);
    }
}
