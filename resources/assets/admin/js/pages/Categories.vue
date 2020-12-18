<template>
  <div>
    <Spinner v-if="loading"></Spinner>

    <div class="filter-container">
    <div class="columns">
      <div class="column">
      <div class="field">
        <p class="control">
          <input v-model="search" class="input" type="text" placeholder="Search">
        </p>
      </div>
    </div>
  <div class="column">

</div>

<div class="column">
  <a @click="showCategoryModal('new')" class="button is-info">New Category</a>
	<CategoryModal v-if="showingCategoryModal.new"
		:value="null"
		@input="createCategory"
		:show="true"
		:requestClose="() => closeCategoryModal('new')"
	/>

</div>

</div>
</div>
			<DataTable :data="filteredCategories" :sort="sort" @sort="(field, direction) => { sort = [field, direction]; }" class="table">
				<Column field="category" :sortable="true">
					<template slot="header" scope="{sorting, direction}">
						<th>Category</th>
					</template>
					<template slot="cell" scope="{row, field}">
						<td>{{row[field]}}</td>
					</template>
				</Column>
				<Column field="cr_date" :sortable="true">
					<template slot="header" scope="{sorting, direction}">
						<th>Created Date</th>
					</template>
					<template slot="cell" scope="{row, field}">
						<td>{{row[field]}}</td>
					</template>
				</Column>
				<Column field="modify_date" :sortable="true">
					<template slot="header" scope="{sorting, direction}">
						<th>Modified date</th>
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
							<a class="button"  @click="showCategoryModal(row.catid)">
								<span class="icon is-small">
									<i class="fa fa-edit"></i>
									<CategoryModal
										:value="row.category"
										@input="(cat) => updateCategory(row, cat)"
										:show="showingCategoryModal[row.catid]"
										:requestClose="() => closeCategoryModal(row.catid)"
									/>
								</span>
							</a>
							<a class="button" @click="deleteCategory(row)">
								<span class="icon is-small">
									<i class="fa fa-trash"></i>
								</span>
							</a>
						</p>
					</td>
				</template>
			</Column>
			</DataTable>
</div>
</template>
<script>
import axios from 'axios';
import Vue from 'vue';
import orderBy from 'lodash/orderBy';

import { DataTable, Column } from '../components/Table';
import Spinner from '../components/Spinner';
import CategoryModal from '../components/CategoryModal';

export default {
	data () {
		return {
			categories: [],
			loading: true,
			showingCategoryModal: {
				new: false
			},
			sort: ['category', 1],
			search: null
		};
	},
	computed: {
		filteredCategories () {
			return orderBy(this.categories, [this.sort[0]], this.sort[1] === 1 ? 'asc' : 'desc')
				.filter(({ category }) => this.search ? category.match(new RegExp(this.search, 'i')) : true);
		}
	},
	created () {
		axios({
			method: 'GET',
			url: '/admin/api/categories'
		}).then(({ data }) => { this.loading = false; this.categories = data; });
	},
	methods: {
		showCategoryModal (id) {
			this.$set(this.showingCategoryModal, String(id), true);
		},
		closeCategoryModal (id) {
			this.$set(this.showingCategoryModal, id, false);
		},
		createCategory (cat) {
			const date = new Date()
				.toISOString()
				.replace(/\....Z/, '')
				.split('T')
				.join(' ');

			const category = {
				category: cat,
				cr_date: date,
				modify_date: date
			};

			this.categories.push(category);

			axios({
				method: 'POST',
				url: '/admin/api/categories',
				data: category
			}).then(({ data }) => Object.assign(category, data));
		},
		updateCategory (row, cat) {
			row.category = cat;  // eslint-disable-line no-param-reassign

			axios({
				method: 'PUT',
				url: '/admin/api/categories',
				data: row
			});
		},
		deleteCategory (row) {
			Vue.delete(this.categories, this.categories.indexOf(row));

			axios({
				method: 'DELETE',
				url: `/admin/api/categories/${row.catid}`
			});
		}
	},
	components: {
		DataTable,
		Column,
		Spinner,
		CategoryModal
	}
};
</script>
