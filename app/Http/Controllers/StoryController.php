<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function addNewStory (Request $request)
    {
        Story::create([
            'osm_id' => 1,
            'story' => $request->story
        ]);

        return [
            'success' => true
        ];
    }
}
