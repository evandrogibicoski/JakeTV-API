<?php

namespace App\Http\Controllers\Posts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Posts;
use Illuminate\Support\Facades\Validator;
use App\Services\TokenService;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class PostController extends Controller
{

	/**
     * Get a validator for an incoming getsearch request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function getsearch_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'max:11',
            'page' => ''
        ]);
    }

	/**
     * Get a response for an incoming getSearch request.
     *
     * @param  Request  $request (including $userid, $page, $search)
     * @return json response.
     */
    public function getSearch(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$page = $request->page;
    	if ($page == NULL) $page = 0;
    	$search = $request->search;

       	$status = 200;
    	$data = array();

    	$v = $this->getsearch_validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $v->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }



		try{
			$alldata = array();
			$responsedata = array();

			$user = User::where('userid', $userid)->firstOrNew(['catid' => '']);
			$catgory_unique_ids = Category::where('selected', 1)->select('catidu')->get();

			foreach($catgory_unique_ids as $category_unique_id){
				$id[] =  $category_unique_id->catidu;
			}

			$imp_catid = implode(',', $id);

			if($user->catid != '' && !empty($id))
			{
				$catids = $user->catid.','.$imp_catid;
			}
			else
			{
				$catids = $imp_catid;
			}


			/*Pagination Sneaker*/

			$pageLimit = 25;

			$numtotal = Category::join('tbl_post', 'tbl_category.catidu', '=', 'tbl_post.catid')
						->select('tbl_post.*', 'tbl_category.catidu', 'tbl_category.category')
						->where('tbl_post.title', 'LIKE', '%'.addslashes($search).'%')
						->orWhere('tbl_post.description', 'LIKE', '%'.addslashes($search).'%')
						->orWhere('tbl_post.kicker', 'LIKE', '%'.addslashes($search).'%')
						->orWhere('tbl_post.source', 'LIKE', '%'.addslashes($search).'%')
						->orWhere('category', 'LIKE', '%'.addslashes($search).'%')
						->whereIn('tbl_post.catid', explode(',', $catids))
						->orderBy('tbl_post.sort_order', 'ASC')
						->count();

			$totalpage = $numtotal / $pageLimit;

			if($numtotal % $pageLimit != 0)
			{
				$totalpage = explode(".", $totalpage);
				$totalpage = $totalpage[0] + 1;
			}
			$offSet = $page * $pageLimit;

			$search_results = Category::join('tbl_post', 'tbl_category.catidu', '=', 'tbl_post.catid')
						->select('tbl_post.*', 'tbl_category.catidu', 'tbl_category.category')
						->where('tbl_post.title', 'LIKE', '%'.addslashes($search).'%')
						->orWhere('tbl_post.description', 'LIKE', '%'.addslashes($search).'%')
						->orWhere('category', 'LIKE', '%'.addslashes($search).'%')
						->whereIn('tbl_post.catid', explode(',', $catids))
						->orderBy('tbl_post.postid', 'DESC')
						->skip($offSet)->take($pageLimit)
						->get();


			if(!empty($search_results))
			{
				foreach($search_results as $search_result)
				{

					$alldata["postid"] = $search_result->postid;
					$alldata["title"] = $search_result->title;
					$alldata["catuniqueid"] = $search_result->catidu;
					$alldata["category"] = $search_result->category;
					$alldata["image"] = $search_result->image;
					$alldata["url"] = $search_result->url;
					$alldata["kickerline"] = $search_result->kicker;
					$alldata["source"] = $search_result->source;
					$alldata["description"] = $search_result->description;
					$alldata["totalpostlikes"] = $search_result->totalpostlikes;

					$book = explode(',', $search_result->isbookmarked);
					if(in_array($userid, $book))
					{
						$alldata['isbookmarked'] = '1';
					}
					else
					{
						$alldata['isbookmarked'] = '0';
					}
					$like = explode(',', $search_result['isliked']);
					if(in_array($userid, $like))
					{
						$alldata['isliked'] = '1';
					}
					else
					{
						$alldata['isliked'] = '0';
					}
					$alldata["sort_order"] = $search_result->sort_order;
					$alldata["cr_date"] = $search_result->cr_date;
					$responsedata[] = $alldata;
				}
			}

			if(!empty($responsedata))
			{
				$data = array(
	                'status' => 200,
	                'success' => true,
	                'message' => 'Post found.',
	                'totalpage' => $totalpage,
	                'data' => $responsedata
	        	);
			}
			else
			{
				$status = 400;
				$data = array(
	                'status' => 400,
	                'success' => false,
	                'message' => 'No Post found.',
	            );

			}

		} catch (ModelNotFoundException $e) {
			$status = 400;
			$data = array(
	                'status' => 400,
	                'success' => false,
	                'message' => 'Post not found.'
	        );
		}

		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }

    /**
     * Get a validator for an incoming getpost request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function getpost_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'page' => ''
        ]);
    }

    /**
     * Get a response for an incoming getPost request.
     *
     * @param  Request  $request (including $userid, $page)
     * @return json response.
     */
 	public function getPost(Request $request)
 	{
 		$token = $request->accessToken;
 		$userid = $request->userid;
 		$page = $request->page;
 		if ($page == NULL) $page = 0;

 		$status = 200;
 		$data = array();

 		if (!(TokenService::validateToken($token, $userid))) {
            $status = 401;
            $data = array(
                'status' => 401,
                'success' => false,
                'message' => 'Unathorized'
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

		$v = $this->getpost_validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $v->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }


		try {

			$alldata = array();
			$responsedata = array();

			$user = User::where('userid', $userid)
						->firstOrFail();
			$category_unique_ids = Category::where('selected', 1)
						->select('catidu')->get();

			foreach($category_unique_ids as $category_unique_id){
				$arr_unique_ids[] =  $category_unique_id->catidu;
			}

			$imp_catid = implode(',', $arr_unique_ids);

			if($user->catid != '' && !empty($arr_unique_ids))
			{
				$catids = $user->catid.','.$imp_catid;
			}
			else
			{
				$catids = $imp_catid;
			}

			$catids = explode(',', $catids);
			$catids = array_unique($catids);

			foreach($catids as $key=>$value){
				$cat[] = $value;
			}

			for($i=0; $i < count($cat); $i++)
			{
				$arr_unique_ids = $cat[$i];
				$posts = Posts::whereRaw('find_in_set(?, catid)', $arr_unique_ids)
						->where('status', 1)
						->where('publish', 1)
						->orderBy('postid', 'DESC')
						->get();
				//$qry021 = qry_fetchRows("SELECT * FROM `tbl_post` WHERE find_in_set($arr_unique_ids, catid) AND status='1' AND publish='1' order by postid desc");
				$arr_posts[] = $posts;
			}

			$newArrays = array();

			foreach($arr_posts as $arr_post){
				if(is_array($arr_post)){
					foreach($arr_post as $v2){
						$newArrays[] = $v2;
					}
				}
			}

			$post_id = array();
			foreach($newArrays as $newArray){
				$post_id[] = $newArray['postid'];
			}
			$post_id1 = array_unique($post_id);
			foreach($post_id1 as $k=>$v){
				if($v == "") { $post_id2 = array(); }
				else { $post_id2[] = $v; }
			}
			if(!empty($post_id2)){
				$arr_postids = implode(',', $post_id2);
			}else{
				$arr_postids = "";
			}

			if($arr_postids != ""){
				$pageLimit = 25;

				$numtotal = Posts::whereIn('postid', $arr_postids)->count();


				$totalpage = $numtotal / $pageLimit;
				if($numtotal % $pageLimit != 0)
				{
					$totalpage= explode(".", $totalpage);
					$totalpage= $totalpage[0] + 1;
				}
				$offSet = $page * $pageLimit;

				$posts = Posts::whereIn('postid', $arr_postids)
						->orderBy('sort_order', 'ASC')
						->skip($offSet)->take($pageLimit)
						->get();

				foreach($posts as $post)
				{

					$alldata["postid"] = $post["postid"];
					$alldata["title"] = $post["title"];

					$cat = explode(',', $post["catid"]);

                    $cat2 = array();
					for($i=0; $i<count($cat); $i++)
					{
						$cat1 = Category::where('catidu', $cat[$i])->firstOrFail();

                        array_push($cat2, $cat1);
					}

                    $cat_id = array();
                    $cat_name = array();
                    if(!empty($cat2))
                    {
						foreach($cat2 as $newArray21)
						{
                        	array_push($cat_id, $newArray21['catidu']);
                            array_push($cat_name, $newArray21['category']);
						}
					}

					$alldata["catid"] = implode(',', $cat_id);
					$alldata["category"] = implode(',', $cat_name);

					$alldata["image"] = $post["image"];
					$alldata["url"] = $post["url"];
					$alldata["kickerline"] = $post["kicker"];
					$alldata["source"] = $post["source"];
					$alldata["description"] = $post["description"];
					$alldata["totalpostlikes"] = $post["totalpostlikes"];

					$book = explode(',', $post['isbookmarked']);
					if(in_array($userid, $book))
					{
						$alldata['isbookmarked']='1';
					}
					else
					{
						$alldata['isbookmarked']='0';
					}
					$like = explode(',', $post['isliked']);

					if(in_array($userid, $like))
					{
						$alldata['isliked']='1';
					}
					else
					{
						$alldata['isliked']='0';
					}
					$alldata["cr_date"] = $post["cr_date"];
					$responsedata[] = $alldata;
				}

				$data = array(
					'status' => 200,
					'success' => true,
					'message' => 'Post Found',
					'totalpage' => $totalpage,
					'data' => $responsedata
				);


			}else{
				$status = 400;
				$data = array(
					'status' => 400,
					'success' => false,
					'message' => 'Post Not Found'
				);
			}

		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
				'status' => 401,
				'success' => false,
				'message' => 'Post Not Found'
			);
		}
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
 	}


 	/**
     * Get a validator for an incoming getpostbycategory request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function getpostbycategory_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'catid' => 'required',
            'page' => ''
        ]);
    }


 	/**
     * Get a response for an incoming getpostbycategory request.
     *
     * @param  Request  $request (including $userid, $page, $catid)
     * @return json response.
     */
 	public function getPostByCategory(Request $request)
 	{
 		$token = $request->accessToken;
 		$userid = $request->userid;
 		$page = $request->page;
 		$catid = $request->catid;
 		if ($page == NULL) {
 			$page = 0;
 		}

 		$data = array();
		$status = 200;

		if (!(TokenService::validateToken($token, $userid))) {
            $status = 401;
            $data = array(
                'status' => 401,
                'success' => false,
                'message' => 'Unathorized'
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

		$v = $this->getpostbycategory_validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $v->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }



		try {

			$alldata = array();
			$responsedata = array();

			$category_name = Category::where('catidu', $catid)
							->select('category')
							->firstOrFail();
			$resultdata = Posts::where('status', 1)
						->where('publish', '<=', Carbon::now())
						->whereRaw('find_in_set(?, catid)', $catid)
						->get();

			if(!empty($resultdata))
			{
				foreach( @$resultdata as $catdata)
				{
					@$maindata[] = @$catdata;
				}
				@$resultcat[] = $resultdata->count();
			}
			else
			{
				$maindata = array();
			}


			if(empty($maindata))
			{
				$data = array(
					'status' => 400,
					'success' => false,
					'message' => 'Post not found.'
				);
				return response(json_encode($data), $status, ['Content-Type', 'application/json']);
				exit;
			}
			else
			{
				$input = array_map("unserialize", array_unique(array_map("serialize", $maindata)));
			}


			if($input != NULL)
			{
				$pageLimit = 25;
				$numtotal = count($input);
				$totalpage = ceil($numtotal / $pageLimit);

				if($totalpage==0)
				{
					$totalpage = 1;
				}

				$response = array();

				$offset = ($page * $pageLimit);


				for($i = 0; $i < $pageLimit; $i++)
				{
					$array_index = $i + $offset;

					if ($array_index >= $numtotal) {
						break;
					}

					if($input[$array_index] != '')
					{
						if(empty($response))
						{

							$alldata["postid"] = $input[$array_index]->postid;
							$alldata["title"] = $input[$array_index]->title;
							$alldata["catuniqueid"] = $input[$array_index]->catid;
							$alldata["category"] = $category_name->category;
							$alldata["subcatuniqueid"] = $input[$array_index]->subcatid;
							//$alldata["subcategory"] = $subcategory['subcategory'];
							$alldata["image"] = $input[$array_index]->image;
							$alldata["url"] = $input[$array_index]->url;
							$alldata["kickerline"] = $input[$array_index]->kicker;
							$alldata["source"] = $input[$array_index]->source;
							$alldata["description"] = $input[$array_index]->description;
							$alldata["totalpostlikes"] = $input[$array_index]->totalpostlikes;
							$book = explode(',',$input[$array_index]->isbookmarked);
							if(in_array($userid,$book))
							{
								$alldata['isbookmarked'] = '1';
							}
							else
							{
								$alldata['isbookmarked'] = '0';
							}
							$like = explode(',', $input[$array_index]->isliked);
							if(in_array($userid, $like))
							{
								$alldata['isliked'] = '1';
							}
							else
							{
								$alldata['isliked'] = '0';
							}
							$alldata["cr_date"] = $input[$array_index]->cr_date;
							$responsedata[] = $alldata;
						}
						else if( !in_array($input[$array_index], $response) )
						{
							$category = Category::where('catidu', $input[$array_index]->catid)
										->select('category')->first();

							$subcategory = SubCategory::where('subcatidu', $input[$array_index]->subcatid)
										->select('subcategory')->first();

							$alldata["postid"] = $input[$array_index]->postid;
							$alldata["title"] = $input[$array_index]->title;
							$alldata["catuniqueid"] = $input[$array_index]->catid;
							$alldata["category"] = $category->category;
							$alldata["subcatuniqueid"] = $input[$array_index]->subcatid;
							$alldata["subcategory"] = $subcategory->subcategory;
							$alldata["image"] = $input[$array_index]->image;
							$alldata["url"] = $input[$array_index]->url;
							$alldata["kickerline"] = $input[$array_index]->kicker;
							$alldata["source"] = $input[$array_index]->source;
							$alldata["description"] = $input[$array_index]->description;
							$alldata["totalpostlikes"] = $input[$array_index]->totalpostlikes;
							$book = explode(',', $input[$array_index]->isbookmarked);
							if(in_array($userid, $book))
							{
								$alldata['isbookmarked'] = '1';
							}
							else
							{
								$alldata['isbookmarked'] = '0';
							}
							$like = explode(',', $input[$array_index]->isliked);
							if(in_array($userid, $like))
							{
								$alldata['isliked'] = '1';
							}
							else
							{
								$alldata['isliked'] = '0';
							}
							$alldata["cr_date"] = $input[$array_index]->cr_date;
							$responsedata[] = $alldata;
						}
					}
				}

				$outputdata = array_map("unserialize", array_unique(array_map("serialize", $responsedata)));
				foreach($outputdata as $finaldata)
				{
					$responces[] = $finaldata;
				}

				$data = array(
					'status' => 200,
					'success' => true,
					'message' => 'Post found',
					'totalpage' => $totalpage,
					'data' => $responces
				);
			}
			else
			{
				$status = 400;
				$data = array(
					'status' => 400,
					'success' => false,
					'message' => 'Post Not Found'
				);
			}


		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
				'status' => 401,
				'success' => false,
				'message' => 'Post Not Found'
			);
		}
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
 	}


}
