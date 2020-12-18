<template>
<div>
<LoginModal :show="loginModalShowing" :requestClose="hideLoginModal">
</LoginModal>
<PostsLoader :filters="{catid: selectedCategory, search: search}" @notLoggedIn="showLoginModal">
	<template scope="{posts, loading, hasMorePages, loadMore, toggleLike, toggleBookmark}">
		<div class="flex-grid">
			<div class="search-container form-row">
				<input v-model="search" class="search--input" type="text" placeholder="Search" />
			</div>
			<div class="categories-container form-row">
				<VueSelect
					:options="categories"
					:multiple="false"
					placeholder="Select a Category"
					v-model="selectedCategory"
				></VueSelect>
			</div>
		</div>

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
</div>
</template>
<script>
import VueSelect from 'vue-select';
import includes from 'lodash/includes';
import request from '../lib/request';
import PostsLoader from '../containers/PostsLoader';
import PostList from '../components/PostList.vue';
import LoginModal from '../components/LoginModal';
import injectors from '../mixins/injectors';

export default {
	name: 'HomePage',
	data () {
		return {
			categories: [],
			selectedCategory: null,
			search: null,
			loginModalShowing: false
		};
	},
	mixins: [injectors],
	created () {
		const myCategories = this.isGuest ? [] : this.user.catid.split(',');

		request('getcategories').then(
			({ data: categories }) => {
				this.categories = categories
				.filter(cat => this.isGuest || includes(myCategories, cat.catidu))
				.map(({ catidu, category }) => ({
					value: catidu,
					label: category
				}));

				if (!this.isGuest) {
					const otherCats = categories
					.filter(cat => !includes(myCategories, cat.catidu));

					this.categories.push(...otherCats.map(({ catidu, category }) => ({
						value: catidu,
						label: category
					})));
				}
			}
		);
	},
	methods: {
		showLoginModal () {
			this.loginModalShowing = true;
		},
		hideLoginModal () {
			this.loginModalShowing = false;
		},
	},
	components: { PostsLoader, PostList, VueSelect, LoginModal }
};
</script>
<style>

.search-container{
	flex: 3;
	margin-right: 90px;
}

.categories-container{
		flex: 1;
}
</style>
