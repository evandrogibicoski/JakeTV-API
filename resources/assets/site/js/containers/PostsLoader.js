import axios from 'axios';
import request from '../lib/request';
import injectors from '../mixins/injectors';

export default {
	name: 'PostsLoader',
	props: ['filters'],
	mixins: [injectors],
	data () {
		return {
			currentPage: 1,
			posts: [],
			loading: true,
			hasMorePages: true
		};
	},
	mounted () {
		this.loadPosts(this.currentPage, this.filters);
	},
	watch: {
		currentPage () {
			this.loadPosts(this.currentPage, this.filters);
		},
		filters: {
			handler () {
				this.posts = [];
				this.loadPosts(1, this.filters);
			},
			deep: true
		}
	},
	methods: {
		loadPosts (page, { catid, search, bookmarked, liked }) {
			const existingToken = this.source;

			this.source = axios.CancelToken.source();

			if (this.loading && existingToken) {
				existingToken.cancel();
			}

			this.loading = true;

			request(`user/search?page=${page}&catid=${catid ? catid.value : ''}&search=${search || ''}&bookmarked=${bookmarked ? 1 : 0}&liked=${liked ? 1 : 0}`, {
				cancelToken: this.source.token
			}).then(({ data: { last_page: lastPage, data } }) => {
				this.posts.push(...data);
				this.hasMorePages = page < lastPage;
				this.loading = false;
			});
		},
		toggleBookmark (id) {
			if (this.isGuest) {
				this.$emit('notLoggedIn');
				return;
			}

			const post = this.posts.find(x => x.postid === id);

			const bookmarks = post.isbookmarked ? post.isbookmarked.split(',') : [];

			const index = bookmarks.indexOf(String(this.user.userid));

			if (index !== false) {
				bookmarks.splice(1, index);
			}
			else {
				bookmarks.push(this.user.userid);
			}

			post.isbookmarked = bookmarks.join(',');

			request(`toggleBookmark?id=${id}`, { method: 'POST' })
				.then(({ data: newPost }) => Object.assign(post, newPost));
		},
		toggleLike (id) {
			if (this.isGuest) {
				this.$emit('notLoggedIn');
				return;
			}

			const post = this.posts.find(x => x.postid === id);

			const likes = post.isliked ? post.isliked.split(',') : [];

			const index = likes.indexOf(String(this.user.userid));

			if (index !== false) {
				likes.splice(1, index);
			}
			else {
				likes.push(this.user.userid);
			}

			post.isliked = likes.join(',');

			request(`toggleLike?id=${id}`, { method: 'POST' })
				.then(({ data: newPost }) => Object.assign(post, newPost));
		}
	},
	render (h) {
		return h('div', this.$scopedSlots.default({
			posts: this.posts,
			hasMorePages: this.hasMorePages,
			loading: this.loading,
			loadMore: () => { this.currentPage += 1; },
			toggleBookmark: this.toggleBookmark,
			toggleLike: this.toggleLike
		}));
	}
};
