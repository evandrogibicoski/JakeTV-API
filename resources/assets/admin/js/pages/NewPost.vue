<template>
<div>
	<PostForm :handleSubmit="handleSubmit" :saving="saving" :value="initial"></PostForm>
</div>
</template>
<script>
import axios from 'axios';
import { withRouter } from 'vue-component-router';
import PostForm from '../components/PostForm';

export default withRouter({
	name: 'NewPost',
	data () {
		return {
			saving: false,
			initial: {
				publish: new Date().toISOString().replace(/:..\....Z/, '')
			}
		};
	},
	components: {
		PostForm
	},
	methods: {
		handleSubmit (post) {
			this.saving = true;

			axios({
				method: 'POST',
				url: '/admin/api/posts',
				data: post
			}).then(() => this.router.history.push('/admin/posts'));
		}
	}
});
</script>
