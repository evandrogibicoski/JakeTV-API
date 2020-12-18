const injectorsObj = {};

// eslint-disable-next-line no-undef
const Laravel = window.Laravel || {};

Object.keys(Laravel).forEach((injector) => {
	injectorsObj[injector] = {
		default: () => Laravel[injector],
	};
});

export default {
	props: injectorsObj,
};
