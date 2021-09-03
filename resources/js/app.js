/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('items', require('./components/Items.vue').default);
Vue.component('dashboard', require('./components/Dashboard.vue').default);
Vue.component('inventories', require('./components/Inventories.vue').default);

Vue.component('users', require('./components/Users.vue').default);
Vue.component('activity-logs', require('./components/ActivityLogs.vue').default);

// Settings
Vue.component('companies', require('./components/Settings/Companies.vue').default);
Vue.component('locations', require('./components/Settings/Locations.vue').default);
Vue.component('departments', require('./components/Settings/Departments.vue').default);
Vue.component('buildings', require('./components/Settings/Buildings.vue').default);
Vue.component('types', require('./components/Settings/Types.vue').default);

//Reports
Vue.component('borrow-logs', require('./components/Reports/BorrowLogs.vue').default);
Vue.component('return-logs', require('./components/Reports/ReturnLogs.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
