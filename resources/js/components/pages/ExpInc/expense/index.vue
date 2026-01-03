<template>
  <div>
    <v-card>
      <v-card-title>
        <div class="row align-center">
          <div class="col-8">
            <h3>
              Expense
              <v-chip color="error" v-if="allData.total_expense">{{
                $filters.toCurrency(allData.total_expense)
              }}</v-chip>
            </h3>
          </div>
          <div class="col-4">
            <v-btn
              v-if="auth"
              size="x-large"
              color="success"
              class="float-right"
              @click="addDataModel('Add Deposit')"
              icon="mdi-plus-box"
              title="Add"
            ></v-btn>
            <v-btn
              v-else
              size="x-large"
              color="success"
              class="float-right"
              href="/login"
              icon="mdi-plus-box"
              title="Add"
            ></v-btn>
          </div>
        </div>
      </v-card-title>

      <v-card-text>
        <div class="card-body">
          <div v-if="allData.data">
            <v-tabs
              v-model="topTab"
              bg-color="secondary"
              align-tabs="center"
              slider-color="error"
              height="25"
            >
              <v-tab value="one"> Monthly Expense</v-tab>
              <v-tab value="two"> Data Short</v-tab>
            </v-tabs>

            <v-card-text>
              <v-tabs-window v-model="topTab">
                <v-tabs-window-item value="one">
                  <!-- Last 12 months expense  -->
                  <v-tabs v-if="allData.last_12_months_expense" show-arrows>
                    <!-- <v-tab>Last 12 months expense: </v-tab> -->
                    <v-tab
                      v-for="(item, indx) in allData.last_12_months_expense"
                      :key="indx"
                      >{{ item.month }} :
                      {{ $filters.toCurrency(item.total) }}</v-tab
                    >
                  </v-tabs>
                </v-tabs-window-item>
                <v-tabs-window-item value="two">
                  <v-row>
                    <v-col cols="6" lg="2">
                      <v-autocomplete
                        v-model="paginate_custom"
                        label="Show:"
                        :items="tblItemNumberShow"
                        title="Show Per Page"
                        variant="outlined"
                        density="compact"
                      ></v-autocomplete>
                    </v-col>

                    <v-col cols="6" lg="2">
                      <v-autocomplete
                        v-if="allData.exp_types"
                        label="Expense Type"
                        placeholder="Select Expense Type"
                        v-model="sort_by_type"
                        :items="allData.exp_types"
                        item-title="name"
                        item-value="id"
                        variant="outlined"
                        density="compact"
                        clearable
                      ></v-autocomplete>
                    </v-col>

                    <v-col cols="6" lg="2">
                      <v-text-field
                        type="date"
                        label="Start Date:"
                        v-model="start_date"
                        placeholder="Enter Start Date"
                        clearable
                        variant="outlined"
                        density="compact"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="6" lg="2">
                      <v-text-field
                        type="date"
                        label="End Date:"
                        v-model="end_date"
                        placeholder="Enter End Date"
                        clearable
                        variant="outlined"
                        density="compact"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" lg="4">
                      <v-text-field
                        label="Search:"
                        v-model="search_custom"
                        placeholder="Search by any data at the table..."
                        title="type for search"
                        clearable
                        variant="outlined"
                        density="compact"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-tabs-window-item>
              </v-tabs-window>
            </v-card-text>
            <div class="table-responsive">
              <v-table density="compact" hover>
                <thead>
                  <tr>
                    <th>
                      <a href="#" @click.prevent="change_sort('money')"
                        >Money</a
                      >
                      <span v-if="sort_field == 'money'" class="ml-1">
                        <v-icon
                          v-if="sort_direction == 'desc'"
                          icon="mdi-sort-ascending"
                        ></v-icon>
                        <v-icon
                          v-if="sort_direction == 'asc'"
                          icon="mdi-sort-descending"
                        ></v-icon>
                      </span>
                    </th>

                    <th>
                      <a href="#" @click.prevent="change_sort('expense_date')"
                        >Expense Date At</a
                      >
                      <span v-if="sort_field == 'expense_date'" class="ml-1">
                        <v-icon
                          v-if="sort_direction == 'desc'"
                          icon="mdi-sort-ascending"
                        ></v-icon>
                        <v-icon
                          v-if="sort_direction == 'asc'"
                          icon="mdi-sort-descending"
                        ></v-icon>
                      </span>
                    </th>

                    <th>
                      <a href="#" @click.prevent="change_sort('details')"
                        >Details</a
                      >
                      <span v-if="sort_field == 'details'" class="ml-1">
                        <v-icon
                          v-if="sort_direction == 'desc'"
                          icon="mdi-sort-ascending"
                        ></v-icon>
                        <v-icon
                          v-if="sort_direction == 'asc'"
                          icon="mdi-sort-descending"
                        ></v-icon>
                      </span>
                    </th>

                    <th>
                      <a href="#" @click.prevent="change_sort('id')">Action</a>
                      <span v-if="sort_field == 'id'" class="ml-1">
                        <v-icon
                          v-if="sort_direction == 'desc'"
                          icon="mdi-sort-ascending"
                        ></v-icon>
                        <v-icon
                          v-if="sort_direction == 'asc'"
                          icon="mdi-sort-descending"
                        ></v-icon>
                      </span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="singleData in allData.data" :key="singleData.id">
                    <td>
                      {{ $filters.toCurrency(singleData.money) }}
                      <v-chip v-if="singleData.exptype" size="x-small">{{
                        singleData.exptype.name
                      }}</v-chip>
                    </td>

                    <td>
                      {{
                        $moment(singleData.expense_date).format("DD/MMMM/YYYY")
                      }}

                      <v-chip size="x-small">{{
                        $moment(singleData.created_at).format(
                          "DD/MMMM/YYYY, h:mm a"
                        )
                      }}</v-chip>
                    </td>
                    <td>{{ singleData.details }}</td>
                    <td>
                      <v-btn
                        size="x-small"
                        color="warning"
                        @click="editDataModel(singleData, 'Edit Transaction')"
                        ><v-icon start icon="mdi mdi-text-box-edit"></v-icon
                        >Edit</v-btn
                      >
                      <v-btn
                        class="ml-1"
                        size="x-small"
                        color="error"
                        @click="deleteDataTemp(singleData.id)"
                        ><v-icon start icon="mdi mdi-delete"></v-icon>Del</v-btn
                      >
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </div>
            <div>
              <span>Total Records: {{ totalValue }}</span>
            </div>
            <v-pagination
              v-if="v_total > 1"
              v-model="currentPageNumber"
              :length="v_total"
              :total-visible="7"
              @update:modelValue="getResults"
              rounded="circle"
              size="small"
              active-color="red"
              color="green"
            ></v-pagination>
          </div>
          <div v-else>
            <v-skeleton-loader
              type="table-tbody"
              v-if="dataLoading"
            ></v-skeleton-loader>
          </div>
          <div v-if="!totalValue && !dataLoading">Data not available</div>
        </div>

        <v-card-text>
          <bar-chart
            v-if="allData && allData.chart_data"
            :chartLabel="allData.chart_data.labels"
            :chartData="allData.chart_data.data"
            :key="chartKey"
          ></bar-chart>
        </v-card-text>
      </v-card-text>
    </v-card>

    <v-card class="my-3"> </v-card>

    <!-- Dilog  -->
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
                  <v-text-field
                    type="number"
                    label="Expense of Money:"
                    placeholder="Enter Expense of Money"
                    v-model="form.money"
                    counter
                    maxlength="50"
                    :error-messages="form.errors.get('money')"
                    :rules="[v_rules.required]"
                    required
                    variant="outlined"
                    class="required"
                    prepend-inner-icon="mdi mdi-cash"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-autocomplete
                    v-if="allData.exp_types"
                    label="Expense Type"
                    placeholder="Select Expense Type"
                    v-model="form.type_id"
                    :items="allData.exp_types"
                    item-title="name"
                    item-value="id"
                    :error-messages="form.errors.get('type_id')"
                    variant="outlined"
                    class="required"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="12">
                  <!-- {{ form.expense_date }} -->
                  <v-text-field
                    type="date"
                    label="Expense Date:"
                    placeholder="Enter Expense Date"
                    v-model="form.expense_date"
                    :error-messages="form.errors.get('expense_date')"
                    variant="outlined"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    type="text"
                    label="Details:"
                    placeholder="Enter Details"
                    v-model="form.details"
                    counter
                    maxlength="1000"
                    :error-messages="form.errors.get('details')"
                    required
                    variant="outlined"
                    rows="2"
                    prepend-inner-icon="mdi mdi-text-box-outline"
                  ></v-textarea>
                </v-col>

                <v-btn
                  block
                  blockdepressed
                  :loading="dataModalLoading"
                  color="primary my-3"
                  type="submit"
                >
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
import moment from "moment";
import barChart from "./../chart/bar_chart.vue";

