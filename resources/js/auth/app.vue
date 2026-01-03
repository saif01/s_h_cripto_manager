<template>
  <v-sheet class="pa-12 my-16" rounded>
    
    <v-card class="bg-indigo-darken-4 mx-auto px-6 py-12" max-width="544">
      <div class="text-center h3 mb-5">Login</div>
      
      <v-form >
        <v-form lazy-validation>
          <form @submit.prevent="login()">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  type="email"
                  label="Email"
                  placeholder="Enter Email"
                  v-model="form.email"
                  :readonly="loading"
                  :error-messages="form.errors.get('email')"
                  :rules="[required]"
                  required
                  class="required"
                  prepend-inner-icon="mdi mdi-email-lock-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  type="password"
                  label="Password"
                  placeholder="Enter Password"
                  v-model="form.password"
                  counter
                  maxlength="11"
                  :error-messages="form.errors.get('password')"
                  :rules="[required]"
                  required
                  class="required"
                  prepend-inner-icon="mdi mdi-lock"
                ></v-text-field>
              </v-col>

              <v-btn
                :disabled="!form"
                :loading="loading"
                color="success"
                size="large"
                type="submit"
                variant="elevated"
                block
              >
               <v-icon start icon="mdi mdi-login" ></v-icon> Sign In
              </v-btn>

             
            </v-row>
          </form>
        </v-form>
      </v-form>

      <v-btn block color="secondary" variant="elevated" class="mt-12" size="small" href="/" ><v-icon start icon="mdi mdi-home" ></v-icon>Home</v-btn>
     
    </v-card>
   
  </v-sheet>
</template>
  

<script>
// vform
import Form from "vform";

export default {
  data: () => ({
   
    loading: false,

    // Form
    form: new Form({
      email: "",
      password: "",
    }),
  }),

  methods: {
    
    required(v) {
      return !!v || "Field is required";
    },

     // Login
     login() {
      this.loading = true;
      this.form
        .post("/login_action")
        .then((response) => {
          console.log(response.data.data);
          let resData = response.data;

          // Success
          if (resData.status == "success") {
            // console.log(resData);
            // redirect with reload
            window.location.href = "/";
            
          }

          // Error
          if (resData.status == "error") {
            // suggestions
            console.log(resData.suggestions);
           
              Swal.fire({
                icon: "error",
                title: resData.title,
                text: resData.msg,
                customClass: "text-danger",
              });
            

            console.log(resData);
          }

          // Clear Current Session
          sessionStorage.clear();
          this.loading = false;
        })
        .catch((error) => {
          if (error.response && error.response.status === 419) {
            location.reload();
          }
          this.loading = false;
          console.log(error);
        });
    },
  },
};
</script>