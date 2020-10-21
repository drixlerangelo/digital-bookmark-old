require('./bootstrap');

import 'bulma/css/bulma.min.css';

// page component
Vue.component('auth-page', require('./components/page/AuthPage').default);

new Vue({
    el : '#vue-element'
});
