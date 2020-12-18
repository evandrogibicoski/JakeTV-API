<template>
<div>
<header class="site-header">
	<div class="inner">
	<nav class="main-nav">
		<RouterLink class="nav--item__home" to="/">Home</RouterLink>
		<RouterLink class="nav--item__likes" to="/likes">Likes</RouterLink>
		<RouterLink class="nav--item__bookmarks" to="/bookmarks">Bookmarks</RouterLink>
	</nav>

	<a class="logo"><img src="https://res.cloudinary.com/jaketv/image/upload/v1492525420/logo/jakelogo.png" /></a>

	<nav class="secondary-nav">
		<a class="btn--app" href="https://play.google.com/store/apps/details?id=com.jaketv.jaketvapp"><i class="fa fa-android" aria-hidden="true"></i> Android App</a>
		<a class="btn--app" href="https://itunes.apple.com/us/app/jake-tv/id1063239686?ls=1&mt=8" ><i class="fa fa-apple" aria-hidden="true"></i> IOS App</a>
		<a v-if="isGuest" href="#" @click.prevent="showLoginModal" class="btn-signup">Login / Signup</a>
		<span v-else style="position:relative; top:2px;">
			<a href="#" @click.prevent="showProfileModal" style="text-decoration:underline;">{{user.fname}}!</a> (<a class="logout" href="/logout">Logout</a>)
		</span>
		<LoginModal :show="shouldShowLoginModal" :requestClose="hideLoginModal"></LoginModal>
		<Modal :show="shouldShowProfileModal" :requestClose="hideProfileModal">
			<Tabs initialTab="profile">
				<ModalNavigation>
					<TabNav tabId="profile">
						<template scope="{active}">
							<a class="link__login-tab">Profile</a>
						</template>
					</TabNav>
					<TabNav tabId="password">
						<template scope="{active}">
							<a class="link__registration-tab">Change Password</a>
						</template>
					</TabNav>
				</ModalNavigation>


				<TabPanel tabId="profile">
					<TabContainer>
						<ProfileForm :user="user"></ProfileForm>
					</TabContainer>
				</TabPanel>
				<TabPanel tabId="password">
					<TabContainer>
						<ChangePasswordForm></ChangePasswordForm>
					</TabContainer>
				</TabPanel>
			</Tabs>
		</Modal>

		<Modal :show="showingNewsletterModal" :requestClose="hideNewsletterModal">
			<div class="newsletter-modal" style="padding:20px;">
				<h2 class="email-h2">Jake Home Delivery! </h2>
 				<h3 class="email-h3">The best in Jewish video, delivered to your inbox weekly.</h3>

				<div style="background-color:#eee; padding:10px; margin:10px 0;" >
				<div class="form-row">
					<input type="text" v-model="newsletter.fname" placeholder="First Name" />
				</div>
				<div class="form-row">
					<input type="text" v-model="newsletter.lname" placeholder="Last Name" />
				</div>
				<div class="form-row">
					<input type="email" v-model="newsletter.email" placeholder="Email" />
				</div>
			</div>

				<button class="button" @click="submitNewsletter">Sign Me Up!</button>
				<button style="background-color:red;" class="button" @click="hideNewsletterModal">No, thanks.   </button>
				<button style="background-color:#444;" class="button"  @click="hideNewsletterModal" >Ask me later?</button>
			</div>
		</Modal>

	</nav>

</div>
</header>


<main>
	<section>
	</section>
		<section class="articles__list">
			<MatchFirst>
				<Route path="/" :exact="true">
					<HomePage></HomePage>
				</Route>
				<Route path="/likes" :exact="true">
					<Liked></Liked>
				</Route>
				<Route path="/bookmarks" :exact="true">
					<Bookmarked></Bookmarked>
				</Route>
			</MatchFirst>
		</section>
</main>


<footer class="site-footer">
	© 2017 JAKE TV. All rights reserved
</footer>

</div>
</template>
<script>
import { Route, MatchFirst, RouterLink } from 'vue-component-router';
import axios from 'axios';

import HomePage from './HomePage';
import Liked from './Liked';
import Bookmarked from './Bookmarked';
import LoginModal from '../components/LoginModal';
import Modal from '../components/Modal';
import ModalNavigation from '../components/ModalNavigation';
import TabContainer from '../components/TabContainer';
import { Tabs, TabNav, TabPanel } from '../components/Tabs';
import ChangePasswordForm from '../components/ChangePassword';
import ProfileForm from '../components/ProfileForm';

import injectors from '../mixins/injectors';

import '../../css/grid.css';
import '../../css/forms.css';
import '../../css/main.css';
import '../../css/header.css';

export default {
	name: 'App',
	mixins: [injectors],
	data () {
		return {
			shouldShowLoginModal: false,
			shouldShowProfileModal: false,
			showingNewsletterModal: !localStorage.newsletterShown,
			newsletter: {
				fname: null,
				lname: null,
				email: null
			}
		};
	},
	methods: {
		showLoginModal () {
			this.shouldShowLoginModal = true;
		},
		hideLoginModal () {
			this.shouldShowLoginModal = false;
		},
		showProfileModal () {
			this.shouldShowProfileModal = true;
		},
		hideProfileModal () {
			this.shouldShowProfileModal = false;
		},
		hideNewsletterModal () {
			this.showingNewsletterModal = false;
			localStorage.newsletterShown = true;
		},
		submitNewsletter () {
			axios({
				method: 'GET',
				url: '/emails.php',
				params: this.newsletter
			});

			alert('Thanks!'); // eslint-disable-lint no-alert
			this.hideNewsletterModal();
		}
	},
	components: {
		Route,
		MatchFirst,
		RouterLink,
		HomePage,
		Bookmarked,
		Liked,
		LoginModal,
		Modal,
		Tabs,
		TabNav,
		TabPanel,
		TabContainer,
		ModalNavigation,
		ProfileForm,
		ChangePasswordForm
	}
};
</script>

<style>
.logout{
	font-size: 12px;
}

.email-h2{
		text-align: center;
		font-weight: bold;
		font-size: 24px;
}

.email-h3{
	text-align: center;
	font-size: 18px;

}


	.button{
		box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2);
		height: 36px;
    line-height: 36px;
    padding: 0 12px;
    text-transform: uppercase;
		border: none;
    border-radius: 2px;
    outline: 0;
		text-decoration: none;
    color: #fff;
    background-color: #26a69a;
    text-align: center;
    letter-spacing: .5px;
		user-select: none;
		-webkit-tap-highlight-color: transparent;
		vertical-align: middle;
		z-index: 1;
		transition: .3s ease-out;
		position: relative;
		cursor: pointer;
		display: inline-block;
		overflow: hidden;
	}

	.newsletter-modal .button{
		margin-right: 8px;
	}
</style>
