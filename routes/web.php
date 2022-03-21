<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');

// Registration
Route::get('/signup', 'HomeController@index');

// Add a new story to a building
Route::post('/add-story', 'StoryController@addNewStory');

Route::post('/add-data', 'BuildingsController@addData');

// Check if a building has any stories
Route::get('/check-building', 'StoryController@checkStory');

// Monthly subscription
Route::post('subscribe', 'SubscribersController@create');

/* Stripe Webhooks - excluded from CSRF protection */
Route::post('/stripe/webhook', 'WebhookController@handleWebhook')->name('webhook');
//Route::post('/stripe/customer-created', 'StripeController@create');
//Route::post('/stripe/payment-success', 'StripeController@payment_success');

Route::get('map', 'HomeController@index');

Route::get('/global/buildings', 'GlobalMap\GlobalMapController@buildings');
Route::get('/global/art-data', 'GlobalMap\GlobalMapController@artData');

// Get all stories for all buildings
Route::get('/buildings/all-stories', 'StoryController@getAllStories');

/** Auth Routes */

// Get currently auth user when logged in
Route::get('/current-user', 'UsersController@getAuthUser');

// Upload page
//Route::get('submit', 'HomeController@index'); // old route
Route::get('upload', 'HomeController@index')->name('upload');

// Upload the image, extract lat long, reverse geocode to address
//Route::post('submit', 'PhotosController@store');

// Tag litter to an image
//Route::get('profile', 'HomeController@index');

// Get unverified paginated photos for tagging
//Route::get('photos', 'PhotosController@unverified');
//Route::post('/profile/upload-profile-photo', 'UsersController@uploadProfilePhoto');

// The user can add tags to image
//Route::post('/add-tags', 'PhotosController@addTags');

// The user can change Remaining bool of a photo in Profile
//Route::post('/profile/photos/remaining/{id}', 'PhotosController@remaining');

// The user can delete photos
//Route::post('/profile/photos/delete', 'PhotosController@deleteImage');

// Paginated array of the users photos (no filters)
//Route::get('/user/profile/photos/index', 'User\UserPhotoController@index');
//
//// Filtered paginated array of the users photos
//Route::get('/user/profile/photos/filter', 'User\UserPhotoController@filter');
//
//// Add Many Tags to Many Photos
//Route::post('/user/profile/photos/tags/create', 'User\UserPhotoController@create');
//
//// Delete selected photos
//Route::post('/user/profile/photos/delete', 'User\UserPhotoController@destroy');

///**
// * USER SETTINGS
// */
//Route::get('/settings', 'HomeController@index');
//Route::get('/settings/password', 'HomeController@index');
//Route::get('/settings/details', 'HomeController@index');
//Route::get('/settings/account', 'HomeController@index');
//Route::get('/settings/payments', 'HomeController@index');
//Route::get('/settings/privacy', 'HomeController@index');
//Route::get('/settings/littercoin', 'HomeController@index');
//Route::get('/settings/phone', 'HomeController@index');
//Route::get('/settings/presence', 'HomeController@index');
//Route::get('/settings/email', 'HomeController@index');
//Route::get('/settings/show-flag', 'HomeController@index');
//Route::get('/settings/teams', 'HomeController@index');

// Game settings @ SettingsController
// Toggle Presense of a piece of litter
// Route::post('/settings/settings', 'SettingsController@presense');

// Subscription settings @ SubscriptionsController
// Control Current Subscription
//Route::post('/settings/payments/cancel', 'SubscriptionsController@destroy');
//Route::post('/settings/payments/reactivate', 'SubscriptionsController@resume');

// User settings @ UsersController
// The user can update their name, username and/or email
//Route::post('/settings/details', 'UsersController@details');
//
//// Change password
//Route::patch('/settings/details/password', 'UsersController@changePassword');
//
//// The user can delete their profile, and all associated records.
//// todo - remove user id from redis
//Route::post('/settings/delete', 'UsersController@destroy');
//
//// The user can change their Security settings eg name, surname, username visiblity and toggle public profile
//Route::post('/settings/security', [
//    'uses' => 'UsersController@updateSecurity',
//    'as'   => 'profile.settings.security'
//]);
//
//// Update the users privacy eg toggle their anonmyity
//Route::post('/settings/privacy/update', 'UsersController@togglePrivacy');
//
//// Control Ethereum wallet and Littercoin
//Route::post('/settings/littercoin/update', 'BlockchainController@updateWallet');
//Route::post('/settings/littercoin/removewallet', 'BlockchainController@removeWallet');
//
//// Update users phone number
//Route::post('/settings/phone/submit', 'UsersController@phone');
//Route::post('/settings/phone/remove', 'UsersController@removePhone');
//
//// Change default litter presence value
//Route::post('/settings/toggle', 'UsersController@togglePresence');
//
//// Toggle Email Subscription
//Route::post('/settings/email/toggle', 'EmailSubController@toggleEmailSub');
//
//// Get list of available countries for flag options
//Route::get('/settings/flags/countries', 'SettingsController@getCountries');
//// Save Country Flag for top 10
//Route::post('/settings/save-flag', 'SettingsController@saveFlag');

// The users profile
//Route::get('/user/profile/index', 'User\ProfileController@index');
//Route::get('/user/profile/map', 'User\ProfileController@geojson');
//Route::get('/user/profile/download', 'User\ProfileController@download');
//
//// Unsubscribe via email (user not authenticated)
//Route::get('/emails/unsubscribe/{token}', 'EmailSubController@unsubEmail');
//Route::get('/unsubscribe/{token}', 'UsersController@unsubscribeEmail');

Route::get('/terms', function() {
    return view('pages.terms');
});

Route::get('/privacy', function() {
    return view('pages.privacy');
});

// Confirm Email Address, old and new
Route::get('register/confirm/{token}', 'Auth\RegisterController@confirmEmail');
// Route::get('a', function () {
//     $user = \App\Models\User\User::first();
//     return view('auth.emails.confirm', ['user' => $user]);
//  });
Route::get('confirm/email/{token}', 'Auth\RegisterController@confirmEmail')
    ->name('confirm-email-token');

// Logout
Route::get('logout', 'UsersController@logout');

// Register, Login
Auth::routes();

// Overwriting these auth blade views with Vue components
Route::get('/password/reset', 'HomeController@index')
    ->middleware('guest');
Route::get('/password/reset/{token}', 'HomeController@index')
    ->name('password.reset')
    ->middleware('guest');


/** PAYMENTS */
Route::get('/join/{plan?}', 'HomeController@index');

Route::get('plans', function () {
    return \App\Plan::all();
});

// Pay
Route::post('/join', 'SubscriptionsController@store');
Route::post('/change', 'SubscriptionsController@change');

// Route::get('/profile/awards', 'AwardsController@getAwards');



/**
 * ADMIN
 */
Route::group(['prefix' => '/admin'], function () {

    // Review stories
    Route::get('stories', 'HomeController@index');
    Route::get('review', 'HomeController@index');

    // Get stories to review
    Route::get('review/get-data', 'Admin\AdminReviewController@getData');
    Route::get('stories/get-stories', 'Admin\AdminStoryController@getStories');

    // Update a story as reviewed
    Route::post('stories/accept-story', 'Admin\AdminStoryController@acceptStory');
    Route::post('stories/delete-story', 'Admin\AdminStoryController@deleteStory');
});

