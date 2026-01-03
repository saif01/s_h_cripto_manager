<template>
  <v-container fluid class="auth-hero pa-0">
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>

    <v-row class="mx-0 auth-grid">
      <v-col
        cols="12"
        md="6"
        class="hero-panel d-flex flex-column justify-center px-10 py-14"
      >
        <v-chip
          color="deep-purple-accent-1"
          text-color="indigo-darken-4"
          variant="elevated"
          class="mb-4 align-self-start"
        >
          S/H Crypto Manager
        </v-chip>

        <div class="text-white text-h3 font-weight-bold mb-4">
          Control every transaction with confidence.
        </div>
        <div class="text-white text-body-1 text-opacity-80 mb-8">
          A focused console for monitoring balances, approvals, and treasury moves without losing sight of security.
        </div>

        <div class="hero-stats">
          <div class="stat-card">
            <div class="text-overline text-white text-opacity-70 mb-1">Session Guard</div>
            <div class="text-h6 text-white">Multi-layer protection</div>
          </div>
          <div class="stat-card">
            <div class="text-overline text-white text-opacity-70 mb-1">Audits</div>
            <div class="text-h6 text-white">Real-time visibility</div>
          </div>
          <div class="stat-card">
            <div class="text-overline text-white text-opacity-70 mb-1">Ops Speed</div>
            <div class="text-h6 text-white">Optimized for teams</div>
          </div>
        </div>
      </v-col>

      <v-col
        cols="12"
        md="6"
        class="form-panel d-flex align-center justify-center px-6 py-14"
      >
        <v-card class="login-card" max-width="520" elevation="14">
          <v-card-text class="pa-8">
            <div class="d-flex align-center justify-space-between mb-8">
              <div>
                <div class="text-h5 font-weight-bold mb-1">Sign in to workspace</div>
                <div class="text-body-2 text-medium-emphasis">
                  Enter your credentials to access the dashboard.
                </div>
              </div>
              <v-avatar color="indigo-darken-4" size="44" class="text-white">
                <v-icon icon="mdi-shield-lock-outline"></v-icon>
              </v-avatar>
            </div>

            <v-form lazy-validation @submit.prevent="login()">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    type="email"
                    label="Email"
                    placeholder="name@company.com"
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
                    placeholder="Enter your password"
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

                <v-col cols="12" class="pt-2">
                  <v-btn
                    block
                    color="indigo-darken-4"
                    size="large"
                    type="submit"
                    :disabled="!form.email || !form.password"
                    :loading="loading"
                    class="text-white"
                  >
                    <v-icon start icon="mdi mdi-login"></v-icon>
                    Sign In
                  </v-btn>
                </v-col>

                <v-col cols="12">
                  <v-btn
                    block
                    color="secondary"
                    variant="outlined"
                    class="mt-2"
                    size="large"
                    href="/"
                  >
                    <v-icon start icon="mdi mdi-home"></v-icon>
                    Back to Home
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
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

<style scoped>
.auth-hero {
  position: relative;
  background: radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.18), transparent 30%),
    radial-gradient(circle at 80% 0%, rgba(139, 92, 246, 0.2), transparent 32%),
    linear-gradient(135deg, #0b1026 0%, #101638 50%, #0f172a 100%);
  overflow: hidden;
}

.auth-grid {
  min-height: 100vh;
}

.glow {
  position: absolute;
  filter: blur(70px);
  opacity: 0.6;
  pointer-events: none;
}

.glow-1 {
  width: 420px;
  height: 420px;
  background: #6d28d9;
  top: 12%;
  right: 12%;
}

.glow-2 {
  width: 380px;
  height: 380px;
  background: #4f46e5;
  bottom: 8%;
  left: 4%;
}

.hero-panel {
  color: #fff;
  min-height: 100%;
}

.form-panel {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
  backdrop-filter: blur(12px);
}

.hero-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
}

.stat-card {
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 12px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(8px);
}

.login-card {
  width: 100%;
  border-radius: 18px;
  border: 1px solid rgba(15, 22, 46, 0.08);
  background: #ffffff;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.24);
}

@media (max-width: 960px) {
  .auth-grid {
    min-height: auto;
  }
  .hero-panel {
    padding: 56px 32px !important;
    text-align: center;
  }
  .hero-stats {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  }
  .form-panel {
    padding: 48px 24px !important;
  }
  .login-card {
    max-width: 640px;
  }
}
</style>
