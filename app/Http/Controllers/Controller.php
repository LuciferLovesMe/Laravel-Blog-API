<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function storeImage($image, $path = 'public')
    {
        if(!$image){
            return null;
        }

        $filename = time().'.png';
        // Save image
        \Storage::disk($path)->put($filename, base64_decode($filename));

        // return the path
        return URL::to('/').'/storage/'.$path.'/'.$filename;
    }
}
