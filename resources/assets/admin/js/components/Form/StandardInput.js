export default {
	functional: true,
	props: ['input', 'type'],
	render (h, { data, props: { input, type } }) {
		const domProps = {
			value: input.value
		};

		if (type !== 'textarea') {
			domProps.type = type;
		}

		return h(type === 'textarea' ? 'textarea' : 'input', {
			...data,
			on: {
				input: input.onInput,
				blur: input.onBlur,
				focus: input.onFocus
			},
			props: {
				value: input.value,
				type
			},
			domProps
		});
	}
};
