<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategoryService {
	public function getCategoriesWithPosts () {
		return DB::table('tbl_category')
		->select(['catidu', 'category'])
		->join('tbl_post', 'tbl_post.catid', '=', 'catidu')
		->where('publish', '<=', DB::raw('NOW()'))
		->where('tbl_post.status', 1)
		->where('tbl_category.status', 1)
		->groupBy(['catidu', 'category'])
		->get();
	}

	public function getAllCategories () {
		return DB::table('tbl_category')->where('status', 1)->get();
	}

	public function updateCategory ($update) {
		$category = Category::find($update['catid']);

		$category->category = $update['category'];
		$category->modify_date = date('Y-m-d H:i:s');

		$category->save();

		return $category;
	}

	public function createCategory ($category) {
		return Category::create([
			'category' => $category['category'],
			'catidu' => mt_rand(),
			'status' => 1,
			'cr_date' => date("Y-m-d H:i:s"),
			'modify_date' => date("Y-m-d H:i:s")
		]);
	}

	public function deleteCategory ($id) {
		$category = Category::find($id);

		$category->status = 2;

		$category->save();

		return $category;
	}
}
