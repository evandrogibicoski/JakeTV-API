<?php

namespace App\Http\Controllers\Posts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Posts;
use App\Services\TokenService;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class LikePostController extends Controller
{
	/**
     * Get a validator for an incoming request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'page' => ''
        ]);
    }

	/**
     * Get a response for an incoming get liked posts request.
     *
     * @param  Request  $request (including $userid, $page)
     * @return json response.
     */
    public function getLikePost(Request $request)
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

    	$v = $this->validator($request);
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
					->where('isliked', '!=', '')
					->where('publish', '<=', Carbon::now())
					->orderBy('cr_date', 'DESC')
					->get();

			foreach($posts as $post)
			{
				$checkin = explode(',', $post->isliked);
				$index = array_search($userid, $checkin);
				if($index !== false)
				{

					$qry_catetory = Category::where('catidu', $post->catid)->first();
					$qry_subcategory = SubCategory::where('subcatidu', $post->subcatid)->first();
					
					$alldata["postid"] = $post->postid;
					$alldata["title"] = $post->title;
					$alldata["catuniqueid"] = $post->catid;
					if ($qry_catetory) {
						$alldata["category"] = $qry_catetory->category;
					} else {
						$alldata["category"] = '';
					}
					
					$alldata["subcatuniqueid"] = $post->subcatid;
					if ($qry_subcategory) {
						$alldata["subcategory"] = $qry_subcategory->subcategory;	
					} else {
						$alldata["subcategory"] = '';
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
						$alldata['isbookmarked'] = '1';
					}
					else
					{
						$alldata['isbookmarked'] = '0';
					}

					$like = explode(',', $post->isliked);
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

				if($totalpage == 0)
				{
					$totalpage = 1;
				}
				
				$response = array();
				$offset = 0;

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
					$array_index = $i + $offset; 
					
					if ($array_index >= $numtotal) {
						break;
					}

					if($responsedata[$array_index] != '')
					{
						if(empty($response))
						{
							$response[] = $responsedata[$array_index];	
						}
						else if( !in_array($responsedata[$array_index], $response) ) 
						{
							$response[] = $responsedata[$array_index];
						}
					}
				}
				

				$data = array(
					'status' => 200,
					'success' => true,
					'message' => 'Like found',
					'totalpage' => $totalpage,
					'data' => $response
				);
			}
			else
			{
				$status = 400;
				$data = array(
		                'status' => 400,
		                'success' => false,
		                'message' => 'No Like found.'	                
		        );
			}

		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
	                'status' => 401,
	                'success' => false,
	                'message' => 'No Like found.'	                
	        );
		}
		
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }


    /**
     * Get a validator for an incoming request.
     *
     * @param  Request  $request
     * @return Illuminate\Support\Facades\Validator
     */
    protected function post_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'postid' => 'required|max:11'
        ]);
    }


    /**
     * Get a response for an incoming set likepost request.
     *
     * @param  Request  $request (including $userid, $postid)
     * @return json response.
     */
    public function setLikePost(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$postid = $request->postid;
    	
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


    	$v = $this->post_validator($request);
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
			$post = Posts::where('postid', $postid)->firstOrFail();

			$isliked = $post->isliked;
			if ($isliked != "")
			{
				$arr_isliked = explode(',', $isliked);
				if (!in_array($userid, $arr_isliked)) 
					$isliked = $isliked.",".$userid;
			}				
			else
				$isliked = $userid;
			$post->isliked = $isliked;
			if ($post->save()) 
			{
				$arr_isliked = explode(',', $isliked);
				$totallikes = count($arr_isliked);
				$post->totalpostlikes = $totallikes;
				$post->save();
				$data = array(
					'status' => 200,
					'success' => true,
					'message' => 'Post like successfully'
				);
			}
			else 
			{
				$status = 400;
				$data = array(
		                'status' => 400,
		                'success' => false,
		                'message' => 'Post Like failed.'	                
		        );
			}

		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
	                'status' => 401,
	                'success' => false,
	                'message' => 'Post Like failed.'	                
	        );
		}
		
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }


    /**
     * Get a response for an incoming set unlikepost request.
     *
     * @param  Request  $request (including $userid, $postid)
     * @return json response.
     */
    public function setUnLikePost(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$postid = $request->postid;
    	
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


    	$v = $this->post_validator($request);
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
			$post = Posts::where('postid', $postid)->firstOrFail();

			if($post->isliked != "")
			{
				$arr_isliked = explode(',',$post->isliked);
				$index = array_search($userid, $arr_isliked);
				if($index !== false)
				{
					unset($arr_isliked[$index]);
				}
				$totallikes = count($arr_isliked);
				$isliked=implode(',', $arr_isliked);
				$post->isliked = $isliked;
				$post->totalpostlikes = $totallikes;
				if ($post->save()) {
					$data = array(
						'status' => 200,
						'success'=>true,
						'message'=>'Post unlike successfully'
					);	
				} else {
					$status = 400;
					$data = array(
						'status' => 400,
						'success' => false,
						'message' => 'Post unlike failed'
					);
				}
				
			}
			else
			{
				$data = array(
					'status' => 200, 
					'success' => true,
					'message' => 'Aready UnLiked.'
				);
			}

		} catch (ModelNotFoundException $e) {
			$status = 401;
			$data = array(
	                'status' => 401,
	                'success' => false,
	                'message' => 'Post Not Found.'	                
	        );
		}
		
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }

}