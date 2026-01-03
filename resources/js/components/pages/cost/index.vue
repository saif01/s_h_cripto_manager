<template>
  <div>
    <v-card>
      <v-card-title>
        <div class="row align-center">
          <div class="col-6">
            <h3>Transactions</h3>
          </div>
          <div class="col-6">
            <v-btn v-if="auth && auth.cost_deposit" size="x-large" color="success" class="float-right"
              @click="addDataModel('Add Transaction')" icon="mdi-plus-box" title="Add"></v-btn>
            <v-btn v-else-if="!auth" size="large" color="success" class="float-right" href="/login"
              title="Login"><v-icon start icon="mdi mdi-login"></v-icon> Login</v-btn>
          </div>
        </div>
      </v-card-title>
      <v-card-text cl>
        <div class="card-body">
          <div v-if="allData.data">
            <v-row>
              <v-col cols="4">
                <!-- data-show-per-page -->
                <v-autocomplete prepend-icon="mdi-database-eye" v-model="paginate_custom" label="Show:"
                  :items="tblItemNumberShow" title="Show Per Page" variant="underlined"
                  density="compact"></v-autocomplete>
              </v-col>
              <v-col cols="8">
                <!-- data-search -->
                <v-text-field prepend-icon="mdi-clipboard-text-search" label="Search:" v-model="search_custom"
                  placeholder="Search by any data at the table..." title="type for search" clearable
                  variant="underlined" density="compact"></v-text-field>
              </v-col>
            </v-row>

            <v-table class="bordered__table" density="compact">
              <thead class="bg-light-blue-darken-4">
                <tr>
                  <th>Last Deposit</th>
                  <th>Remaining</th>
                  <!-- <th>Received At</th> -->
                </tr>
              </thead>
              <tbody></tbody>
              <tr>
                <td>
                  <b class="text-success h2">{{
                    allData.last_deposit
                      ? $filters.toCurrency(allData.last_deposit.money)
                      : "N/A"
                  }}</b>
                </td>
                <td>
                  <b class="text-info h2">{{
                    $filters.toCurrency(allData.remaining_balance)
                    }}</b>
                </td>
                <!-- <td>{{ $moment(allData.last_deposit.created_at).format('DD/MM/YYYY') }}</td> -->
              </tr>
            </v-table>

            <div class="table-responsive">
              <v-table class="bordered__table" density="compact" hover>
                <thead>
                  <tr>
                    <th>
                      <a href="#" @click.prevent="change_sort('money')">Send</a>
                      <span v-if="sort_field == 'money'" class="ml-1">
                        <v-icon v-if="sort_direction == 'desc'" icon="mdi-sort-ascending"></v-icon>
                        <v-icon v-if="sort_direction == 'asc'" icon="mdi-sort-descending"></v-icon>
                      </span>
                    </th>

                    <th>
                      <a href="#" @click.prevent="change_sort('to')">Sent To</a>
                      <span v-if="sort_field == 'to'" class="ml-1">
                        <v-icon v-if="sort_direction == 'desc'" icon="mdi-sort-ascending"></v-icon>
                        <v-icon v-if="sort_direction == 'asc'" icon="mdi-sort-descending"></v-icon>
                      </span>
                    </th>
                    <th>
                      <a href="#" @click.prevent="change_sort('details')">Details</a>
                      <span v-if="sort_field == 'details'" class="ml-1">
                        <v-icon v-if="sort_direction == 'desc'" icon="mdi-sort-ascending"></v-icon>
                        <v-icon v-if="sort_direction == 'asc'" icon="mdi-sort-descending"></v-icon>
                      </span>
                    </th>

                    <th>
                      <a href="#" @click.prevent="change_sort('created_at')">Created At</a>
                      <span v-if="sort_field == 'created_at'" class="ml-1">
                        <v-icon v-if="sort_direction == 'desc'" icon="mdi-sort-ascending"></v-icon>
                        <v-icon v-if="sort_direction == 'asc'" icon="mdi-sort-descending"></v-icon>
                      </span>
                    </th>

                    <th v-if="auth && auth.cost_deposit">
                      <a href="#" @click.prevent="change_sort('id')">Action</a>
                      <span v-if="sort_field == 'id'" class="ml-1">
                        <v-icon v-if="sort_direction == 'desc'" icon="mdi-sort-ascending"></v-icon>
                        <v-icon v-if="sort_direction == 'asc'" icon="mdi-sort-descending"></v-icon>
                      </span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="singleData in allData.data" :key="singleData.id">
                    <td class="text-danger h4">{{ $filters.toCurrency(singleData.money) }}

                       <v-tooltip
                        :open-on-click="true"
                        :open-on-hover="false"
                        location="bottom"
                      >
                        <template v-slot:activator="{ props }">
                          <v-btn
                            v-bind="props"
                            icon="mdi-lock-question"
                            variant="text"
                            title="Balance"
                          >
                          </v-btn>
                        </template>
                        <span>{{ $filters.toCurrency(singleData.remaining) }}</span>
                      </v-tooltip>
                    </td>
                    <td>{{ singleData.to }}</td>
                    <td>{{ singleData.details }}</td>
                    <td>
                      {{ $moment(singleData.created_at).format("DD/MM/YYYY") }}
                    </td>
                    <td v-if="auth && auth.cost_deposit">
                      <v-btn class="ma-1" size="x-small" color="success" @click="resendLine(singleData.id)"><v-icon
                          start icon="mdi mdi-chat"></v-icon>SMS</v-btn>
                      <v-btn class="ma-1" size="x-small" color="warning"
                        @click="editDataModel(singleData, 'Edit Transaction')"><v-icon start
                          icon="mdi mdi-text-box-edit"></v-icon>Edit</v-btn>
                      <v-btn class="ma-1" size="x-small" color="error" @click="deleteDataTemp(singleData.id)"><v-icon
                          start icon="mdi mdi-delete"></v-icon>Del</v-btn>
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </div>
            <div>
              <span>Total Records: {{ totalValue }}</span>
            </div>
            <v-pagination v-if="v_total > 1" v-model="currentPageNumber" :length="v_total" :total-visible="7"
              @update:modelValue="getResults" rounded="circle" size="small" active-color="red"
              color="green"></v-pagination>
          </div>
          <div v-else>
            <v-skeleton-loader type="table-tbody" v-if="dataLoading"></v-skeleton-loader>
          </div>
          <div v-if="!totalValue && !dataLoading">Data not available</div>
        </div>
      </v-card-text>
    </v-card>

    <!-- Dilog   -->
    <v-dialog v-model="dataModalDialog" persistent max-width="600px">
      <v-card>
        <v-card-title class="justify-center">
          <v-row align="center">
            <v-col cols="10">{{ dataModelTitle }}</v-col>
            <v-col cols="2">
              <v-btn @click="dataModalDialog = false" class="float-right">
                Close
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>

        <v-card-text>
          <v-form lazy-validation>
            <form @submit.prevent="editmode ? updateData() : createData()">
              <v-row>
                <v-col cols="12">
                  <v-text-field type="number" label="Ammount of Money:" placeholder="Enter Ammount of Money"
                    v-model="form.money" counter maxlength="50" :error-messages="form.errors.get('money')"
                    :rules="[v_rules.required]" required variant="outlined" class="required"
                    prepend-inner-icon="mdi mdi-cash"></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-text-field type="number" label="Sent Number:" placeholder="Enter Sent Number" v-model="form.to"
                    counter maxlength="11" :error-messages="form.errors.get('to')"
                    :rules="[v_rules.required, v_rules.phone]"  variant="outlined" 
                    prepend-inner-icon="mdi mdi-cellphone-basic"></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-row class="mb-3">
                    <v-chip size="small" @click="form.details = 'Int. Bnk. Brack, Last No. 6724'">Brack</v-chip>
                    <v-chip size="small" @click="form.details = 'Int. Bnk. DBBL. Last No. 4387'">DBBL</v-chip>
                    <v-chip size="small" @click="form.details = 'Bkash Last No. 401'">Bkash</v-chip>
                    <v-chip size="small" @click="form.details = 'Nagod Last No. 401'">Nagod</v-chip>
                    <v-chip size="small" @click="form.details = 'Recharge by Bkash'">TU Bkash</v-chip>
                    <v-chip size="small" @click="form.details = 'Recharge by DBBL'">TU DBBL</v-chip>
                  </v-row>


                  <v-textarea type="text" label="Details:" placeholder="Enter Details" v-model="form.details" counter
                    maxlength="1000" :error-messages="form.errors.get('details')" required variant="outlined" rows="2"
                    prepend-inner-icon="mdi mdi-text-box-outline"></v-textarea>
                </v-col>
                <v-col class="mt-n10" cols="12">
                  <v-checkbox color="green" hide-details v-model="form.sms_sent"
                  label="Send Notification"></v-checkbox>
                </v-col>

                

                <v-btn block blockdepressed :loading="dataModalLoading" color="primary my-3" type="submit">
                  <span v-if="editmode"> Update </span>
                  <span v-else> Create </span>
                </v-btn>
              </v-row>
            </form>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
