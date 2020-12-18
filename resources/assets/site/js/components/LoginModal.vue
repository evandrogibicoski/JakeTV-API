<template>

<Modal :show="show" :requestClose="requestClose">
	<Tabs initialTab="login">
		<ModalNavigation>
			<TabNav tabId="login">
				<template scope="{active, selectTab}">
					<a class="link__login-tab">Login</a>
				</template>
			</TabNav>
			<TabNav tabId="register">
				<template scope="{active, selectTab}">
					<a class="link__registration-tab">Registration</a>
				</template>
			</TabNav>
			<TabNav tabId="forgot">
				<template scope="{active, selectTab}">
					<a class="link__forgot-tab">Forgot Password</a>
				</template>
			</TabNav>
		</ModalNavigation>

		<TabPanel tabId="login">
			<TabContainer class="login-tab">
				<div class="form-row">
					<input type="email" v-model="emailLogin" placeholder="Email" />
				</div>
				<div class="form-row">
					<input type="password" v-model="passwordLogin" placeholder="Password" />
				</div>
				<span v-if="loginFailed">Email and Password Don't Match</span>
				<button class="button" @click="login">Sign In With JAKE TV <i v-if="loggingIn" class="fa fa-spin fa-spinner"></i></button>
				<a class="button" style="background-color:#f44336;" href="/login/google">Sign in with G+</a>
			</TabContainer>
		</TabPanel>
		<TabPanel tabId="register">
			<TabContainer class="registration-tab">
				<div class="form-row">
					<input type="text" v-model="registration.fname" placeholder="First Name" />
				</div>
				<div class="form-row">
					<input type="text" v-model="registration.lname" placeholder="Last Name" />
				</div>
				<div class="form-row">
					<input type="email" v-model="registration.email" placeholder="Email" />
				</div>
				<div class="form-row">
					<input type="password" v-model="registration.password" placeholder="Password" />
				</div>

				<span v-if="registrationError">{{registrationError}}</span>
				<button class="button" @click="register">Join <i class="fa fa-spinner fa-spin" v-if="registering"></i></button>
			</TabContainer>
		</TabPanel>
		<TabPanel tabId="forgot">
			<TabContainer class="forgot-tab">
				<div class="form-row">
					<input type="email" v-model="forgotPasswordEmail" placeholder="Email" />
				</div>
				<button class="button" @click="forgotPassword">Submit</button>
			</TabContainer>
		</TabPanel>
	</Tabs>

</Modal>
</template>
<script>
import request from '../lib/request';
import { Tabs, TabNav, TabPanel } from './Tabs';
import Modal from './Modal';
import injectors from '../mixins/injectors';
import ModalNavigation from './ModalNavigation';
import TabContainer from './TabContainer';

export default {
	name: 'LoginModal',
	props: ['show', 'requestClose'],
	mixins: [injectors],
	data () {
		return {
			forgotPasswordEmail: null,
			emailLogin: null,
			passwordLogin: null,
			loggingIn: false,
			loginFailed: false,
			registration: {
				fname: null,
				lname: null,
				email: null,
				password: null
			},
			registering: false,
			registrationError: null
		};
	},
	components: { Modal, Tabs, TabNav, TabPanel, TabContainer, ModalNavigation },
	methods: {
		register () {
			this.registrationError = null;
			this.registering = true;

			if (
				!this.registration.fname ||
				!this.registration.lname ||
				!this.registration.email ||
				!this.registration.password

			) {
				this.registrationError = 'All fields are required';
				this.registering = false;
				return;
			}

			request('register', {
				method: 'POST',
				data: this.registration
			}).then(({ data }) => {
				if (!data.success) {
					this.registrationError = data.message;
					this.registering = false;
				}
				else {
					window.location.reload();
				}
			});
		},
		login () {
			this.loggingIn = true;
			this.loginFailed = false;

			request('login', {
				method: 'POST',
				data: {
					email: this.emailLogin,
					password: this.passwordLogin
				}
			}).then((result) => {
				this.loggingIn = false;

				if (!result.data) {
					this.loginFailed = true;
				}
				else {
					window.location.reload();
				}
			});
		},
		forgotPassword () {
			window.alert('Request to reset your password has been sent');

			request('forgotpassword', {
				method: 'POST',
				data: {
					email: this.forgotPasswordEmail
				}
			});
		}
	}
};
</script>