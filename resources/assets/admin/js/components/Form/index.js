import { vueSet } from 'vue-deepset';
import get from 'lodash/get';
import any from 'lodash/fp/any';
import omit from 'lodash/omit';

import Field from './Field';
import FieldArray from './FieldArray';
import StandardInput from './StandardInput';

const notUndefined = value => value !== undefined;

function form (WrappedComponent, {
	validations = {},
	asyncValidation
}) {
	let props;

	if (WrappedComponent.props) {
		if (WrappedComponent.props.length) {
			props = WrappedComponent.props.reduce((acc, prop) => ({
				...acc,
				[prop]: {}
			}), {});
		}
		else {
			props = WrappedComponent.props;
		}
	}

	return {
		name: 'Form',
		props: {
			value: {
				type: Object,
				default: () => ({})
			},
			handleSubmit: {
				type: Function,
				default: Function.prototype
			},
			...omit((props || {}), ['formState', 'validating', 'valid'])
		},
		created () {
			const validators = {};

			for (const validation in validations) {
				validators[validation] =
					({ message = '', options }, field) => validations[validation](get(this.state, field) || '', this.state, options)
							? undefined
							: message;
			}

			this.validators = validators;
		},
		data () {
			return {
				state: this.value,
				fields: {}
			};
		},
		computed: {
			valid () {
				const hasNoValidations = validations => Object.keys(validations).length === 0;
				const allValid = validations =>
					Object.values(validations).filter(notUndefined).length === 0;

				return !!Object.values(this.fields)
					.reduce((valid, { validations }) =>
						(hasNoValidations(validations) || allValid(validations)) * valid, true);
			},
			validating () {
				return any(x => x.validating, Object.values(this.fields));
			}
		},
		provide () {
			return {
				formState: this.state,
				setFieldData: this.setFieldData,
				getArray: this.getArray,
				addItem: this.addItem,
				validate: this.validate,
				setTouched: this.setTouched,
				fieldMeta: this.fieldMeta,
				runAsyncValidate: this.runAsyncValidate
			};
		},
		methods: {
			setFieldData (field, value) {
				vueSet(this.state, field, value);
			},
			validate (field, validators = []) {
				const validationResult = validators.reduce((acc, [validator, message, options]) =>
					({ ...acc,
						[validator]: Object.values(acc).filter(notUndefined).length === 0
						? this.validators[validator]({ message, options }, field)
						: undefined
					})
				, {});

				vueSet(this.fields, `["${field}"].validations`, validationResult);
			},
			runAsyncValidate (field, value) {
				vueSet(this.fields, `["${field}"].validating`, true);

				asyncValidation.call(this.$parent, this.state, field, value, (result) => {
					vueSet(this.fields, `["${field}"].validating`, false);

					if (result) {
						vueSet(this.fields[field], 'validations', { ...(this.fields[field].validations || {}), ...result });
					}
				});
			},
			fieldMeta (field) {
				return this.fields[field];
			},
			setTouched (field) {
				vueSet(this.fields, `["${field}"].touched`, true);
			},
			getArray (field) {
				const array = get(this.state, field);

				if (array) return array;

				vueSet(this.state, field, []);

				return get(this.state, field);
			},
			addItem (field, item) {
				const array = this.getArray(field);
				array.push(item);
			},
			doSubmit (e) {
				e.preventDefault();

				Object.values(this.fields).forEach(field => vueSet(field, 'touched', true));

				const valid = new Promise((resolve) => {
					if (!this.validating) {
						resolve(this.valid);
					}
					else {
						const unwatch = this.$watch('validating', () => {
							unwatch();
							resolve(this.valid);
						});
					}
				});

				valid.then(valid => valid && this.handleSubmit(this.state));
			}
		},
		render (h) {
			return h(WrappedComponent, {
				props: {
					formState: this.state,
					validating: this.validating,
					valid: this.valid,
					...this.$props
				},
				nativeOn: {
					submit: this.doSubmit
				}
			});
		}
	};
}

export { Field, FieldArray, StandardInput };
export default form;
