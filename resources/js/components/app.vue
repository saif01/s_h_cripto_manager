<template>
    <v-app>
        <v-main>
            <router-view />
            <vue-progress-bar />
        </v-main>

    </v-app>
</template>

<script>
export default {

    props: ["authuser"],

    data: ()=>({
        // loginDialog: true,
        
    }),

    methods: {
       
    },

    mounted() {
        console.log('Component mounted.')
    },

    created(){

        if(this.authuser){
            // Set Auth and Role data in Store
            this.$store.commit("setAuth", JSON.parse(this.authuser));
        }
    
        console.log('Auth User', this.auth)
    }
}
</script>
