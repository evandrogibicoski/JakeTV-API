<?php
namespace App\Services;

use App\Models\Posts;
use DB;

class PostService {
	public function updatePost($update) {
		$post = Posts::find($update['postid']);

		$post->fill($update);
		$post->modify_date = date('Y-m-d H:i:s');

		$post->save();

		return $post;
	}

	public function savePost($post) {
		return Posts::create($post);
	}

	public function getPost ($id) {
		return Posts::find($id);
	}

	public function deletePost ($id) {
		$post = Posts::find($id);

		$post->status = 2;

		$post->save();
	}

	public function getPosts($options) {
		$options = array_merge(['catID' => null, 'search' => null, 'page' => 0, 'bookmarked' => false, 'liked' => false, 'user' => null, 'limit' => 27, 'sort' => ['sort_order', 1, 'onlyPublished' => false]], $options);

		extract($options);

		$query = Posts::where(function ($query) use ($search, $catID, $user, $bookmarked, $liked, $sort, $onlyPublished) {
			$query->where('status', '=', 1);

			if ($onlyPublished) {
				$query->where('publish', '<=', DB::raw('NOW()'));
			}

			if ($bookmarked) {
				$query->whereRaw('FIND_IN_SET(?,isbookmarked)', [$user->userid]);

				return;
			}

			if ($liked) {
				$query->whereRaw('FIND_IN_SET(?,isliked)', [$user->userid]);

				return;
			}

			if ($catID) {
				$query->whereRaw('FIND_IN_SET(?,catid)', [$catID]);
			}

			if ($search) {
				$query->where(function ($query) use ($search) {
					$query->where('title', 'LIKE', '%'.addslashes($search).'%')
					->orWhere('description', 'LIKE', '%'.addslashes($search).'%')
					->orWhere('kicker', 'LIKE', '%'.addslashes($search).'%')
					->orWhere('source', 'LIKE', '%'.addslashes($search).'%');
				});
			}

			if ($user && !$catID) {
				if ($user->catid) {
					$cats = explode(',', $user->catid);

					foreach ($cats as $catID) {
						$query->whereRaw('FIND_IN_SET(?,catid)', [$catID]);
					}
				}
			}
		});

		if ($sort) {
			list($field, $direction) = $sort;

			$query->orderBy($field, $direction == 1 ? 'ASC' : 'DESC');
		}

		return $query->paginate($limit, ['*'], null, $page);
	}

	public function toggleBookmark ($id, $user) {
		$post = Posts::find($id);

		$bookmarks = $post->isbookmarked;

		if (!$bookmarks) {
			$bookmarks = [];
		}
		else {
			$bookmarks = explode(',', $bookmarks);
		}

		$key = array_search($user->userid, $bookmarks);

		if ($key === false) {
			array_push($bookmarks, $user->userid);
		}
		else {
			unset($bookmarks[$key]);
		}

		$post->isbookmarked = implode(',', $bookmarks);

		$post->save();

		return $post;
	}

	public function toggleLike ($id, $user) {
		$post = Posts::find($id);

		$likes = $post->isliked;

		if (!$likes) {
			$likes = [];
		}
		else {
			$likes = explode(',', $likes);
		}

		$key = array_search($user->userid, $likes);

		if ($key === false) {
			array_push($likes, $user->userid);
		}
		else {
			unset($likes[$key]);
		}

		$post->isliked = implode(',', $likes);

		$post->save();

		return $post;
	}

	public function updateSort ($id, $newIndex) {
		DB::transaction(function () use ($id, $newIndex) {
			Posts::where('postid', $id)->update(['sort_order' => $newIndex, 'modify_date' => DB::raw('NOW()')]);
			DB::update('update tbl_post p1 inner join
			(select @rank := @rank + 1 rank,  z.* from (
			select postid, sort_order from tbl_post where status = 1 order by sort_order asc, modify_date desc
			) z, (select @rank:=0)y) p2
			on p1.postid = p2.postid set p1.sort_order = rank - 1;');
		});
	}
}
