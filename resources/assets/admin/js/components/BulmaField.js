export default {
	functional: true,
	render (h, { slots }) {
		const { input: [field], label } = slots();

		return h('div', { staticClass: 'field' }, [
			label ? h('label', { staticClass: 'label' }, label) : null,
			h('p', { staticClass: 'control' },
				[field]
			)
		]);
	}
};
