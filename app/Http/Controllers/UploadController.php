<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    public function upload(Request $request)
    {
        $error = false;

        if ($request->property_image) {
            $allowedFileExtension = ['jpg', 'png'];

            foreach ($request->property_image as $key => $value) {
                if (preg_match('/^data:image\/(\w+);base64,/', $value['image'])) {
                    $data = substr($value['image'], strpos($value['image'], ',') + 1);
                    $extension = explode('/', mime_content_type($value['image']))[1];
                    $check = in_array($extension, $allowedFileExtension);

                    if ($check) {
                        $image_name = md5(rand(1000, 10000));
                        $extension = strtolower($extension);
                        $image_full_name = $image_name . '.' . $extension;
                        $uploade_path = 'uploads/images/' . $request->module . '/' . $request->id . '/';
                        $image_url = $uploade_path . $image_full_name;
                        $image[$key]['url'] = $image_url;
                        $image[$key]['default'] = $value['default'];

                        $data = base64_decode($data);
                        Storage::disk('local')->put($image_url, $data);
                    } else {
                        $error = true;
                        break;
                    }
                }
            }

            // foreach ($files as $file) {
            // $filename = $file->getClientOriginalName();
            // $extension = $file->getClientOriginalExtension();
            // $compressed = \Image::make($file)->encode('png', 90);
            // $upload_path = $request->module . '/' . $request->id . '/';
            // $file->store('image/' . $upload_path, 'public');
            // }
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
}
