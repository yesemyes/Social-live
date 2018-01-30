<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if ( env( 'APP_ENV' ) === 'local' ) {
	URL::forceSchema( 'https' );
}

Auth::routes();

/* Default */
Route::get( '/', 'HomeController@index' );
Route::get( '/dashboard', 'HomeController@index' );
Route::get( '/networks', 'HomeController@network' );
Route::get( '/create-post', 'HomeController@createPost' );
Route::get( '/posts', 'HomeController@managePosts' );
Route::get( '/edit-post/{id}', 'HomeController@editPost' );
Route::get( '/edit-posted/{id}', 'HomeController@editPosted' );
Route::get( '/publish-post/{id}/{posted?}', 'HomeController@publishPost' );
Route::get( '/privacy', 'HomeController@policy' );
Route::get( '/settings', 'HomeController@settings' );
Route::post( '/createPostAction', 'HomeController@createPostAction' );
Route::post( '/image/delete/{id}', 'HomeController@deletePostImage' );
Route::post( '/editPostAction/{id}', 'HomeController@editPostAction' );
Route::post( '/publish-post', 'HomeController@publishPostsAction' );
Route::post( '/deletePost/{id?}', 'HomeController@deletePost' );
Route::post( '/account/delete/{id}', 'Auth\OauthController@destroy' );
Route::post('/account/update/{id}', 'HomeController@accountUpdate');
Route::post('/account/invite/{id}', 'HomeController@accountInvite');
//Route::get('check/email/{id}/{url}', 'Auth\OauthController@checkEmail');
//Route::post('/setPass/{id}', 'Auth\OauthController@setPass');

/* Networks */
Route::get( 'facebook/login/{wp?}', 'Auth\OauthController@loginWithFacebook' );
Route::get( 'google/login/{wp?}', 'Auth\OauthController@loginWithGoogle' );
Route::get( 'twitter/login/{wp?}', 'Auth\OauthController@loginWithTwitter' );
Route::get( 'linkedin/login/{wp?}', 'Auth\OauthController@loginWithLinkedin' );
Route::get( 'instagram/login/{wp?}', 'Auth\OauthController@loginWithInstagram' );
Route::get( 'reddit/login/{wp?}', 'Auth\OauthController@loginWithReddit' );
Route::get( 'pinterest/login/{wp?}', 'Auth\OauthController@loginWithPinterest' );

/* API */
Route::group( [ 'middleware' => 'api', 'prefix' => 'api' ], function() {
	Route::post( 'login', 'APIController@login' );
	Route::post( 'register', 'APIController@register' );

	Route::group( [ 'middleware' => 'jwt-auth' ], function() {
		Route::post( 'users', 'APIController@get_oauth_users' );
		Route::post( 'account/delete', 'APIController@destroy' );
		Route::post( 'twitter', 'SocialController@twitter' );
		Route::post( 'facebook', 'SocialController@facebook' );
		Route::post( 'linkedin', 'SocialController@linkedin' );
		Route::post( 'reddit', 'SocialController@reddit' );
		Route::post( 'instagram', 'SocialController@instagram' );
		Route::post( 'pinterest', 'SocialController@pinterest' );
		Route::post( 'get_boards', 'SocialController@get_boards' );
		Route::post( 'get_subreddits', 'SocialController@get_subreddits' );
	} );
} );

/* Storage check/set access */
Route::get( 'storage/app/{postImage}/{filename}', function( $postImage, $filename ) {
	$path = storage_path( 'app/' . $postImage . '/' . $filename );
	if ( ! File::exists( $path ) ) {
		abort( 404 );
	}
	$file     = File::get( $path );
	$type     = File::mimeType( $path );
	$response = Response::make( $file, 200 );
	$response->header( "Content-Type", $type );

	return $response;
} );