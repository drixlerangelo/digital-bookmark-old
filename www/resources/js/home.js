require('./bootstrap');

import 'bulma/css/bulma.min.css';

// page component
Vue.component('home-page', require('./components/page/HomePage').default);

new Vue({
    el : '#vue-element'
});
