<template>
<div>
	<Spinner v-if="!post"></Spinner>
	<PostForm :handleSubmit="handleSubmit" :saving="saving" v-else v-model="post"></PostForm>
</div>
</template>
<script>
import axios from 'axios';

import PostForm from '../components/PostForm';
import Spinner from '../components/Spinner';

export default {
	name: 'SinglePage',
	props: ['id'],
	data () {
		return {
			saving: false
		};
	},
	asyncComputed: {
		post () {
			return axios({
				method: 'GET',
				url: `/admin/api/posts/${this.id}`
			}).then(({ data }) => ({ ...data, publish: new Date(data.publish).toISOString().replace(/:..\....Z/, '') }));
		}
	},
	components: {
		PostForm,
		Spinner
	},
	methods: {
		handleSubmit (post) {
			this.saving = true;

			axios({
				method: 'PUT',
				url: `/admin/api/posts/${post.postid}`,
				data: post
			}).then(({ data }) => {
				Object.assign(this.post, data);
				this.saving = false;
			});
		}
	}
};
</script>
