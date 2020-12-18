<template>
<HistoryRouter>
	<MatchFirst>
		<Route path="/bih"><Bih/></Route>
		<Route path="/"><MainSite/></Route>
	</MatchFirst>
</HistoryRouter>
</template>
<script>
import { HistoryRouter, Route, MatchFirst } from 'vue-component-router';
import MainSite from './pages/MainSite';
import Bih from './pages/bih';

export default {
	components: {
		HistoryRouter, MatchFirst, Route, MainSite, Bih
	}
};
</script>