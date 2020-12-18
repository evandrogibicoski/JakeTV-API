export default {
	name: 'FieldArray',
	props: ['name'],
	inject: ['getArray', 'addItem'],
	computed: {
		array () {
			return this.getArray(this.name);
		},
		fields () {
			return this.array.map((_, index) => `${this.name}[${index}]`);
		}
	},
	render (h) {
		const $slot = this.$scopedSlots.default({
			addItem: item => this.addItem(this.name, item),
			fields: this.fields
		});

		return h('div', $slot);
	}
};
