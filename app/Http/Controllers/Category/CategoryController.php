<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\TokenService;

class CategoryController extends Controller
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
            'userid' => 'required|max:11'
        ]);
    }


	/**
     * Get a response for an incoming getAllCategories request.
     *
     * @param  Request  $request (including $userid, $page)
     * @return json response.
     */
    public function getAllCategories(Request $request)
    {
    	$token = $request->accessToken;
    	$page = $request->page;
    	$userid = $request->userid;

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

    	

		/*Pagination Sneaker*/
		$pageLimit = 100;
		$numtotal = Category::where('status', 1)->count();
		$totalpage = ceil($numtotal / $pageLimit);

		$OffSet = $page * $pageLimit; // Here $Page variable is passing from app side like 0,1,2,3...etc
		/*Pagination Sneaker*/
		
		$alldata = array();
		$responsedata = array();
		

		try{
			$categories = Category::where('status' , 1)->orderBy('category', 'ASC')->skip($OffSet)->take($pageLimit)->get();
			$user = User::where('userid', $userid)->where('status', 1)->firstOrFail();

			foreach($categories as $category)
			{
				$subcatarray = array();
				reset($subcatarray);
				
				$alldata["catid"] = $category->catid;
				$alldata["catuniqueid"] = $category->catidu;
				$alldata["category"] = $category->category;
				$book = explode(',',$user->catid);
				if(in_array($category->catidu,$book))
				{
					$alldata["isselected"] = '1';
				}
				else
				{
					$alldata["isselected"] = '0';
				}
				$alldata["subcatdata"] = $subcatarray;
				$responsedata[] = $alldata;
			}
			$data = array(
	                'status' => 200,
	                'success' => true,
	                'message' => 'Category found.',
	                'totalpage' => $totalpage,
	                'data' => $responsedata,
	        );
		} 
		catch (ModelNotFoundException $e) 
		{
			$status = 400;
			$data = array(
	                'status' => 400,
	                'success' => false,
	                'message' => 'Category not found.'	                
	        );
		}
		
            
		return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }


    /**
     * Get a response for an incoming getSelectedCategories request.
     *
     * @param  Request  $request (including only $userid)
     * @return json response.
     */
    public function getSelectedCategories(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;

    	$status = 200;
    	$data = array();

    	if (!(TokenService::validateToken($token, $userid))) {
            $status = 401;
            $data = array(
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized'
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
    	$response = array();
    	$array_merge = array();
    	
    	try{
    		$user = User::where('userid', $userid)->firstOrFail();
    		if($user->catid != "")
			{
				$categories = Category::whereIn('catidu', explode(',', $user->catid))
						->where('status', 1)
						->select('catidu AS catid', 'category')
						->get();
				$subCategories = SubCategory::whereIn('subcatidu', explode(',', $user->catid))
						->where('status', 1)
						->select('subcatidu AS catid', 'subcategory AS category')
						->get();
				
				if(!empty($categories) && !empty($subCategories))
				{
					$array_merge = $categories->merge($subCategories);
				}
				else
				{
					if(!empty($categories))
					{
						$array_merge = $categories;	
					}
					else
					{
						$array_merge = $subCategories;		
					}
				}

				foreach($array_merge as $arraydata)
				{
					$alldata['catid'] = $arraydata->catid;
					$alldata['category'] = $arraydata->category;
					$response[] = $alldata;
				}
				
				$data = array(
		                'status' => 200,
		                'success' => true,
		                'message' => 'Selected Category Found',
		                'data' => $response
		        );
			}
			else
			{
				$status = 400;
				$data = array(
		                'status' => 400,
		                'success' => false,
		                'message' => 'Selected Category Not Found'	                
		        );	
			}
    	} catch(ModelNotFoundException $e) {
    		$status = 400;
			$data = array(
	                'status' => 400,
	                'success' => false,
	                'message' => 'Selected Category Not Found'	                
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
    protected function category_validator(Request $request)
    {
        return Validator::make($request->all(), [
            'userid' => 'required|max:11',
            'catid' => 'required'
        ]);
    }


    /**
     * Get a response for an incoming selectCategoryByUser request.
     *
     * @param  Request  $request (including $userid, $catid)
     * @return json response.
     */
    public function selectCategoryByUser(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$catid = $request->catid;

    	$status = 200;
    	$data = array();

    	if (!(TokenService::validateToken($token, $userid))) {
            $status = 401;
            $data = array(
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized'
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

    	$v = $this->category_validator($request);
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
    		$user = User::where('userid', $userid)->firstOrFail();
    		$newcatids = $user->catid;
    		if($newcatids != "")
			{
				if (!in_array($catid, explode(',', $newcatids)))
					$newcatids= $newcatids.','.$catid;
			}
			else
			{
				$newcatids = $catid;
			}
			$user->catid = $newcatids;
			if ($user->save()) {
				$data = array(
					'status' => 200,
					'success' => true,
					'message' => 'Category Select Successfully',
				);
			} else {
				$status = 400;
				$data = array(
					'status' => 400,
					'success' => true,
					'message' => 'Category Select Failed',
				);
			}

    	} catch(ModelNotFoundException $e) {
    		$status = 400;
			$data = array(
	                'status' => 400,
	                'success' => false,
	                'message' => 'Category Select Failed'	                
	        );
    	}
    	return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }



    /**
     * Get a response for an incoming unselectCategoryByUser request.
     *
     * @param  Request  $request (including $userid, $catid)
     * @return json response.
     */
    public function unselectCategoryByUser(Request $request)
    {
    	$token = $request->accessToken;
    	$userid = $request->userid;
    	$catid = $request->catid;

    	$status = 200;
    	$data = array();

    	if (!(TokenService::validateToken($token, $userid))) {
            $status = 401;
            $data = array(
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized'
            );
            return response(json_encode($data), $status, ['Content-Type', 'application/json']);
        }

    	$v = $this->category_validator($request);
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
    		$user = User::where('userid', $userid)->firstOrFail();
    		$newcatids = $user->catid;
    		if($newcatids != "")
			{
				$checkin = explode(',',$user->catid);
				$index = array_search($catid, $checkin);
				if($index !== false)
				{
					unset($checkin[$index]);
				}
				$newcatids = implode(',', $checkin);
				
				$user->catid = $newcatids;
				if ($user->save()) {
					$data = array(
						'status' => 200,
						'success' => true,
						'message' => 'Category Unselect Successfully',
					);
				} else {
					$status = 400;
					$data = array(
						'status' => 400,
						'success' => true,
						'message' => 'Category UnSelect Failed',
					);
				}	
			} else {
				$status = 201;
				$data = array(
					'status' => 201,
					'success' => true,
					'message' => 'Selected Categories Not Existed',
				);
			}
			

    	} catch(ModelNotFoundException $e) {
    		$status = 400;
			$data = array(
	                'status' => 400,
	                'success' => false,
	                'message' => 'Category Unselect Failed'	                
	        );
    	}
    	return response(json_encode($data), $status, ['Content-Type', 'application/json']);
    }

}
