<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class RefineFormDataSrv {

    public function Refine(Request $request)
    {
        $raw = $request->getContent();
        $delimiter = "multipart/form-data; boundary=";
        $boundary = "--".explode($delimiter, $request->headers->get("content-type"))[1];
        $elements = str_replace([$boundary, "Content-Disposition: form-data;"], "", $raw);
        $elementsTab = explode("\r\n\r\n", $elements);
        
        $data = [];
        for ($i=0; isset($elementsTab[$i]); $i+=2) {
            $key = str_replace(["\r\n", ' "', '"', ' name='], '', $elementsTab[$i]);
            if(strpos($key, "Content-Type:")){
                $data['avatar'] = $elementsTab[$i+1];
            } else {
                $val = str_replace(["\r\n","--"], '', $elementsTab[$i+1]);
                if ($key == 'avatar') {
                    $val = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,'], '', $elementsTab[$i+1]);
                }
                $data[$key] = $val;
            }
        }
        return $data;
    }
}