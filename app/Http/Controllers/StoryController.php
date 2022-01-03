<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Authenticate;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    /**
     * Add a new story to a builiding
     *
     * When it has a Request object, we know its a post request.
     * All of the data from the post request will be contained in the $request object.
     */
    public function addNewStory (Request $request)
    {
        $request->validate([
           'title' => 'required|min:5|max:255',
           'story' => 'required|min:50',
           'date' => 'required|min:5',
           'osm_id' => 'required'
        ]);

        $title = $request->title;
        $story = $request->story;
        $date = $request->date;
        $buildingId = $request->osm_id;

        if (Auth::guest()) {
            return [
                'success' => false,
                'msg' => 'guest'
            ];
        }

        $user = Auth::user();

        Story::create([
            'user_id' => $user->id,
            'osm_id' => $buildingId,
            'story' => $story,
            'title' => $title,
            'date' => $date
        ]);

        return [
            'success' => true
        ];
    }

    /**
     * Check if a building has any stories associated with it
     */
    public function checkStory () {

        $osm_id = request('osm_id');

        $stories = Story::where('osm_id', $osm_id)->where('reviewed', 1)->get();

        return [
            'success' => true,
            'stories' => $stories
        ];
    }
}
