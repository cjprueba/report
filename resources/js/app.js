/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

/* ------------------------------------------------------------------------------- */
/*window.$ = require('jquery')
window.JQuery = require('jquery')*/


/* ********************************************* */

/* VUE */ 


window.Vue = require('vue');
require('./bootstrap');
require('bootstrap-datepicker');
// // not sure if you need this at all


/* ********************************************* */

/* APPEX CHARTS */

import VueApexCharts from 'vue-apexcharts'

/* ********************************************* */

/* FONT AWESOME */ 

import { library } from '@fortawesome/fontawesome-svg-core'
import { faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faPlus, faAmbulance, faCog, faChartArea, faTv, faStickyNote, faBell, faEnvelope, faSearch, faDownload)

/* ********************************************* */

/* ------------------------------------------------------------------------------- */

/* COMPONENTS */ 

/* ********************************************* */

// FONT AWESOME

Vue.component('font-awesome-icon', FontAwesomeIcon)

/* ********************************************* */

// APPEX CHARTS

Vue.component('apexchart', VueApexCharts)

Vue.component('bar', require('./components/charts/Bar.vue').default);
Vue.component('donut', require('./components/charts/Donut.vue').default);

/* ********************************************* */

// CHART JS

Vue.component('barChart', require('./components/charts/Barchart.vue').default);

/* ********************************************* */

// MARCA

Vue.component('marca', require('./components/marca/Inicio.vue').default);
Vue.component('venta-marca-categoria', require('./components/marca/busquedas/VentaMarcaCategoria.vue').default);

/* ********************************************* */

// CAJAS

Vue.component('primeraCaja', require('./components/cajas/PrimeraCaja.vue').default);
Vue.component('segundaCaja', require('./components/cajas/SegundaCaja.vue').default);
Vue.component('terceraCaja', require('./components/cajas/TerceraCaja.vue').default);
Vue.component('cuartaCaja', require('./components/cajas/CuartaCaja.vue').default);

/* ********************************************* */

Vue.component('miscomponentes', require('./components/MisComponentes.vue').default);
Vue.component('categoria', require('./components/Categoria.vue').default);
Vue.component('formv', require('./components/Form.vue').default);
Vue.component('sidebar', require('./components/Sidebar.vue').default);
Vue.component('dashboard', require('./components/Dashboard.vue').default);
Vue.component('home', require('./components/Home.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
