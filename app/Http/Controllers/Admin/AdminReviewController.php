<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Engage\BuildingsData;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Get array of buildingsData to review, along with the building it is associated with
     */
    public function getData ()
    {
        return BuildingsData::with('building')
            ->where('verified', false)
            ->get();
    }
}
