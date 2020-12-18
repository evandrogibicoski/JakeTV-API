<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/registration', 'Auth\RegisterController@index');
Route::get('/login', 'Auth\LoginController@login');
Route::get('/forgotpassword', 'Auth\ForgotPasswordController@index');

Route::get('/changepassword', 'Auth\ResetPasswordController@index');

Route::get('/getcategory', 'Category\CategoryController@getAllCategories');
Route::get('/user/categories', 'Category\CategoryController@getSelectedCategories');

Route::get('/user/selectcategory', 'Category\CategoryController@selectCategoryByUser');
Route::get('/user/unselectcategory', 'Category\CategoryController@unselectCategoryByUser');

Route::get('/user/search', 'Posts\PostController@getSearch');
Route::get('/user/post', 'Posts\PostController@getPost');

Route::get('/user/getbookmark', 'Bookmark\BookmarkController@getBookmarkByUserId');
Route::get('/user/bookmarkpost', 'Bookmark\BookmarkController@setBookmarkByUserId');
Route::get('/user/unbookmarkpost', 'Bookmark\BookmarkController@setUnBookmarkByUserId');

Route::get('/user/getlikepost', 'Posts\LikePostController@getLikePost');
Route::get('/user/postbycategory', 'Posts\PostController@getPostByCategory');

Route::get('/user/likepost', 'Posts\LikePostController@setLikePost');
Route::get('/user/unlikepost', 'Posts\LikePostController@setUnLikePost');