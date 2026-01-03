<template>
    <v-card>

        <v-tabs v-model="tab" bg-color="primary" align-tabs="center" slider-color="error">
            <v-tab value="one" @click="keyCryptoPortfolioComponente++">Crypto</v-tab>
            <v-tab value="two" @click="keyCostComponente++">Costs</v-tab>
            <v-tab value="three" @click="keyDepositComponente++">Deposits</v-tab>



            <v-tab v-if="auth" link router :to="{ name: 'IncomeExpense' }"> Income & Expense</v-tab>

        </v-tabs>

        <v-card-text>
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

        <v-card-subtitle v-if="auth">Logged in as {{ auth.name }}
            <v-btn class="float-right" color="error" href="/logout"> <v-icon start
                    icon="mdi mdi-logout"></v-icon>Logout</v-btn>
        </v-card-subtitle>
        <v-card-subtitle v-else class="d-flex justify-center pb-2">
            <v-btn color="success" href="/login"> <v-icon start icon="mdi mdi-login"></v-icon> Login</v-btn>
        </v-card-subtitle>
    </v-card>
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
        tab: null,

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
