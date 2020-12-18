<template>
<form>

	<section class="white-creamy-center flex-grid">

	<div class="flex-box">
		<Field name="title" label="Title">
			<template scope="{input, meta, label}">
				<StandardInput :placeholder="label" type="text" :input="input" class="input"></StandardInput>
			</template>
		</Field>

		<Field name="url" label="URL">
			<template scope="{input, meta, label}">
				<StandardInput :placeholder="label" type="text" :input="input" class="input"></StandardInput>
			</template>
		</Field>

		<Field name="catid" label="Category">
			<template scope="{input, meta, label}">
				<Multiselect
					:value="input.value && input.value.split(',').map(id => (categories || []).find(cat => cat.catidu == id))"
					@input="input.onInput($event.map(x => x.catidu).join())"
					:multiple="true"
					:options="categories || []"
					label="category"
					track-by="catidu"
					placeholder="Select a Category"
				>
				</Multiselect>
			</template>
		</Field>

		<Field name="image" label="Image (350px x 200px)">
			<template scope="{input, meta, label}">
				<FileChooser @fileChosen="handleUpload($event, input.onInput)"></FileChooser>
				<img v-if="input.value" width="350" height="200" :src="`https://res.cloudinary.com/jaketv/image/fetch/${input.value}`" />
			</template>
		</Field>

		<Field name="kicker" label="Kicker Line">
			<template scope="{input, meta, label}">
				<StandardInput :placeholder="label" type="text" :input="input" class="input"></StandardInput>
			</template>
		</Field>

		<Field name="source" label="Source">
			<template scope="{input, meta, label}">
				<StandardInput :placeholder="label" type="text" :input="input" class="input"></StandardInput>
			</template>
		</Field>

		<Field name="description" label="Description">
			<template scope="{input, meta, label}">
				<StandardInput :placeholder="label" type="textarea" :input="input" class="textarea"></StandardInput>
			</template>
		</Field>

		<Field name="publish" label="Publish Date">
			<template scope="{input, meta, label}">
				<StandardInput :placeholder="label" type="datetime-local" :input="input" class="input"></StandardInput>
			</template>
		</Field>

		<BulmaField>
			<template slot="input">
				<div>
					<button class="button is-success">
			      Save Post <i v-if="saving" class="fa fa-spin fa-spinner"></i>
			    </button>

					<button type="button" class="button is-light">
						Cancel
					</button>
				</div>
			</template>
		</BulmaField>
</div>

<div class="flex-box" style="width:150px;" v-if="formState.postid">

	<div class="field">
	  <p class="control">
	    <input class="input" type="text" placeholder="Create Date" disabled v-model="formState.cr_date">
	  </p>
	</div>

	<div class="field">
	  <p class="control">
			<input class="input" type="text" placeholder="Modified Date" disabled v-model="formState.modify_date">
	  </p>
	</div>
</div>

</section>

</form>
</template>
<script>
import axios from 'axios';
import Multiselect from 'vue-multiselect';

import { Field, StandardInput } from '../Form';
import BulmaField from '../BulmaField';

const FileChooser = {
	name: 'FileChooser',
	render (h) {
		return h('input', {
			attrs: {
				type: 'file',
				value: 'Choose a File'
			},
			on: {
				change: e => this.$emit('fileChosen', e.currentTarget.files[0])
			}
		});
	}
};

export default {
	name: 'PostForm',
	props: ['formState', 'saving'],
	asyncComputed: {
		categories () {
			return axios({
				method: 'GET',
				url: '/admin/api/categories'
			}).then(({ data }) => data);
		}
	},
	components: {
		BulmaField,
		StandardInput,
		FileChooser,
		Multiselect,
		Field: {
			functional: true,
			props: [...Field.props, 'label'],
			render (h, { props, data }) {
				const { label, ...fieldProps } = props;

				const slot = data.scopedSlots.default;

				const input = h(Field, {
					props: fieldProps,
					scopedSlots: {
						default: ctx => slot({ ...ctx, label })
					}
				});

				return h(BulmaField, {}, [
					h('template', { slot: 'label' }, label),
					h('template', { slot: 'input' }, [input]),
				]);
			}
		}
	},
	methods: {
		handleUpload (file, cb) {
			const data = new FormData();
			data.append('file', file);

			axios({
				method: 'POST',
				url: '/admin/api/images/upload',
				data
			}).then(({ data }) => cb(data));
		},
		startUpload (file, component) {
			//eslint-disable-next-line
			component.active = true;
		}
	}
};
</script>

<style scoped>
	.white-creamy-center{
	}
	.flex-box{
		padding: 10px;
	}
</style>
