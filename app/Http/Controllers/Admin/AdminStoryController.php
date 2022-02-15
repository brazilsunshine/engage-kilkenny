<?php

namespace App\Http\Controllers\Admin;

use App\Models\Story;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminStoryController extends Controller
{
    /**
     * Update a story as reviewed.
     *
     * This will make the story appear on the map when a building is clicked
     *
     * If admin has changed the title, story or date, we update it here.
     */
    public function acceptStory (Request $request)
    {
        $story = Story::find($request->story['id']);

        $story->title = $request->story['title'];
        $story->story = $request->story['story'];
        $story->date = $request->story['date'];

        $story->reviewed = true;
        $story->save();

        return [
            'success' => true
        ];
    }

    /**
     * Permanently delete a story from the database9
     */
    public function deleteStory (Request $request)
    {
        $story = Story::find($request->story_id);

        $story->delete();

        return [
            'success' => true
        ];
    }

    /**
     * Get any stories that need to be reviewed
     */
    public function getStories ()
    {
        return Story::where('reviewed', false)->get();
    }
}
