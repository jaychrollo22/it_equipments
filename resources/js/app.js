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
Vue.component('inventory-transfer', require('./components/InventoryTransfer.vue').default);
Vue.component('inventory-receive', require('./components/InventoryReceive.vue').default);

Vue.component('transfer-approval', require('./components/TransferApproval.vue').default);

Vue.component('users', require('./components/Users.vue').default);
Vue.component('activity-logs', require('./components/ActivityLogs.vue').default);

// Settings
Vue.component('companies', require('./components/Settings/Companies.vue').default);
Vue.component('locations', require('./components/Settings/Locations.vue').default);
Vue.component('departments', require('./components/Settings/Departments.vue').default);
Vue.component('buildings', require('./components/Settings/Buildings.vue').default);
Vue.component('types', require('./components/Settings/Types.vue').default);
Vue.component('categories', require('./components/Settings/Categories.vue').default);
Vue.component('system-approvers', require('./components/Settings/SystemApprovers.vue').default);

//Reports
Vue.component('borrow-logs', require('./components/Reports/BorrowLogs.vue').default);
Vue.component('return-logs', require('./components/Reports/ReturnLogs.vue').default);
Vue.component('asset-logs', require('./components/Reports/AssetLogs.vue').default);
Vue.component('disposed-logs', require('./components/Reports/DisposedLogs.vue').default);
Vue.component('asset-handover-forms', require('./components/Reports/AssetHandoverForms.vue').default);
Vue.component('asset-user-history', require('./components/Reports/AssetUserHistory.vue').default);
Vue.component('reports-letter-of-undertaking', require('./components/Reports/ReportsLetterOfUndertaking.vue').default);
Vue.component('reports-letter-of-undertaking-print', require('./components/Reports/ReportsLetterOfUndertakingPrint.vue').default);

//RFID Registration Device
Vue.component('rfid-registration-device', require('./components/Rfid/RfidRegistrationDevice.vue').default);


//For Disposal
Vue.component('for-disposal', require('./components/ForDisposal/ForDisposal.vue').default);
Vue.component('for-disposal-items', require('./components/ForDisposal/ForDisposalItems.vue').default);
Vue.component('for-disposal-approval', require('./components/ForDisposal/ForDisposalApproval.vue').default);

//For Maintenance
Vue.component('for-maintenance', require('./components/ForMaintenance/ForMaintenance.vue').default);

//Generate LOU
Vue.component('generate-lou', require('./components/LetterOfUndertaking/GenerateLou.vue').default);

//Requests
Vue.component('borrow-requests', require('./components/Requests/BorrowRequests.vue').default);
Vue.component('borrow-request-for-approval', require('./components/Requests/BorrowRequestForApproval.vue').default);
Vue.component('letter-of-undertaking', require('./components/Requests/LetterOfUndertaking.vue').default);
Vue.component('return-requests', require('./components/Requests/ReturnRequests.vue').default);

// User Page
Vue.component('home-user', require('./components/HomeUser/HomeUser.vue').default);
Vue.component('home-user-borrowed-requests', require('./components/HomeUser/BorrowedRequests.vue').default);
Vue.component('home-user-returned-requests', require('./components/HomeUser/ReturnedRequests.vue').default);


//QR Scanner
Vue.component('qr-scanner', require('./components/QrScanner/QrScanner.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
