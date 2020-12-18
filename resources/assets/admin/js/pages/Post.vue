<template>
  <div>
    <Spinner v-if="loading"></Spinner>

    <div class="filter-container">
    <div class="columns">
      <div class="column">
      <div class="field">
        <p class="control">
          <input @input="setSearch($event.currentTarget.value)" class="input" type="text" placeholder="Search">
        </p>
      </div>
    </div>
  <div class="column">

      <div class="field">
        <p class="control">
          <span class="select">
            <select @change="setCategory($event.currentTarget.value)">
              <option :value="null" v-if="categories.length === 0">(Loading Categories)</option>
              <template v-else>
                <option :value="null">Select a Category</option>
                <option v-for="category of categories" :value="category.catidu">{{category.category}}</option>
              </template>
            </select>
          </span>
        </p>
      </div>
</div>

<div class="column">
  <RouterLink to="/admin/posts/new" class="button is-info">New Post</RouterLink>

</div>

</div>
</div>




    <DataTable :data="posts" class="table is-narrow" @sort="sortPosts" :sort="sortField">
      <Column field="postid" :sortable="true">
				<template slot="header" scope="{sorting, direction}">
					<th>ID</th>
				</template>
				<template slot="cell" scope="{row, field}">
					<td><RouterLink :to="`/admin/posts/${row.postid}`">{{row[field]}}</RouterLink></td>
				</template>
      </Column>
      <Column field="image">
        <template slot="header" scope="{sorting, direction}">
          <th>Image</th>
        </template>
        <template slot="cell" scope="{row, field}">
          <td><img :src="row[field]" width="50" height="50"/></td>
        </template>
      </Column>
			<Column field="title" :sortable="true">
				<template slot="header" scope="{sorting, direction}">
					<th>Title</th>
				</template>
				<template slot="cell" scope="{row, field}">
					<td><RouterLink :to="`/admin/posts/${row.postid}`">{{strip(row[field])}}</RouterLink></td>
				</template>
      </Column>
			<Column field="url">
				<template slot="header" scope="{sorting, direction}">
					<th>URL</th>
				</template>
				<template slot="cell" scope="{row, field}">
					<td>{{row[field]}}</td>
				</template>
      </Column>

			<Column field="actions">
				<template slot="header" scope="{}">
					<th>Action</th>
				</template>
				<template slot="cell" scope="{row}">
					<td style="width:150px;">
            <p class="field">
              <a class="button">
								<RouterLink :to="`/admin/posts/${row.postid}`">
									<span class="icon is-small">
										<i class="fa fa-edit"></i>
									</span>
								</RouterLink>
              </a>
              <a class="button">
                <span class="icon is-small" @click="destroy(row)">
                  <i class="fa fa-trash"></i>
                </span>
              </a>
            </p>
					</td>
				</template>
			</Column>
			<Column field="publish">
				<template slot="header" scope="{}">
					<th>Publish</th>
				</template>
				<template slot="cell" scope="{row}">
					<td><a @click="publish(row)" class="button is-info">{{ new Date(`${row.publish}Z`) <= new Date() ? "Published" : "Publish" }}</a></td>
				</template>
			</Column>
    </DataTable>
    <nav class="pagination">

		<Paginate
			v-if="!loading"
		  :pageCount="totalPages"
		  :clickHandler="goToPage"
		  prevText="Prev"
		  nextText="Next"
			containerClass="pagination-list"
			pageClass="page"
		>
		</Paginate>
  </nav>

</div>
</template>
<script>
import axios from 'axios';
import Paginate from 'vuejs-paginate';
import { RouterLink } from 'vue-component-router';
import { strip } from 'slashes';
import Vue from 'vue';

import Spinner from '../components/Spinner.vue';
import { DataTable, Column } from '../components/Table';

export default {
	data () {
		return {
			posts: [],
			loading: false,
			totalPages: null,
			sortField: ['postid', 1],
			page: 1,
			search: null,
			category: null
		};
	},
	asyncComputed: {
		categories: {
			get () {
				return axios({
					method: 'GET',
					url: '/admin/api/categories'
				}).then(({ data }) => data);
			},
			default: () => []
		}
	},
	created () {
		this.loadPosts();
	},
	methods: {
		strip,
		goToPage (page) {
			this.page = page;
			this.loadPosts();
		},
		sortPosts (field, direction) {
			this.sortField = [field, direction];
			this.loadPosts();
		},
		setSearch (value) {
			this.search = value;
			this.page = 1;
			this.loadPosts();
		},
		setCategory (value) {
			this.category = value;
			this.page = 1;
			this.loadPosts();
		},
		loadPosts () {
			this.loading = true;

			const existingToken = this.source;

			this.source = axios.CancelToken.source();

			if (this.loading && existingToken) {
				existingToken.cancel();
			}

			axios({
				method: 'GET',
				url: '/admin/api/posts',
				params: {
					page: this.page,
					sort: this.sortField.join(),
					limit: 30,
					search: this.search || undefined,
					catID: this.category || undefined
				},
				cancelToken: this.source.token
			}).then(({ data: { data, last_page: totalPages } }) => {
				this.loading = false;
				this.totalPages = totalPages;
				this.posts = data;
			});
		},
		publish (post) {
			post.publish = new Date().toISOString().replace('Z', ''); // eslint-disable-line no-param-reassign

			axios({
				method: 'PUT',
				url: `/admin/api/posts/${post.postid}`,
				data: post
			});
		},
		destroy (row) {
			if (!window.confirm('Are you sure you want to delete this?')) return;

			Vue.delete(this.posts, this.posts.indexOf(row));

			axios({
				method: 'DELETE',
				url: `/admin/api/posts/${row.postid}`
			});
		}
	},
	components: {
		Paginate,
		Spinner,
		Column,
		DataTable,
		RouterLink
	}
};
</script>

<style scoped>
.filter-container{
  padding: 10px;
  background: #444;
}
.active a{
  background-color: #00d1b2;
  border-color: #00d1b2;
  color: #fff;
}
.page a{
  border-color: #dbdbdb;
  min-width: 2.25em;
}
</style>
