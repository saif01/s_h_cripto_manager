<template>
  <div>
    <v-card>
      <v-card-title>
        <div class="row align-center">
          <div class="col-6">
            <h3>Income</h3>
          </div>
          <div class="col-6">
            <v-btn
              v-if="auth"
              size="x-large"
              color="success"
              class="float-right"
              @click="addDataModel('Add Income')"
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
            <v-row>
              <v-col cols="4">
                <!-- data-show-per-page --> 
                <v-autocomplete
                prepend-icon="mdi-database-eye"
                v-model="paginate_custom"
                label="Show:"
                :items="tblItemNumberShow"
                title="Show Per Page"
                variant="underlined"
                density="compact"
              ></v-autocomplete>
              </v-col>
              <v-col cols="8">
                <!-- data-search -->
                <v-text-field
                  prepend-icon="mdi-clipboard-text-search"
                  label="Search:"
                  v-model="search_custom"
                  placeholder="Search by any data at the table..."
                  title="type for search"
                  clearable
                  variant="underlined"
                  density="compact"
                ></v-text-field>
              </v-col>
            </v-row>

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
                      <a href="#" @click.prevent="change_sort('details')">Details</a>
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
                      <a href="#" @click.prevent="change_sort('created_at')">Created At</a>
                      <span v-if="sort_field == 'created_at'" class="ml-1">
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

                    

                    <th v-if="auth">
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
                    <td>{{ $filters.toCurrency(singleData.money) }}</td>
                    <td>{{ singleData.details }}</td>
                    <td>
                        {{ $moment(singleData.created_at).format('DD/MMMM/YYYY, h:mm a') }}
                    </td>
                    <td v-if="auth">
                      <v-btn
                        size="x-small"
                        color="warning"
                        @click="editDataModel(singleData, 'Edit Income')"
                        ><v-icon start icon="mdi mdi-text-box-edit" ></v-icon>Edit</v-btn
                      >
                      <v-btn
                        class="ml-1"
                        size="x-small"
                        color="error"
                        @click="deleteDataTemp(singleData.id)"
                        ><v-icon start icon="mdi mdi-delete" ></v-icon>Del</v-btn
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
      </v-card-text>
    </v-card>

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
                    label="Deposit of Money:"
                    placeholder="Enter Deposit of Money"
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

export default {

  data() {
    return {
      //current page url
      currentUrl: "/income",

      // Form
      form: new Form({
        id: "",
        money: "",
        type: "",
        details: "Monthly Salary",
      }),
    };
  },

  watcher: {},

  methods: {},

  created() {
    this.getResults();
  },
};
</script>