export default {
	props: ['initialTab'],
	data () {
		return {
			tabData: {
				activeTab: this.initialTab
			}
		};
	},
	provide () {
		return {
			tabs: {
				selectTab: this.selectTab,
				tabData: this.tabData
			}
		};
	},
	methods: {
		selectTab (tab) {
			this.tabData.activeTab = tab;
		}
	},
	render (h) {
		return h('div', [this.$slots.default]);
	}
};
