<template>
	<div>
		<div class="form-row">
			<input type="text" v-model="firstName" placeholder="First Name" />
		</div>
		<div class="form-row">
			<input type="text" v-model="lastName" placeholder="Last Name" />
		</div>
		<div class="form-row">
			<input v-model="email" type="email" placeholder="Email" />
		</div>
		<button class="button" @click="save">Save Profile <i v-if="savingProfile" class="fa fa-spin fa-spinner"></i></button>
	</div>
</template>
<script>
import request from '../lib/request';

export default {
	props: ['user'],
	data () {
		return {
			savingProfile: false,
			firstName: this.user.fname,
			lastName: this.user.lname,
			email: this.user.email
		};
	},
	methods: {
		save () {
			this.savingProfile = true;

			const profile = {
				fname: this.firstName,
				lname: this.lastName,
				email: this.email
			};

			request('/updateProfile', {
				method: 'POST',
				data: profile
			}).then(() => {
				Laravel.updateProfile(profile);
				this.savingProfile = false;
			});
		}
	}
};
</script>