export default {
  components: {
    barChart,
  },

  data() {
    return {
      //current page url
      currentUrl: "/expense",
      start_date: moment().subtract(30, "days").format("YYYY-MM-DD"),

      // Form
      form: new Form({
        id: "",
        money: null,
        type_id: null,
        expense_date: null,
        details: "Expense of ",
      }),

      topTab: null,
      chartKey: 0,
      chartShow: false,
    };
  },

  // computed: {
  //   computedCustomValue() {
  //     if (this.start_date_raw) {
  //       // console.log('raw val ', this.start_date_raw)
  //       this.start_date = this.frmtInsideDate(this.start_date_raw);
  //       return this.start_date;
  //     }
  //   },

  //   computedCustomValue2() {
  //     if (this.end_date_raw) {
  //       // console.log('raw val ', this.end_date_raw)
  //       this.end_date = this.frmtInsideDate(this.end_date_raw);
  //       return this.end_date;
  //     }
  //   },
  // },

  watcher: {},

  methods: {

     // Get table data
     getResults(page = 1) {
            this.dataLoading = true;
            this.chartShow = false;
            return axios
                .get(this.currentUrl + "/index", {
                    params: {
                        page: page,
                        paginate: this.paginate_custom,
                        search: this.search_custom,
                        sort_direction: this.sort_direction,
                        sort_field: this.sort_field,
                        sort_direction_custom: this.sort_direction_custom,
                        sort_field_custom: this.sort_field_custom,
                        start_date: this.start_date,
                        end_date: this.end_date,
                        sort_by_day: this.sort_by_day,
                        sort_by_type: this.sort_by_type,
                    },
                })
                .then((response) => {
                    //console.log(response.data.data);
                    //console.log(response.data.from, response.data.to);
                    this.allData = response.data;

                    if (response.data.total != 0) {
                        this.totalValue = response.data.total.toLocaleString();
                    } else {
                        this.totalValue = null;
                    }

                    this.dataShowFrom = response.data.from;
                    this.dataShowTo = response.data.to;
                    this.currentPageNumber = response.data.current_page;
                    this.v_total = response.data.last_page;
                    // Loading Animation
                    this.dataLoading = false;
                    this.chartKey++;
                    this.chartShow = true;
                });
        },

  },

  created() {
    this.getResults();
  },
};
</script>