<?php
namespace App\Http\Controllers;

class TempController extends Controller
{

    public function camera($id)
    {
        $url = 'http://giaothong.hochiminhcity.gov.vn/render/ImageHandler.ashx?id=' . $id . '&bg=white&w=500&t=' . time();
        $content = file_get_contents($url);
        return response($content)->header('Content-type','image/png');
    }

}
