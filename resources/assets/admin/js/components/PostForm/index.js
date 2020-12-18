import form from '../Form';
import PostForm from './Form';

export default form(PostForm, {
	validations: {
		required: val => !!val
	}
});
