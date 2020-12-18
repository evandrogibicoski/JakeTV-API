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
use App\Services\AuthService;
use App\Services\PostService;
use App\Services\CategoryService;

Route::post('/login', function(Request $request, AuthService $service) {
   return $service->loginAdmin($request->email, $request->password);
});

Route::group(['prefix' => 'api'], function () {
  Route::group(['prefix' => 'posts'], function () {
    Route::get('/', function(Request $request, PostService $service){
        return $service->getPosts([
          'catID' => $request->catID,
          'search' => $request->search,
          'page' => $request->page,
          'bookmarked' => null,
          'liked' => null,
          'user' => null,
          'limit' => $request->limit,
          'sort' => $request->sort ? explode(',',$request->sort) : null,
          'onlyPublished' => false
        ]);
      });

      Route::get('/{id}', function(Request $request, PostService $service) {
        return $service->getPost($request->id);
      });

      Route::post('/', function (Request $request, PostService $service) {
        return $service->savePost($request->all());
      });

      Route::put('/{id}', function(Request $request, PostService $service) {
        return $service->updatePost($request->all());
      });

      Route::delete('/{id}', function(Request $request, PostService $service) {
        return $service->deletePost($request->id);
      });

      Route::post('/sort', function (Request $request, PostService $service) {
        $service->updateSort($request->id, $request->newIndex);
      });
  });

  Route::post('/images/upload', function(Request $request) {
    $path = $request->file->store('images');

    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . $path;
  });

  Route::group(['prefix' => 'categories'], function () {
    Route::get('/', function (CategoryService $service) {
      return $service->getAllCategories();
    });

    Route::post('/', function (Request $request, CategoryService $service) {
      return $service->createCategory($request->all());
    });

    Route::put('/', function(Request $request, CategoryService $service) {
      return $service->updateCategory($request->all());
    });

    Route::delete('/{id}', function(Request $request, CategoryService $service) {
      return $service->deleteCategory($request->id);
    });
  });
});

Route::get('/{vue_capture?}', function () {
    return view('admin');

})->where('vue_capture', '[\/\w\.-]*');
