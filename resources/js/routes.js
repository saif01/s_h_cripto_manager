import {
    createRouter,
    createWebHistory
} from 'vue-router'


// All Routs define
const routes = [

    {
        path: '/',
        component: () => import('./components/pages/dashboard.vue'),
        name: 'Dashboard',
        meta: {
            title: 'Dashboard',
        },
    },

    {
        path: '/income-expense',
        component: () => import('./components/pages/ExpInc/dashboard_2.vue'),
        name: 'IncomeExpense',
        meta: {
            title: 'Income & Expense',
        },
    },


    {
        path: '/crypto-portfolio',
        component: () => import('./components/pages/crypto/index.vue'),
        name: 'CryptoPortfolio',
        meta: {
            title: 'Crypto Portfolio',
        },
    },



    {
        path: '/about',
        component: () => import('./components/pages/about/index.vue'),
        name: 'About',
        meta: {
            title: 'About',
        },
    },














    {
        path: '/:pathMatch(.*)*',
        component: () => import('./components/pages/dashboard.vue'),
        name: 'Error',
        meta: {
            title: 'Error',
        },
    }



];

const router = createRouter({
    history: createWebHistory(),
    //history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});


// Run brfore every route request
router.beforeEach((to, from, next) => {

    let appName = 'CryptoManager ';
    let title = to.meta && to.meta.title ? to.meta.title : '';
    // set current title
    document.title = `${appName} ${title}`;

    next();
});



export default router;
