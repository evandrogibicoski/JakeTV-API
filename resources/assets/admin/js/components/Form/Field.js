import get from 'lodash/get';

const notUndefined = value => value !== undefined;

function eventOrValue (e) {
	return e instanceof window.Event
		? e.currentTarget.value
		: e;
}

export default {
	name: 'Field',
	props: ['name', 'validators', 'asyncValidate'],
	inject: ['setFieldData', 'formState', 'validate', 'setTouched', 'fieldMeta', 'runAsyncValidate'],
	computed: {
		value () {
			return get(this.formState, this.name);
		},
		meta () {
			return this.fieldMeta(this.name);
		},
		valid () {
			return Object.values(this.meta.validations).filter(notUndefined).length === 0;
		}
	},
	watch: {
		formState: {
			handler () {
				this.validate(this.name, this.validators);
			},
			immediate: true,
			deep: true
		}
	},
	render (h) {
		const setValue = (e) => {
			const value = eventOrValue(e);
			this.setFieldData(this.name, value);

			return value;
		};

		const vnodes = this.$scopedSlots.default({
			input: {
				onInput: (e) => {
					const value = setValue(e);
					this.$emit('input', value);
				},
				onBlur: (e) => {
					const value = setValue(e);
					this.setTouched(this.name);
					this.$emit('blur', value);

					if (this.asyncValidate) {
						this.runAsyncValidate(this.name, value);
					}
				},
				onFocus: () => {
					this.$emit('focus');
				},
				setTouched: () => this.setTouched(this.name),
				value: this.value,
				name: this.name
			},
			meta: { ...this.meta, valid: this.valid }
		});

		return vnodes.length > 1 ? h('div', vnodes) : vnodes[0];
	}
};
