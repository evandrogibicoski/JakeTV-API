<template>
	<div>
		<form @submit.prevent="logIn">
			<div>
				<input v-model="email" type="text"/>
			</div>
			<div>
				<input v-model="password" type="password" />
			</div>
			<div>
				<button>Sign In <i class="fa fa-spin fa-spinner" v-if="loggingIn"></i></button>
			</div>
		</form>
	</div>
</template>
<script>
import axios from 'axios';

export default {
	name: 'Login',
	data () {
		return {
			email: null,
			password: null,
			loggingIn: false
		};
	},
	methods: {
		logIn () {
			this.loggingIn = true;

			axios({
				method: 'POST',
				url: '/admin/login',
				data: {
					email: this.email,
					password: this.password
				}
			}).then(({ data }) => this.$emit('loggedIn', data));
		}
	}
};
</script>
