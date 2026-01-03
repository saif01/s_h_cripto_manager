<template>
    <div>
        <!-- Main Card Container -->
        <v-card class="crypto-card">
            <!-- Compact Header -->
            <v-card-title class="d-flex align-center justify-space-between pa-4 mobile-header">
                <div class="d-flex align-center gap-6 mobile-stats">
                    <div class="d-flex align-center gap-4">
                        <div class="text-center">
                            <div class="text-caption text-grey-darken-1">P&L</div>
                            <div class="text-h6 font-weight-bold" :class="getOverallPnlClass()">
                                ${{ formatCurrency(portfolioSummary.overallPNLUSD) }}
                                ({{ portfolioSummary.overallPNLPercentage >= 0 ? '+' : '' }}{{portfolioSummary.overallPNLPercentage.toFixed(2) }}%)
                            </div>
                        </div>

                        <v-divider vertical class="mx-2 mobile-divider"></v-divider>
                        <div class="text-center">
                            <div class="text-caption text-grey-darken-1">Current</div>
                            <div class="text-h6 font-weight-bold">${{ formatCurrency(portfolioSummary.totalCurrentValue) }}</div>
                        </div>
                        <v-divider vertical class="mx-2 mobile-divider"></v-divider>
                        <div class="text-center">
                            <div class="text-caption text-grey-darken-1">Invested</div>
                            <div class="text-h6 font-weight-bold">${{ formatCurrency(portfolioSummary.totalInvested) }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-center gap-2">
                    <v-btn size="small" color="primary" variant="text" @click="refreshComponent" icon="mdi-refresh" :loading="dataLoading"></v-btn>
                    <v-btn v-if="auth && auth.cost_deposit" size="small" color="success" variant="flat" @click="addDataModel('Add Transaction')">
                        <v-icon start icon="mdi-plus" size="small"></v-icon><span class="mobile-btn-text">Add</span>
                    </v-btn>
                    <v-btn v-else-if="!auth" size="small" color="success" variant="flat" href="/login">
                        <v-icon start icon="mdi-login" size="small"></v-icon><span class="mobile-btn-text">Login</span>
                    </v-btn>
                </div>
            </v-card-title>

            <v-card-text>
                <div class="card-body">
                    <div v-if="allData.data">

                        <!-- Table Controls: Pagination and Search -->
                        <v-row>
                            <v-col cols="4">
                                <!-- Records per page selector -->
                                <v-autocomplete prepend-icon="mdi-database-eye" v-model="paginate_custom" label="Show:"
                                    :items="tblItemNumberShow" title="Show Per Page" variant="underlined"
                                    density="compact"></v-autocomplete>
                            </v-col>
                            <v-col cols="8">
                                <!-- Search filter for table data -->
                                <v-text-field prepend-icon="mdi-clipboard-text-search" label="Search:"
                                    v-model="search_custom" placeholder="Search by any data at the table..."
                                    title="type for search" clearable variant="underlined"
                                    density="compact"></v-text-field>
                            </v-col>
                        </v-row>


                        <!-- Portfolio Holdings Table -->
                        <div class="table-responsive">
                            <v-table class="bordered__table" density="compact" hover>
                                <!-- Table Headers with Sortable Columns -->
                                <thead>
                                    <tr>
                                        <!-- Asset Name Column (with icon from CoinGecko) -->
                                        <th style="width: 15%;">
                                            <a href="#" @click.prevent="change_sort('asset_name')">Asset</a>
                                            <span v-if="sort_field == 'asset_name'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 8%;">
                                            <a href="#" @click.prevent="change_sort('pnl_percentage')">P&L (%)</a>
                                            <span v-if="sort_field == 'pnl_percentage'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 10%;">
                                            <a href="#" @click.prevent="change_sort('pnl_usd')">P&L (USD)</a>
                                            <span v-if="sort_field == 'pnl_usd'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 10%;">
                                            <a href="#" @click.prevent="change_sort('average_asset_price')">Average
                                                Price</a>
                                            <span v-if="sort_field == 'average_asset_price'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 22%;">
                                            <a href="#" @click.prevent="change_sort('current_price')">Current Price</a>
                                            <span v-if="sort_field == 'current_price'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 10%;">
                                            <a href="#" @click.prevent="change_sort('total_invested')">Total
                                                Invested</a>
                                            <span v-if="sort_field == 'total_invested'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 5%;">
                                            <a href="#" @click.prevent="change_sort('asset_quantity')">Qty</a>
                                            <span v-if="sort_field == 'asset_quantity'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <th class="text-right" style="width: 10%;">
                                            <a href="#" @click.prevent="change_sort('current_value')">Current Value</a>
                                            <span v-if="sort_field == 'current_value'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>

                                        <!-- <th>
                                            <a href="#" @click.prevent="change_sort('created_at')">Created At</a>
                                            <span v-if="sort_field == 'created_at'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th> -->

                                        <th v-if="auth && auth.cost_deposit" style="width: 15%;">
                                            <a href="#" @click.prevent="change_sort('id')">Action</a>
                                            <span v-if="sort_field == 'id'" class="ml-1">
                                                <v-icon v-if="sort_direction == 'desc'"
                                                    icon="mdi-sort-ascending"></v-icon>
                                                <v-icon v-if="sort_direction == 'asc'"
                                                    icon="mdi-sort-descending"></v-icon>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <!-- Table Body - Displays all cryptocurrency holdings -->
                                <tbody>
                                    <tr v-for="singleData in allData.data" :key="singleData.id">
                                        <td style="width: 15%;">
                                            <div class="d-flex align-center">
                                                <!-- Coin Logo (28px, fetched from CoinGecko API) -->
                                                <img v-if="singleData.coin_image" :src="singleData.coin_image"
                                                    :alt="singleData.asset_name"
                                                    style="width: 28px; height: 28px; margin-right: 10px; border-radius: 50%;" />
                                                <!-- Fallback Icon if image not available -->
                                                <v-icon v-else icon="mdi-currency-btc" size="28" class="mr-2"></v-icon>
                                                <span class="font-weight-medium">{{ singleData.asset_name }}</span>
                                            </div>
                                        </td>

                                        <!-- P&L Percentage - Color coded (green=profit, red=loss) -->
                                        <td class="text-right" :class="getPnlClass(singleData)" style="width: 10%;">
                                            {{ calculatePnlPercentage(singleData) }}
                                        </td>

                                        <!-- P&L USD Amount - Color coded (green=profit, red=loss) -->
                                        <td class="text-right" :class="getPnlClass(singleData)" style="width: 12%;">
                                            {{ calculatePnlUSD(singleData) }}
                                        </td>

                                        <!-- Average Purchase Price per Unit -->
                                        <td class="text-right" style="width: 12%;">{{
                                            formatPrice(singleData.average_asset_price) }}</td>

                                        <!-- Current Price - Compact design with stacked layout -->
                                        <td class="text-right" style="width: 20%;">
                                            <div class="d-flex align-center">
                                                <v-text-field :label="formatPrice(effectivePrice(singleData))"
                                                    v-model.number="manualPrices[singleData.id]" density="compact"
                                                    type="number" step="any" variant="outlined"
                                                    :value="effectivePrice(singleData)"
                                                    :placeholder="formatPrice(singleData.current_price)"
                                                    title="Manual override" hide-details
                                                    style="font-size: 12px;"></v-text-field>
                                                <v-btn
                                                    v-if="manualPrices[singleData.id] !== undefined && manualPrices[singleData.id] !== null && manualPrices[singleData.id] !== ''"
                                                    icon="mdi-close-circle" size="x-small" variant="text" class="ml-1"
                                                    title="Clear" @click="clearManualPrice(singleData)"></v-btn>
                                            </div>
                                        </td>

                                        <!-- Total Amount Invested (Quantity × Average Price) -->
                                        <td class="text-right" style="width: 12%;">${{
                                            formatCurrency(singleData.total_invested) }}</td>

                                        <!-- Asset Quantity Owned -->
                                        <td class="text-right" style="width: 8%;">{{
                                            formatNumber(singleData.asset_quantity) }}</td>

                                        <!-- Current Total Value (Quantity × Current Price) -->
                                        <td class="text-right" style="width: 13%;">${{
                                            formatCurrency(calculateCurrentValue(singleData)) }}
                                        </td>

                                        <!-- Record Creation Date -->
                                        <!-- <td>{{ $moment(singleData.created_at).format("DD/MM/YYYY") }}</td> -->

                                        <!-- Action Buttons - Only visible for authenticated users -->
                                        <td v-if="auth && auth.cost_deposit" style="width: 15%;">
                                            <v-btn v-if="singleData.status == 1" @click="statusChange(singleData)"
                                                size="small" color="green" variant="text" icon title="Active">
                                                <v-icon size="x-large" icon="mdi-check-decagram"></v-icon>
                                            </v-btn>
                                            <v-btn v-else @click="statusChange(singleData)" size="small" variant="text"
                                                icon class="ma-1" title="Inactive">
                                                <v-icon size="x-large" icon="mdi-close-octagon"></v-icon>
                                            </v-btn>

                                            <!-- Edit Button -->
                                            <v-btn class="ma-1" size="x-small" color="warning"
                                                @click="editDataModel(singleData, 'Edit Transaction')">
                                                <v-icon start icon="mdi mdi-text-box-edit"></v-icon>Edit
                                            </v-btn>
                                            <!-- Delete Button -->
                                            <v-btn class="ma-1" size="x-small" color="error"
                                                @click="deleteDataTemp(singleData.id)">
                                                <v-icon start icon="mdi mdi-delete"></v-icon>Del
                                            </v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </div>

                        <div class="mt-4">
                            <span>Total Records: {{ totalValue }}</span>
                        </div>
                        <v-pagination v-if="v_total > 1" v-model="currentPageNumber" :length="v_total"
                            :total-visible="7" @update:modelValue="getResults" rounded="circle" size="small"
                            active-color="red" color="green"></v-pagination>
                    </div>
                    <div v-else>
                        <v-skeleton-loader type="table-tbody" v-if="dataLoading"></v-skeleton-loader>
                    </div>
                    <div v-if="!totalValue && !dataLoading">Data not available</div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Add/Edit Transaction Dialog Modal -->
        <v-dialog v-model="dataModalDialog" persistent max-width="600px">
            <v-card>
                <!-- Dialog Header -->
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

                <!-- Dialog Content - Transaction Form -->
                <v-card-text>
                    <v-form lazy-validation>
                        <form @submit.prevent="editmode ? updateData() : createData()">
                            <v-row>
                                <!-- Cryptocurrency Selector - Fetches top 1000 from CoinGecko API -->
                                <v-col cols="12">
                                    <v-autocomplete label="Select Cryptocurrency:" placeholder="Choose a cryptocurrency"
                                        v-model="selectedCrypto" :items="cryptoOptions" item-title="displayText"
                                        item-value="coingecko_id" :error-messages="form.errors.get('asset_name')"
                                        :rules="[v_rules.required]" variant="outlined" class="required"
                                        prepend-inner-icon="mdi mdi-bitcoin" @update:modelValue="onCryptoSelect"
                                        clearable>
                                        <!-- Selected Value Display (24px icon) -->
                                        <template v-slot:selection="{ item }">
                                            <div class="d-flex align-center">
                                                <img v-if="item.raw.image" :src="item.raw.image" :alt="item.raw.name"
                                                    style="width: 24px; height: 24px; margin-right: 12px; border-radius: 50%;" />
                                                <v-icon v-else icon="mdi-currency-btc" size="24" class="mr-3"></v-icon>
                                                <span><strong>{{ item.raw.name }}</strong> ({{ item.raw.symbol
                                                    }})</span>
                                            </div>
                                        </template>
                                        <!-- Dropdown Item Display (32px icon) -->
                                        <template v-slot:item="{ props, item }">
                                            <v-list-item v-bind="props">
                                                <template v-slot:prepend>
                                                    <img v-if="item.raw.image" :src="item.raw.image"
                                                        :alt="item.raw.name"
                                                        style="width: 32px; height: 32px; border-radius: 50%;" />
                                                    <v-icon v-else icon="mdi-currency-btc" size="32"></v-icon>
                                                </template>
                                                <template v-slot:title>
                                                    <strong>{{ item.raw.name }}</strong> ({{ item.raw.symbol }})
                                                </template>
                                            </v-list-item>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Asset Quantity:" placeholder="Enter Asset Quantity"
                                        v-model="form.asset_quantity" step="any"
                                        :error-messages="form.errors.get('asset_quantity')" :rules="[v_rules.required]"
                                        required variant="outlined" class="required"
                                        prepend-inner-icon="mdi mdi-counter"></v-text-field>
                                </v-col>



                                <v-col cols="12">
                                    <v-text-field label="Average Asset Price:" placeholder="Enter Average Asset Price"
                                        v-model="form.average_asset_price" step="any"
                                        :error-messages="form.errors.get('average_asset_price')"
                                        :rules="[v_rules.required]" required variant="outlined" class="required"
                                        prepend-inner-icon="mdi mdi-currency-usd"></v-text-field>
                                </v-col>

                                <!-- <v-col cols="12">
                                    <v-text-field label="Current Price:" placeholder="Enter Current Price"
                                        v-model.number="form.current_price" step="any"
                                        :error-messages="form.errors.get('current_price')"
                                        variant="outlined"
                                        prepend-inner-icon="mdi mdi-chart-line"></v-text-field>
                                </v-col> -->

                                <v-col cols="12">
                                    <v-textarea type="text" label="Details:" placeholder="Enter Details"
                                        v-model="form.details" counter maxlength="1000"
                                        :error-messages="form.errors.get('details')" variant="outlined" rows="2"
                                        prepend-inner-icon="mdi mdi-text-box-outline"></v-textarea>
                                </v-col>



                                <v-btn block blockdepressed :loading="dataModalLoading" color="primary my-3"
                                    type="submit">
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
import mixin from "../../../mixin";

export default {
    mixins: [mixin],
    data() {
        return {
            //current page url
            currentUrl: "/crypto",
            paginate_custom: 25,
            sort_field: "total_invested",
            // Form
            form: new Form({
                id: "",
                asset_name: null,
                asset_id: null,
                asset_quantity: null,
                average_asset_price: null,
                current_price: null,
            }),

            // Crypto options for dropdown
            cryptoOptions: [],
            selectedCrypto: null,

            // Loading state
            dataLoading: false,

            // Manual price overrides keyed by record id.
            // When a value is present for an item's id, all price-based calculations
            // (Current Value, P&L USD/%, and portfolio summary) will use this value
            // instead of the live market price. Clearing the value reverts to market.
            manualPrices: {},
        };
    },

    computed: {
        portfolioSummary() {
            if (!this.allData || !this.allData.data) {
                return {
                    totalInvested: 0,
                    totalCurrentValue: 0,
                    overallPNLUSD: 0,
                    overallPNLPercentage: 0
                };
            }

            const totalInvested = this.allData.data.reduce((sum, item) => {
                return sum + (parseFloat(item.total_invested) || 0);
            }, 0);

            // Use effective price (manual override > market) for portfolio total
            const totalCurrentValue = this.allData.data.reduce((sum, item) => {
                const price = this.effectivePrice(item);
                const qty = parseFloat(item.asset_quantity) || 0;
                const currentValue = qty * price;
                return sum + currentValue;
            }, 0);

            const overallPNLUSD = totalCurrentValue - totalInvested;
            const overallPNLPercentage = totalInvested > 0 ? (overallPNLUSD / totalInvested) * 100 : 0;

            return {
                totalInvested,
                totalCurrentValue,
                overallPNLUSD,
                overallPNLPercentage
            };
        }
    },

    watchers: {},

    methods: {
        // Refresh component by fetching fresh data
        refreshComponent() {
            this.getResults();
            this.fetchCryptoOptions();
        },

        // Get effective price (manual override takes precedence).
        // Returns the numeric manual price if provided; otherwise falls back to item.current_price.
        // Invalid or missing values return 0, ensuring calculations remain safe.
        effectivePrice(item) {
            const override = this.manualPrices[item.id];
            const hasOverride = override !== undefined && override !== null && override !== '';
            const price = hasOverride ? parseFloat(override) : parseFloat(item.current_price);
            return isNaN(price) ? 0 : price;
        },

        // Clear manual override for an item and revert to market price.
        clearManualPrice(item) {
            delete this.manualPrices[item.id];
        },

        // Fetch cryptocurrency options from API
        async fetchCryptoOptions() {
            try {
                const response = await axios.get('/crypto/options');
                this.cryptoOptions = response.data.map(crypto => ({
                    ...crypto,
                    displayText: `${crypto.name} (${crypto.symbol})`
                }));
            } catch (error) {
                console.error('Error fetching crypto options:', error);
            }
        },

        // Handle cryptocurrency selection
        onCryptoSelect(value) {
            if (value) {
                const selected = this.cryptoOptions.find(opt => opt.coingecko_id === value);
                if (selected) {
                    this.form.asset_name = selected.symbol;
                    this.form.asset_id = selected.coingecko_id;
                }
            } else {
                this.form.asset_name = null;
                this.form.asset_id = null;
            }
        },

        // Override editDataModel to set selectedCrypto
        editDataModel(singleData, title = null) {
            this.editmode = true;
            this.dataModelTitle = title ? title : "Update Data";
            this.form.fill(singleData);
            this.selectedCrypto = singleData.asset_id;
            this.dataModalDialog = true;
        },

        // Override addDataModel to reset selectedCrypto
        addDataModel(title = null) {
            this.dataModelTitle = title ? title : "Store Data";
            this.editmode = false;
            this.selectedCrypto = null;
            this.form.reset();
            this.dataModalDialog = true;
        },

        // Calculate P&L Percentage
        // Calculate P&L Percentage using effective price.
        // If manual price is set for the row, that price is used;
        // otherwise, it uses the live market price.
        calculatePnlPercentage(item) {
            const price = this.effectivePrice(item);
            if (!price || !item.average_asset_price || !item.total_invested) {
                return 'N/A';
            }

            const currentValue = (parseFloat(item.asset_quantity) || 0) * price;
            const invested = parseFloat(item.total_invested);
            const pnl = currentValue - invested;
            const percentage = (pnl / invested) * 100;

            const sign = percentage >= 0 ? '+' : '';
            return `${sign}${percentage.toFixed(2)}%`;
        },

        // Calculate P&L USD using effective price (manual > market).
        calculatePnlUSD(item) {
            const price = this.effectivePrice(item);
            if (!price || !item.average_asset_price || !item.total_invested) {
                return 'N/A';
            }

            const currentValue = (parseFloat(item.asset_quantity) || 0) * price;
            const invested = parseFloat(item.total_invested);
            const pnl = currentValue - invested;

            const sign = pnl >= 0 ? '+' : '';
            return `${sign}$${Math.abs(pnl).toFixed(2)}`;
        },

        // Calculate Current Value using effective price.
        calculateCurrentValue(item) {
            const qty = parseFloat(item.asset_quantity) || 0;
            const price = this.effectivePrice(item);
            return qty * price;
        },

        // Get P&L Class for coloring based on effective price.
        getPnlClass(item) {
            const price = this.effectivePrice(item);
            if (!price || !item.total_invested) {
                return 'text-grey';
            }

            const currentValue = (parseFloat(item.asset_quantity) || 0) * price;
            const invested = parseFloat(item.total_invested);
            const pnl = currentValue - invested;

            if (pnl > 0) return 'text-success font-weight-bold';
            if (pnl < 0) return 'text-error font-weight-bold';
            return 'text-grey';
        },

        // Get Overall P&L Class
        getOverallPnlClass() {
            const pnl = this.portfolioSummary.overallPNLUSD;
            if (pnl > 0) return 'text-success';
            if (pnl < 0) return 'text-error';
            return 'text-grey';
        },

        // Format Price (for crypto prices with precision).
        // Uses 2 decimals for >= $0.01, else up to 8 decimals for micro-prices.
        formatPrice(value) {
            if (!value || isNaN(value) || value === 0) return 'N/A';

            const absValue = Math.abs(parseFloat(value));
            // Use 2 decimals if price is >= $0.01, otherwise use up to 8
            const decimals = absValue >= 0.01 ? 2 : 8;

            return `$${parseFloat(value).toFixed(decimals)}`;
        },

        // Format Currency (for USD values - always 2 decimals)
        formatCurrency(value) {
            if (!value || isNaN(value)) return '0.00';
            return parseFloat(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        // Format Number (for quantities)
        formatNumber(value) {
            if (!value || isNaN(value)) return '0.00';
            return parseFloat(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },

    created() {
        // Initial data load: fetch portfolio data and available crypto options
        // so the page can render holdings and the dialog dropdown.
        this.getResults();
        this.fetchCryptoOptions();
    },
};
</script>
<style scoped>
.gap-2 {
    gap: 8px;
}

.gap-4 {
    gap: 16px;
}

.gap-6 {
    gap: 24px;
}

@media (max-width: 768px) {
    .mobile-header {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .mobile-stats {
        justify-content: center;
        gap: 2px;
    }
    
    .mobile-stats .d-flex {
        gap: 8px;
    }
    
    .mobile-stats .text-center {
        min-width: 80px;
    }
    
    .mobile-stats .text-h6 {
        font-size: 0.9rem;
    }
    
    .mobile-divider {
        display: none;
    }
    
    .mobile-btn-text {
        display: none;
    }
}
</style>