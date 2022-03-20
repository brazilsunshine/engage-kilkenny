<?php

namespace App\Http\Controllers;

use App\Models\Engage\BuildingsData;
use Illuminate\Http\Request;

class BuildingsController extends Controller
{
    public function addData (Request $request) {

        $request->validate([
            'osm_id' => 'required'
        ]);

        $buildingData = BuildingsData::create([
            'user_id' => auth()->user()->id,
            'osm_id' => $request->osm_id
        ]);

        if ($request->year) $buildingData->year = $request->year;

        if ($request->buildingsKey && $request->buildingsValue)
        {
            $buildingData->key = $request->buildingsKey;
            $buildingData->value = $request->buildingsValue;
        }

        $buildingData->save();

        return [
            'success' => true
        ];
    }
}
