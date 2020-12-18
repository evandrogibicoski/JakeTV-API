<template>
<div>
	<div class="form-row">
		<input type="password" v-model="password" placeholder="Password" />
	</div>
	<button class="button" @click="save">Change Password <i v-if="changingPassword" class="fa fa-spin fa-spinner"></i></button>    
</div>
</template>
<script>
import request from '../lib/request';

export default {
	data () {
		return {
			password: null,
			changingPassword: false
		};
	},
	methods: {
		save () {
			this.changingPassword = true;

			request('/changePassword', {
				method: 'POST',
				data: {
					password: this.password
				}
			}).then(() => { this.changingPassword = false; });
		}
	}
};
</script>