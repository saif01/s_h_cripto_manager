<template>
  <v-container fluid class="dashboard-shell pa-4 pa-md-8">
    <div class="dash-glow dash-glow-1"></div>
    <div class="dash-glow dash-glow-2"></div>

    <v-row class="justify-center">
      <v-col cols="12" lg="10">

        <v-card class="dash-panel" elevation="12">
          <div class="d-flex align-center justify-space-between px-4 py-2 tab-header">
            <v-tabs v-model="tab" bg-color="transparent" slider-color="indigo-darken-3" color="indigo-darken-4"
              density="compact" class="flex-grow-1">
              <v-tab value="one" @click="keyCryptoPortfolioComponente++" size="small">
                <v-icon start icon="mdi-currency-btc" size="small"></v-icon>
                Crypto
              </v-tab>
              <v-tab value="two" @click="keyCostComponente++" size="small">
                <v-icon start icon="mdi-finance" size="small"></v-icon>
                Costs
              </v-tab>
              <v-tab value="three" @click="keyDepositComponente++" size="small">
                <v-icon start icon="mdi-bank-transfer" size="small"></v-icon>
                Deposits
              </v-tab>
              <v-tab v-if="auth" link router :to="{ name: 'IncomeExpense' }" size="small">
                <v-icon start icon="mdi-chart-areaspline" size="small"></v-icon>
                Income & Expense
              </v-tab>
            </v-tabs>

            <div class="d-flex align-center gap-2 ml-4">
              <v-chip v-if="auth" color="indigo-darken-4" size="small" class="text-white">
                <v-icon start icon="mdi-account" size="x-small"></v-icon>
                {{ auth.name }}
              </v-chip>
              <v-btn v-if="auth" color="error" variant="text" size="small" href="/logout">
                <v-icon icon="mdi-logout" size="small"></v-icon>
              </v-btn>
              <v-btn v-else color="success" variant="text" size="small" href="/login">
                <v-icon icon="mdi-login" size="small"></v-icon>
              </v-btn>
            </div>
          </div>


          <v-card-text class="pa-4 pa-md-6">
            <v-tabs-window v-model="tab">
              <v-tabs-window-item value="one">
                <CryptoPortfolioComponente :key="keyCryptoPortfolioComponente" />
              </v-tabs-window-item>
              <v-tabs-window-item value="two">
                <CostComponente :key="keyCostComponente" />
              </v-tabs-window-item>
              <v-tabs-window-item value="three">
                <DepositComponente :key="keyDepositComponente" />
              </v-tabs-window-item>
            </v-tabs-window>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>

import CostComponente from './cost/index.vue'
import DepositComponente from './deposit/index.vue'
import CryptoPortfolioComponente from './crypto/index.vue'

export default {

  components: {
    CostComponente,
    DepositComponente,
    CryptoPortfolioComponente
  },

  data: () => ({
    tab: 'one',

    keyCostComponente: 0,
    keyDepositComponente: 0,
    keyCryptoPortfolioComponente: 0,
  }),

  mounted() {

    if (this.auth && !this.auth.cost_deposit) {
      console.log('Cost Deposit Access Not Have')
      window.location.href = "/income-expense";
    }

  },

  created() {

  }

}


</script>

<style scoped>
.dashboard-shell {
  position: relative;
  min-height: 100vh;
  background: linear-gradient(135deg, #0b1026 0%, #101638 60%, #0f172a 100%);
  overflow: hidden;
}

.dash-glow {
  position: absolute;
  border-radius: 50%;
  filter: blur(90px);
  opacity: 0.4;
}

.dash-glow-1 {
  width: 460px;
  height: 460px;
  background: #6366f1;
  top: -80px;
  left: -60px;
}

.dash-glow-2 {
  width: 420px;
  height: 420px;
  background: #8b5cf6;
  bottom: -60px;
  right: -80px;
}

.dash-header {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.dash-panel {
  background: rgba(255, 255, 255, 0.98);
  border-radius: 18px;
  overflow: hidden;
}

.tab-header {
  border-bottom: 1px solid rgba(15, 22, 46, 0.06);
  background: rgba(99, 102, 241, 0.06);
}

.gap-2 {
  gap: 8px;
}

.gap-3 {
  gap: 12px;
}

@media (max-width: 960px) {
  .dashboard-shell {
    padding: 24px 16px;
  }

  .dash-panel {
    border-radius: 14px;
  }

  .tab-header {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .tab-header .d-flex:last-child {
    justify-content: center;
  }
}
</style>
