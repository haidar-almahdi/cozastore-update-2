<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back to previous page
        return redirect()->back();
    }
}
