export default {
	props: ['tabId'],
	inject: ['tabs'],
	render (h) {
		if (this.tabId !== this.tabs.tabData.activeTab) {
			return null;
		}

		const vnodes = this.$slots.default;

		const vnode = vnodes.length > 1
			? h('div', [vnodes])
			: vnodes[0];

		return vnode;
	}
};
