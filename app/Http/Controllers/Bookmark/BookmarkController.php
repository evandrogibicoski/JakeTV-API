<?php

namespace App\Http\Controllers\Bookmark;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Posts;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\TokenService;

class BookmarkController extends Controller
{

 	/**
     * Get a validator for an incoming get bookmark request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function getbookmark_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'page' => ''
        ]);
    }


	/**
     * Get a response for an incoming get bookmark request.
     *
     * @param  Request  $request (including $userid, $page)
     * @return json response.
     */
    public function getBookmarkByUserId(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$page = $request->page;
    	
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

    	$v = $this->getbookmark_validator($request);
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

    	$alldata = array();
		$responsedata = array();

		try{
			$posts = Posts::where('status', 1)
						->where('publish', "<=", Carbon::now())
						->orderBy('cr_date', 'DESC')
						->get();
			
			foreach($posts as $post)
			{
				$checkin = explode(',', $post->isbookmarked);
				if(in_array($userid, $checkin))
				{
					$qry_cat = Category::where('catidu', $post->catid)->first();

					$qry_subcat = SubCategory::where('subcatidu', $post->subcatid)->first();
					
					$alldata["postid"] = $post->postid;
					$alldata["title"] = $post->title;
					$alldata["catuniqueid"] = $post->catid;
					if ($qry_cat == NULL) {
						$alldata["category"] = '';	
					} else {
						$alldata["category"] = $qry_cat->category;
					}	

					$alldata["subcatuniqueid"] = $post->subcatid;
					if ($qry_subcat == NULL) {
						$alldata["subcategory"] = '';
					} else {
						$alldata["subcategory"] = $qry_subcat->subcategory;
					}				
					
					$alldata["image"] = $post->image;
					$alldata["url"] = $post->url;
					$alldata["kickerline"] = $post->kicker;
					$alldata["source"] = $post->source;
					$alldata["description"] = $post->description;
					$alldata["totalpostlikes"] = $post->totalpostlikes;
					$book = explode(',', $post->isbookmarked);
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
					$alldata["sort_order"] = $post->sort_order;
					$alldata["cr_date"] = $post->cr_date;
					$responsedata[] = $alldata;
				}
			}
			if($responsedata != NULL)
			{
				$pageLimit = 25;
				$numtotal = count($responsedata);
				$totalpage = ceil($numtotal / $pageLimit);
				if ( $totalpage == 0 )
				{
					$totalpage = 1;	
				}
				
				$response = array();
				if($page == 0)
				{
					$offset = $page;
				}
				else
				{
					$offset = ($page * $pageLimit);	
				}
				
				for($i = 0; $i < $pageLimit; $i++)
				{
					if($i == 0)
					{
						$array_ind = $i + $offset; 
					}
					else
					{
						$array_ind = $i + $offset - 1; 
					}
					
					if(@$responsedata[$array_ind] != '')
					{
						if(empty($response))
						{
							$response[] = $responsedata[$array_ind];	
						}
						else if( !in_array($responsedata[$array_ind], $response) ) 
						{
							$response[] = $responsedata[$array_ind];
						}
					}
				}
				
				
				$data = array(
		                'status' => 200,
		                'success' => true,
		                'message' => 'Bookmark found.',
		                'totalpage'=>$totalpage,
		                'data'=>$response	                
		        );
				
			}
			else
			{
				$status = 400;
				$data = array(
		                'status' => 400,
		                'success' => false,
		                'message' => 'Bookmark not found.'	                
		        );
			}

		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
	                'status' => 401,
	                'success' => false,
	                'message' => 'Bookmark not found.'	                
	        );
		}
		
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }


    /**
     * Get a validator for an incoming set bookmark request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function setbookmark_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'postid' => 'required|max:11'                
        ]);
    }

    /**
     * Get a response for an incoming bookmark post request.
     *
     * @param  Request  $request (including $userid, $postid)
     * @return json response.
     */
    public function setBookmarkByUserId(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$postid = $request->postid;
    	
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

    	$v = $this->setbookmark_validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $vs->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

		try{
			$post = Posts::where('postid', $postid)->firstOrFail();

			$isbookmarked = '';
			if($post->isbookmarked != "")
			{
				$isbookmarked = $post->isbookmarked.','.$userid;				
			}
			else
			{
				$isbookmarked = $userid;				
			}
			$post->isbookmarked = $isbookmarked;

			if ($post->save()) {
				$data = array(
					'status' => 200, 
					'success' => true,
					'message' => 'Post bookmark successfully'
				);	
			} else {
				$status = 400;
				$data = array(
		                'status' => 400,
		                'success' => false,
		                'message' => 'Post bookmark failed.'	                
		        );
			}	
			
		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
	                'status' => 401,
	                'success' => false,
	                'message' => 'Post not Found.'	                
	        );
		}
		
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }


    /**
     * Get a response for an incoming unbookmark post request.
     *
     * @param  Request  $request (including $userid, $postid)
     * @return json response.
     */
    public function setUnBookmarkByUserId(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$postid = $request->postid;
    	
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

    	$v = $this->setbookmark_validator($request);
        if ($v->fails()) {
            $status = 400;
            $data = array(
                'status' => 400,
                'success' => false,
                'message' => 'Wrong params',
                'data' => $vs->errors()
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }
    	
		try{
			$post = Posts::where('postid', $postid)->firstOrFail();
			
			if($post->isbookmarked != "")
			{
				$arr_isbookmarked = explode(',', $post->isbookmarked);
				$index = array_search($userid, $arr_isbookmarked);
				if($index !== false)
				{
					unset($arr_isbookmarked[$index]);
					$isbookmarked = implode(',', $arr_isbookmarked);
					$post->isbookmarked = $isbookmarked;
					if ($post->save()) {
						$data = array(
							'status' => 200,
							'success' => true,
							'message' => 'Post unbookmark successfully'
						);	
					} else {
						$status = 400;
						$data = array(
				                'status' => 400,
				                'success' => false,
				                'message' => 'Post unbookmark failed.'	                
				        );
					}
				}
				else
				{
					$status = 201;
					$data = array(
			                'status' => 201,
			                'success' => true,
			                'message' => 'This post is already unbookmarked.'	                
			        );
				}
				
			} else {
				$status = 201;
				$data = array(
		                'status' => 201,
		                'success' => true,
		                'message' => 'This post is already unbookmarked.'	                
		        );
			}
			
		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
	                'status' => 401,
	                'success' => false,
	                'message' => 'Post not Found.'	                
	        );
		}
		
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }

}