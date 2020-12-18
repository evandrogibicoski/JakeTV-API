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
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Services\AuthService;

Route::post('/register', function(Request $request, AuthService $service) {
   return $service->register($request->fname, $request->lname, $request->email, $request->password);
});

Route::post('/login', function(Request $request, AuthService $service) {
   return $service->login($request->email, $request->password);
});

Route::get('/logout', function () {
  Auth::logout();
  return redirect('/');
});

Route::post('/forgotpassword', function(Request $request, AuthService $service) {
   return $service->forgotPassword($request->email);
});

Route::post('/updateProfile', function(Request $request, AuthService $service) {
  return $service->updateProfile($request->all());
});


Route::post('/changePassword', function(Request $request, AuthService $service) {
  return $service->changePassword($request->password);
});

// get all categories
Route::get('/getcategories', function(App\Services\CategoryService $categoryService) {
	return $categoryService->getCategoriesWithPosts();
});

Route::get('/user/categories', function(){
	return view('Category/getselectedcategory');
});

Route::get('/user/selectcategory', function(){
	return view('Category/selectcategorybyuser');
});

Route::get('/user/unselectcategory', function(){
	return view('Category/unselectcategorybyuser');
});

Route::get('/user/search', function(Request $request, PostService $service){
	$user = Auth::guard('users')->user();

  return $service->getPosts([
    'catID' => $request->catid,
    'search' => $request->search,
    'page' => $request->page,
    'bookmarked' => (int) $request->bookmarked,
    'liked' => (int) $request->liked,
    'user' => $user,
    'onlyPublished' => true
  ]);
});

Route::get('/user/post', function(){
	return view('Posts/getpost');
});

Route::post('/toggleBookmark', function(Request $request, PostService $service) {
	return $service->toggleBookmark($request->id, Auth::user());
});

Route::get('/user/postbycategory', function(){
	return view('Posts/getpostbycategory');
});

Route::post('/toggleLike', function(Request $request, PostService $service) {
	return $service->toggleLike($request->id, Auth::user());
});

Route::get('login/google', 'Auth\LoginController@redirectToProvider');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/jaketv/jaketv_webservice/service.php', function(Request $request) {
  return response()->json(json_decode(\Requests::request('http://old.jaketv.tv/jaketv/jaketv_webservice/service.php?'.$_SERVER['QUERY_STRING'], null, null, 'GET')->body));
});

Route::post('/jaketv/jaketv_webservice/service.php', function(Request $request) {
  $data = ['data'=>$request->data];

  return response()->json(json_decode(\Requests::post('http://old.jaketv.tv/jaketv/jaketv_webservice/service.php?'.$_SERVER['QUERY_STRING'], array(), $data)->body));
});

Route::post('/submit_bih', function (Request $request) {
  $entry = \App\Models\BihEntry::create($request->all());

  Mail::send('bih_submission', ['entry' => $entry], function($message) {
      $message->to('jaketv@gmail.com')->subject("New BIH Submission");
      $message->from('jaketvmanager@gmail.com','No Reply');
  });
});

Route::get('/auth_upload', function (Request $request) {
  $kSecret = 'AWS4RVF9aTyoaaj6xyMu6A3iQrYgGH2cYtP+ashvkUaR';
  
  $to_sign = $request->to_sign;

  $formattedDate = explode('T', $request->datetime)[0];

  $kDate = hash_hmac("sha256",  str_replace("\r\n", "\n", $formattedDate), $kSecret, true);

  $kRegion = hash_hmac("sha256", str_replace("\r\n", "\n", 'us-east-1'), $kDate, true);
  $kService = hash_hmac("sha256", str_replace("\r\n", "\n", 's3'), $kRegion, true);
  $kSigning = hash_hmac("sha256", str_replace("\r\n", "\n", "aws4_request"), $kService, true);
  $signature = hash_hmac("sha256", str_replace("\r\n", "\n", $to_sign), $kSigning);

  return $signature;
});

Route::get('/{vue_capture?}', function () {
    return view('site');

})->where('vue_capture', '[\/\w\.-]*');
