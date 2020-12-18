import map from 'lodash/map';
import Vue from 'vue';

export default {
	props: ['data', 'sort'],
	data () {
		return {
			columns: {}
		};
	},
	methods: {
		handleSort (field) {
			let direction;

			if (this.sort[0] !== field) {
				direction = 1;
			}
			else {
				direction = this.sort[1] * -1;
			}

			this.$emit('sort', field, direction);
		}
	},
	render (h) {
		const columns = this.$slots.default.filter(x => x.componentOptions);

		const columnDefs = columns
			.reduce((colDefs, col) => ({
				...colDefs,
				[col.componentOptions.propsData.field]: {
					cell: col.data.scopedSlots.cell
				}
			}
		), {});

		for (const field in columnDefs) {
			if (!this.columns[field]) {
				Vue.set(this.columns, field, {
					sorting: false,
					sortDirection: null
				});
			}
		}

		for (const { componentOptions: { propsData = {} } } of columns) {
			propsData.handleSort = propsData.sortable
				? this.handleSort
				: Function.prototype;

			propsData.sort = this.sort[0] === propsData.field;
			propsData.sortDirection = this.sort[0] === propsData.field ? this.sort[1] : 0;
		}

		return h('table', [
			h('thead', columns),
			h('tbody',
				this.data.map(row =>
					h('tr', map(columnDefs, (col, field) => col.cell({ row, field })))
				)
			)
		]);
	}
};
