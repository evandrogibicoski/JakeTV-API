import set from 'lodash/set';

export default {
	props: ['tabId'],
	inject: ['tabs'],
	render (h) {
		const vnodes = this.$scopedSlots.default({
			active: this.tabs.tabData.activeTab === this.tabId
		});

		const vnode = vnodes.length > 1
			? h('div', [vnodes])
			: vnodes[0];

		set(vnode, 'data.on.click', () => this.tabs.selectTab(this.tabId));
		set(vnode, 'data.nativeOn.click', () => this.tabs.selectTab(this.tabId));

		return vnode;
	}
};
