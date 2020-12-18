<template>
<Modal :show="show" :requestClose="requestClose">
	<section class="flex-grid" style="padding: 20px">
		<div class="flex-box">
			<BulmaField>
				<template slot="label">Category</template>
				<template slot="input">
					<input type="text" class="input" v-model="category" />
				</template>
			</BulmaField>
			<BulmaField>
				<template slot="input">
					<div>
						<button @click="saveCategory" class="button is-success">
							Save Category
						</button>
					</div>
				</template>
			</BulmaField>
		</div>
	</section>
</Modal>
</template>
<script>
import Modal from './Modal';
import BulmaField from './BulmaField';

export default {
	props: ['value', 'show', 'requestClose'],
	data () {
		return {
			category: this.value
		};
	},
	components: { Modal, BulmaField },
	methods: {
		saveCategory () {
			this.$emit('input', this.category);
			this.requestClose();
		}
	}
};
</script>