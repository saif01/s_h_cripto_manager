<template>
        <v-card>
            
            <v-tabs v-model="tab" bg-color="primary" align-tabs="center" slider-color="error">
                <v-tab value="one" @click="keyTab1Componente++"> <v-icon color="error" start icon="mdi mdi-minus-box-multiple"></v-icon> Expense</v-tab>
                <v-tab value="two" @click="keyTab2Componente++"> <v-icon color="success" start icon="mdi mdi-plus-box-multiple"></v-icon>  Income</v-tab>
                <v-tab value="three" @click="keyTab3Componente++"> Expense Type</v-tab>
            </v-tabs>

            <v-card-text>
                <v-tabs-window v-model="tab">
                    <v-tabs-window-item value="one"> <CostComponente :key="keyTab1Componente" /> </v-tabs-window-item>
                    <v-tabs-window-item value="two"> <IncomeComponente :key="keyTab2Componente"/> </v-tabs-window-item>
                    <v-tabs-window-item value="three"> <ExpenseTypeComponente :key="keyTab3Componente"/> </v-tabs-window-item>
                </v-tabs-window>
            </v-card-text>
            <v-card-subtitle v-if="auth">Logged in as {{ auth.name }}
                <v-btn class="float-right" color="error" href="/logout"> <v-icon start icon="mdi mdi-logout" ></v-icon>Logout</v-btn>
            </v-card-subtitle>
            <v-card-subtitle v-else class="d-flex justify-center pb-2">
                <v-btn color="success" href="/login"> <v-icon start icon="mdi mdi-login" ></v-icon> Login</v-btn>
     </v-card-subtitle>
        </v-card>
</template>

<script>

import CostComponente from './expense/index.vue'
import IncomeComponente from './income/index.vue'
import ExpenseTypeComponente from './expense_type/index.vue'

export default {

    components:{
        CostComponente,
        IncomeComponente,
        ExpenseTypeComponente
    },
    
    data: () => ({
      tab: null,

      keyTab1Componente:0,
      keyTab2Componente:0,
      keyTab3Componente:0,
    }),

    created() {
        
    }

}


</script>
