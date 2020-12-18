<template>
	<HistoryRouter>
    <div class="root flex-grid">

        <aside class="menu sidebar flex-box is-open">
					<img width="120" style="margin:20px auto 0;" src="https://res.cloudinary.com/jaketv/image/upload/logo/jakelogo.png" />
					<hr />
					<div style="padding:0 10px;">
					<div class="menu-list">
						<RouterLink to="/admin" class="nav-link">Dashboard</RouterLink>
						<RouterLink to="/admin/users" class="nav-link">Users</RouterLink>
						<RouterLink to="/admin/select-categories" class="nav-link">Select Categories</RouterLink>
						<RouterLink to="/admin/categories" class="nav-link">Categories</RouterLink>
						<RouterLink to="/admin/posts" class="nav-link">Posts</RouterLink>
						<RouterLink to="/admin/sort-posts" class="nav-link">Sort Posts</RouterLink>
					</div>
					<p class="menu-label">Administration</p>
					<div class="menu-list">
						<RouterLink to="/admin/change-password" class="nav-link">Change Password</RouterLink>
						<RouterLink to="" class="nav-link">Logout</RouterLink>
					</div>
				</div>

        </aside>

        <section class="main flex-box scroll" id="app" style="">
						<Login v-if="!user.adminid" @loggedIn="user = $event"></Login>
						<MatchFirst v-else>
							<Route path="/" :exact="true">
								<Redirect to="/admin"></Redirect>
							</Route>
							<Route path="/admin/users"><Users></Users></Route>
							<Route path="/admin/categories"><Categories></Categories></Route>
							<Route path="/admin/select-categories"><SelectCategories></SelectCategories></Route>
							<Route path="/admin/posts">
								<template scope="{path}">
									<div>
										<MatchFirst>
											<Route :path="`${path}`" :exact="true"><Posts></Posts></Route>
											<Route :path="`${path}/new`"><NewPost/></Route>
											<Route :path="`${path}/:id`"><SinglePage></SinglePage></Route>
										</MatchFirst>
									</div>
								</template>
							</Route>
							<Route path="/admin/sort-posts"><SortPosts></SortPosts></Route>
							<Route path="/admin/change-password"><ChangePassword></ChangePassword></Route>
							<Route path="/admin/"><Dashboard></Dashboard></Route>
						</MatchFirst>
        </section>

    </div>
	</HistoryRouter>
</template>
<script>
import { HistoryRouter, Route, RouterLink, MatchFirst, Redirect } from 'vue-component-router';
import Users from './pages/Users';
import Categories from './pages/Categories';
import SelectCategories from './pages/SelectCategories';
import Posts from './pages/Post';
import SortPosts from './pages/SortPosts';
import ChangePassword from './pages/ChangePassword';
import Dashboard from './pages/HomePage';
import Login from './pages/Login';
import SinglePage from './pages/SinglePage';
import NewPost from './pages/NewPost';

export default {
	name: 'App',
	data () {
		return {
			user: Laravel.user
		};
	},
	components: {
		HistoryRouter,
		Route,
		RouterLink,
		Redirect,
		MatchFirst,
		Users,
		Categories,
		SelectCategories,
		Posts,
		SortPosts,
		ChangePassword,
		Dashboard,
		Login,
		SinglePage,
		NewPost
	}
};
</script>
