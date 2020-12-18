export default {
	props: ['sortable', 'field', 'label', 'sortDirection', 'sorting', 'handleSort'],
	render () {
		const vnode = this.$scopedSlots.header({
			sorting: this.sorting,
			direction: this.sortDirection
		})[0];

		vnode.data = {
			on: {
				click: () => this.handleSort(this.field)
			}
		};

		return vnode;
	}
};
