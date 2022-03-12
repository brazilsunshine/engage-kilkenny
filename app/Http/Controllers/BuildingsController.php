<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuildingsController extends Controller
{
    public function addData (Request $request) {

        \Log::info($request->all());

    }
}
