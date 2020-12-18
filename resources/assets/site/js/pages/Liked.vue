<template>
<div>

<PostsLoader :filters="{liked: true}" v-if="!isGuest">
	<template scope="{posts, loading, hasMorePages, loadMore, toggleLike, toggleBookmark}">
		<PostList
			:posts="posts"
			:loading="loading"
			:user="user"
			@toggleLike="toggleLike"
			@toggleBookmark="toggleBookmark"
		></PostList>

		<button v-if="hasMorePages && !loading" class="btn--load" @click="loadMore">Load More</button>
	</template>
</PostsLoader>
<LoginModal v-else :show="true" :requestClose="goBack">
</LoginModal>
</div>
</template>
<script>
import { withRouter } from 'vue-component-router';
import PostsLoader from '../containers/PostsLoader';
import PostList from '../components/PostList.vue';
import LoginModal from '../components/LoginModal';
import injectors from '../mixins/injectors';

export default withRouter({
	name: 'Liked',
	data () {
		return {
			categories: [],
			selectedCategory: null,
			search: null
		};
	},
	mixins: [injectors],
	components: { PostsLoader, PostList, LoginModal },
	methods: {
		goBack () {
			this.router.history.push('/');
		}
	}
});
</script>
<style>
</style>
