<template>
	<Spinner v-if="loading" />
	<div v-else>
		<Draggable v-model="posts" @change="saveSort" style="margin:20px 50px;">
			<div v-for="post of posts" class="item">
				{{post.title}}
			</div>
		</Draggable>
	</div>
</template>
<script>
import axios from 'axios';
import Draggable from 'vuedraggable';
import { strip } from 'slashes';

import Spinner from '../components/Spinner';

export default {
	data () {
		return {
			posts: [],
			loading: true
		};
	},
	created () {
		axios({
			method: 'GET',
			url: '/admin/api/posts',
			params: {
				sort: 'sort_order,1',
				limit: 5000
			}
		}).then(({ data: { data } }) => { this.posts = data; this.loading = false; });
	},
	components: {
		Spinner,
		Draggable
	},
	methods: {
		strip,
		saveSort ({ moved: { element, newIndex } }) {
			axios({
				method: 'POST',
				url: '/admin/api/posts/sort',
				data: { id: element.postid, newIndex },
				headers: {
					'content-type': 'application/json'
				}
			});
		}
	}
};
</script>
<style scoped>
.main{
	padding: 30px;
}
	.item{
		display: block;
		background: #dadada;
		font-size: 12px;
		border-radius: 4px;
		padding: 5px;
		margin-bottom: 5px;
		cursor: move;
	}
</style>
