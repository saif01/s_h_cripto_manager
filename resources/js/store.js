import { createStore } from 'vuex'


export default createStore({

    state: {
        auth: null,
    },

    getters: {

        getAuth(state) {
            return state.auth;
        },
       

    },

    mutations: {

        // Auth User
        setAuth(state, data) {
            state.auth = data;
        },


    },

    actions: {

    },

});