// vform
import Form from "vform";

export default {
  data() {
    return {
      //current page url
      currentUrl: "/cost",

      // Form
      form: new Form({
        id: "",
        money: "",
        to: "",
        details: "Int. Bnk. Brack, Last No. 6724",
        sms_sent: true,
      }),

      detailsOption: [
        {
          title: "Brack",
          value: "Int. Bnk. Brack, Last No. 6724"
        }
      ],
    };
  },

  watcher: {},

  methods: {
    // resendLine 
    resendLine(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You want to resend line message !",
        showCancelButton: true,
        // confirmButtonColor: '#d33',
        // cancelButtonColor: '#3085d6',
        confirmButtonText: "Yes",
        customClass: {
          confirmButton: "btn_delete",
        },
        willOpen: () => {
          document.body.classList.add("swal-open");
        },
        willClose: () => {
          document.body.classList.remove("swal-open");
        },
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          //console.log(id);
          this.$Progress.start();
          this.form
            .get(this.currentUrl + "/resend_line/" + id)
            .then((response) => {
              //console.log(response);
              Toast.fire({
                icon: "success",
                text: "Resend Line SMS ",
              });
              // Refresh Tbl Data with current page
              this.getResults(this.currentPageNumber);
              this.$Progress.finish();
            })
            .catch((data) => {
              Swal.fire({
                icon: "error",
                title: "Somthing Going Wrong<br>" + data.message,
                customClass: "text-danger",
                willOpen: () => {
                  document.body.classList.add("swal-open");
                },
                willClose: () => {
                  document.body.classList.remove("swal-open");
                },
              });
              //Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },


  },

  created() {
    this.getResults();
  },
};
</script>
