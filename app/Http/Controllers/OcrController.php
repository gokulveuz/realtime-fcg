<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrController extends Controller
{

    public function index()
    {

        return view('ocr');
    }

    public function imageExtraction(Request $request)
    {

        if (isset($request->image)) {
            $text =  (new TesseractOCR($request->image))->run();
            $phoneRegexes = [
                '/\+\d{1,3}\s*\(\d{3}\)\s*\d{3}-\d{4}/', // +91 (999) 999-9999
                '/\+\d{1,3}\s*\d{3}-\d{3}-\d{4}/', // +91 999-999-9999
                '/\(\d{3}\)\s*\d{3}-\d{4}/', // (999) 999-9999
                '/\d{3}-\d{3}-\d{4}/', // 999-999-9999
                '/\d{3}\s*\d{2}\s*\d{2}\s*\d{2}\s*\d{5}/', // 334 34 34 34 34443
                '/\d{10}/', // 84324422323
                '/\+\d{11}/'
            ];

            $phoneNumbers = [];

            foreach ($phoneRegexes as $regex) {
                preg_match_all($regex, $text, $matches);
                $phoneNumbers = array_merge($phoneNumbers, $matches[0] ?? []);
            }
            $emailRegex = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
            $nameRegex = '/[A-Z]{2,} [A-Z]{2,}/';
            $websiteRegex = '/\b(?:https?:\/\/)?(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b/';

            preg_match($emailRegex, $text, $emails);
            preg_match($nameRegex, $text, $names);
            preg_match($websiteRegex, $text, $websites);

            $email = $emails ?? null;
            $name = $names ?? null;
            $website = $websites ?? null;

        }


        return redirect('/image-reader')->with('data', [
            'email' => $email ?? null,
            'name' => $name ?? null,
            'website' => $website ?? null,
            'phone_number' => $phoneNumbers ?? null,
        ]);


    }
}
