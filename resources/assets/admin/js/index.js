import Vue from 'vue';
import AsyncComputed from 'vue-async-computed';
import 'bulma/css/bulma.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'font-awesome/css/font-awesome.css';

import App from './App';

import '../css/reset.css';
import '../css/grid.css';
import '../css/main.css';

Vue.use(AsyncComputed);

// eslint-disable-next-line no-new
new Vue({
	el: '#app',
	render: h => h(App),
});
