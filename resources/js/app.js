import './bootstrap';
import { createApp } from 'vue';
import router from './routes';
import mixin from './mixin';
import store from "./store";
// moment
import moment from 'moment'

// Vuetify 
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

// VueSweetalert2
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from 'sweetalert2';

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  willOpen: () => {
    // Add a class to the body to disable scrolling
    document.body.classList.add("swal-open");
  },
  willClose: () => {
    // Remove the class to enable scrolling
    document.body.classList.remove("swal-open");
  },
})

window.Swal = Swal;
window.Toast = Toast;

const vuetify = createVuetify({
    components,
    directives,
})


// VueProgressBar
import VueProgressBar from "@aacassandra/vue3-progressbar";
const options = {
    color: '#66FE5E',
    failedColor: 'red',
    thickness: "5px",
    transition: {
        speed: "0.2s",
        opacity: "0.6s",
        termination: 300,
    },
    autoRevert: true,
    location: "top", // left, right, top, bottom
    inverse: false,
};




const app = createApp({
    data() {
        return {
            // For Preloader
            preloader: false
        }
    }
});


app.config.globalProperties.$filters = {
  
  toCurrency(value) {
    return new Intl.NumberFormat("en-IN").format(
      value
    ) + "/="
  }
}

import IndexComponent from './components/app.vue';
app.component('index-component', IndexComponent);

app.config.globalProperties.$moment = moment;

app.mixin(mixin);

app.use(router)
app.use(vuetify)
app.use(VueProgressBar, options)
app.use(VueSweetalert2)
app.use(store)
app.mount('#app');
