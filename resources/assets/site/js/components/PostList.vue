<template>
<div class="articles">
<article class="article--item" v-for="post of posts" key="post.postid">
		<div class="image-container">
			<a :href="post.url" ><img :src="`https://res.cloudinary.com/jaketv/image/fetch/${post.image}`" /></a>
		</div>
		<div class="content">
			<span class="kicker" v-if="post.kicker">{{strip(post.kicker)}}</span>
			<h2><a :href="post.url">{{strip(post.title)}}</a></h2>
			<span v-if="post.source" class="source"> {{strip(post.source)}}</span>
			<p v-if="post.description" class="description">
				{{strip(post.description)}}
			</p>

			<div class="actions">
				<button @click="$emit('toggleLike', post.postid)" :class="{isliked: userHasLiked(user, post)}">
					<i class="fa fa-heart-o" aria-hidden="true"></i>
				</button>
			 <button @click="$emit('toggleBookmark', post.postid)" :class="{isbookmarked: userHasBookmarked(user, post)}">
				 <i class="fa fa-bookmark-o" aria-hidden="true"></i>
			 </button>

			 <button>
				 <a target="_blank" :href="`https://facebook.com/sharer.php?u=${post.url}&t=${post.title}`" >
				 <i class="fa fa-facebook" aria-hidden="true"></i>
			 </a>
			 </button>


			 <button>
				 <a target="_blank" :href="`https://twitter.com/share?url=${post.url}&via=JakeTVchannel&text=${post.description}`" >
				 	<i class="fa fa-twitter" aria-hidden="true"></i>
				 </a>
				</button>
		 </div>
	 </div>
</article>
<div v-if="loading" style="position: relative; height: 150px; width: 100%">
	<Spinner></Spinner>
</div>
</div>
</template>
<script>
import includes from 'lodash/includes';
import { strip } from 'slashes';

import Spinner from './Spinner';

export default {
	name: 'PostList',
	props: ['loading', 'posts', 'user'],
	components: { Spinner },
	methods: {
		userHasLiked (user, post) {
			return includes((post.isliked || '').split(','), String(user.userid));
		},
		userHasBookmarked (user, post) {
			return includes((post.isbookmarked || '').split(','), String(user.userid));
		},
		strip
	}
};
</script>
<style scoped>

.isbookmarked{
	color: #E91E63;
}

.isliked{
	color: #E91E63;
}
.articles{
  display: flex;
	flex-wrap: wrap;
}

.article--item{
 width: 31.33%;
 margin: 1%;
 margin-bottom: 40px;
}


.image-container{
		margin-bottom: 5px;
}

.image-container img{
		width: 100%;
}


.kicker{
		display: block;
		text-transform: uppercase;
		color: #555;
		font-size: 11px;
		font-weight: bold;
}

h2{
	color: #191919;
	font-size: 22px;
  font-weight: bold;
	line-height: 120%;
}

.source{
	color: #808080;
	font-weight: normal;
	font-size: 12px;
	line-height: 1.8rem;
	font-family: inherit;
	display: inline-block;
	padding: 2px 8px 2px 0;
	position: relative;
	white-space: nowrap;
}

.description{
	color: #444;
	font-weight: normal;
	font-size: 15px;
	line-height: 135%;
	margin: 0 0 10px;
}

.actions{
	margin-top: 10px;
	position: relative;
	left:-5px;
	color: #8e8e8e;
}

@media only screen and (max-width: 760px) {
	.articles{
	  display: flex;
		flex-direction: row;
		flex-wrap: wrap;
	}

	.article--item{
	 width: 100%;
	 display: flex;
	 border-bottom: 1px solid #dedede;
	 padding: 10px 0;
	}

	.image-container{
		margin-bottom: 5px;
		margin-right: 15px;
		width: 180px;
	}

}


@media only screen and (max-width: 480px) {
	.articles{
		margin: 0;
	}

	.article--item{
	 flex-direction: column;
	 border-bottom: 0 none;
	}

	.image-container{
		margin-bottom: 5px;
		width: 100%;
	}

}

</style>
