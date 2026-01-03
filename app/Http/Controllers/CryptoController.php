<?php
/**
 * Cryptocurrency Portfolio Controller
 *
 * Manages cryptocurrency portfolio data including:
 * - Portfolio holdings (CRUD operations)
 * - Real-time price fetching from CoinGecko API
 * - P&L calculations (Profit/Loss in USD and percentage)
 * - Cryptocurrency options for dropdown selection (top 1000 by market cap)
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\cptCoinPrice;

class CryptoController extends Controller
{
    /**
     * No static data - all cryptocurrency data is fetched dynamically from CoinGecko API
     * This ensures always up-to-date coin lists, prices, and images
     */

    /**
     * Display portfolio holdings with real-time prices and P&L calculations
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Features:
     * - Pagination support
     * - Search functionality
     * - Sortable columns
     * - Real-time prices from CoinGecko API
     * - Automatic P&L calculation
     */
    public function index(){

        // Get request parameters with defaults
        $paginate       = Request('paginate', 10);        // Items per page
        $search         = Request('search', '');           // Search query
        $sort_direction = Request('sort_direction', 'desc'); // asc or desc
        $sort_field     = Request('sort_field', 'id');    // Column to sort by

        // Query database with sorting, searching, and pagination
        // Fetch both active (status = 1) and inactive (status = null) assets
        $allData = cptCoinPrice::where(function($query) {
                $query->where('status', 1)
                      ->orWhereNull('status');
            })
            ->orderBy($sort_field, $sort_direction)
            ->search( trim(preg_replace('/\s+/' ,' ', $search)) )  // Normalize whitespace
            ->paginate($paginate);

        // Fetch current prices and images from CoinGecko API
        // Also calculates P&L metrics for each asset
        $this->attachCurrentPrices($allData);

        // Return paginated data with prices and P&L
        return response()->json($allData, 200);

    }

    /**
     * Get cryptocurrency options for dropdown selection
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Features:
     * - Fetches top 1000 cryptocurrencies by market cap from CoinGecko API
     * - Includes coin symbol, name, CoinGecko ID, and image URL
     * - Cached for 1 hour to improve performance
     * - Sorted alphabetically by name
     * - Rate limiting protection (300ms delay between API calls)
     */
    public function getCryptoOptions()
    {
        try {
            // Try to get from cache first (cached for 1 hour = 3600 seconds)
            $cryptoOptions = Cache::remember('crypto_options', 3600, function () {
                $cryptoOptions = [];
                $perPage = 250; // CoinGecko API limit per page
                $totalPages = 4; // 4 pages × 250 = 1000 coins

                // Fetch data in 4 paginated requests
                for ($page = 1; $page <= $totalPages; $page++) {
                    $response = Http::timeout(15)->get('https://api.coingecko.com/api/v3/coins/markets', [
                        'vs_currency' => 'usd',
                        'order' => 'market_cap_desc',  // Order by market cap (largest first)
                        'per_page' => $perPage,
                        'page' => $page,
                        'sparkline' => false,          // Don't need price chart data
                    ]);

                    if ($response->successful()) {
                        $coins = $response->json();

                        // Extract needed fields for each coin
                        foreach ($coins as $coin) {
                            $cryptoOptions[] = [
                                'symbol' => strtoupper($coin['symbol']),  // BTC, ETH, etc.
                                'name' => $coin['name'],                   // Bitcoin, Ethereum, etc.
                                'coingecko_id' => $coin['id'],            // bitcoin, ethereum, etc.
                                'image' => $coin['image'] ?? null,        // Coin logo URL
                            ];
                        }
                    } else {
                        Log::error('Failed to fetch coins from CoinGecko', [
                            'page' => $page,
                            'status' => $response->status()
                        ]);
                        break;  // Stop if API fails
                    }

                    // Add delay between requests to respect rate limits
                    if ($page < $totalPages) {
                        usleep(300000); // 300ms = 0.3 seconds
                    }
                }

                // Sort alphabetically by coin name
                usort($cryptoOptions, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                return $cryptoOptions;
            });

            return response()->json($cryptoOptions, 200);

        } catch (\Exception $e) {
            Log::error('Error fetching crypto options from CoinGecko', [
                'error' => $e->getMessage()
            ]);

            // Return empty array with 500 status on error
            return response()->json([], 500);
        }
    }

    /**
     * Fetch current prices and images from CoinGecko API for portfolio items
     *
     * @param mixed $paginatedData Laravel paginated collection
     * @return void
     *
     * Process:
     * 1. Initializes inactive assets (status = null) with zero values
     * 2. Collects CoinGecko IDs from active items (status = 1 only)
     * 3. Makes single API call to fetch all prices and images
     * 4. Attaches current_price and coin_image to active items
     * 5. Calculates P&L metrics for active items only
     *
     * Features:
     * - Shows all assets in table (status = 1 and status = null)
     * - Only fetches prices and calculates for active assets (status = 1)
     * - Inactive assets (status = null) display with zero/null values
     * - Batch API call (more efficient than individual calls)
     * - Error handling with graceful fallback
     * - Logging for debugging
     */
    private function attachCurrentPrices(&$paginatedData)
    {
        // Exit early if no items
        if (empty($paginatedData->items())) {
            return;
        }

        // Step 0: Initialize all inactive assets with default values
        foreach ($paginatedData->items() as $item) {
            if ($item->status != 1) {
                $item->current_price = 0;
                $item->coin_image = null;
                $item->current_value = 0;
                $item->pnl_usd = 0;
                $item->pnl_percentage = 0;
            }
        }

        $coinIds = [];

        // Step 1: Collect all CoinGecko IDs from active database records (status = 1 only)
        foreach ($paginatedData->items() as $item) {
            // Only collect IDs for active assets
            if ($item->status != 1) {
                continue;
            }

            $coinId = strtolower(trim($item->asset_id));  // e.g., "bitcoin", "ethereum"
            if (!empty($coinId)) {
                $coinIds[] = $coinId;
            }
        }

        // If no valid IDs found, set defaults and calculate P&L for active assets only
        if (empty($coinIds)) {
            foreach ($paginatedData->items() as $item) {
                if ($item->status == 1) {
                    $item->current_price = 0;
                    $item->coin_image = null;
                    $this->calculatePnL($item);
                }
            }
            return;
        }

        try {
            // Step 2: Make single batch API call for all coins
            $idsString = implode(',', array_unique($coinIds));  // "bitcoin,ethereum,ripple"

            $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/coins/markets', [
                'ids' => $idsString,
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'sparkline' => false,
            ]);

            if ($response->successful()) {
                $coinsData = $response->json();

                // Step 3: Create lookup map for O(1) access
                $coinMap = [];
                foreach ($coinsData as $coin) {
                    $coinMap[$coin['id']] = [
                        'price' => $coin['current_price'] ?? 0,  // Real-time USD price
                        'image' => $coin['image'] ?? null,        // Coin logo URL
                    ];
                }

                // Step 4: Attach prices and images to each active portfolio item
                foreach ($paginatedData->items() as $item) {
                    // Only process active assets
                    if ($item->status != 1) {
                        continue;
                    }

                    $coinId = strtolower(trim($item->asset_id));

                    if (isset($coinMap[$coinId])) {
                        $item->current_price = $coinMap[$coinId]['price'];
                        $item->coin_image = $coinMap[$coinId]['image'];
                    } else {
                        $item->current_price = 0;
                        $item->coin_image = null;
                        Log::warning("Price not found for CoinGecko ID: {$coinId}");
                    }

                    // Calculate P&L metrics for active assets only
                    $this->calculatePnL($item);
                }
            } else {
                // API request failed - set defaults for active assets only
                Log::error('CoinGecko API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                foreach ($paginatedData->items() as $item) {
                    if ($item->status == 1) {
                        $item->current_price = 0;
                        $item->coin_image = null;
                        $this->calculatePnL($item);
                    }
                }
            }
        } catch (\Exception $e) {
            // Exception occurred - set defaults for active assets only
            Log::error('Error fetching prices from CoinGecko', [
                'error' => $e->getMessage()
            ]);

            foreach ($paginatedData->items() as $item) {
                if ($item->status == 1) {
                    $item->current_price = 0;
                    $item->coin_image = null;
                    $this->calculatePnL($item);
                }
            }
        }
    }

    /**
     * Calculate Profit/Loss metrics for a portfolio item
     *
     * @param mixed $item Portfolio item object (passed by reference)
     * @return void
     *
     * Calculations:
     * - Current Value = Quantity × Current Price
     * - P&L (USD) = Current Value - Total Invested
     * - P&L (%) = (P&L USD / Total Invested) × 100
     *
     * Adds three properties to the item:
     * - current_value: Total current value in USD
     * - pnl_usd: Profit/Loss in USD
     * - pnl_percentage: Profit/Loss as percentage
     */
    private function calculatePnL(&$item)
    {
        // Model casts handle conversion to decimal, default to 0 if null
        $assetQuantity = $item->asset_quantity ?? 0;
        $currentPrice = $item->current_price ?? 0;
        $totalInvested = $item->total_invested ?? 0;

        // Current Value = How much your holdings are worth now
        $item->current_value = $assetQuantity * $currentPrice;

        // P&L (USD) = Profit or Loss in dollar amount
        $item->pnl_usd = $item->current_value - $totalInvested;

        // P&L (%) = Profit or Loss as percentage of investment
        if ($totalInvested > 0) {
            $item->pnl_percentage = ($item->pnl_usd / $totalInvested) * 100;
        } else {
            $item->pnl_percentage = 0;  // Avoid division by zero
        }
    } 


    /**
     * Store a new cryptocurrency transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Validation:
     * - asset_name: Required, unique, max 50 chars (e.g., "BTC", "ETH")
     * - asset_id: Required, max 50 chars (CoinGecko ID, e.g., "bitcoin")
     * - asset_quantity: Required, numeric (amount of crypto owned)
     * - average_asset_price: Required, numeric (average purchase price)
     * - current_price: Optional, numeric (will be fetched from API anyway)
     *
     * Auto-calculated:
     * - total_invested = asset_quantity × average_asset_price
     */
    public function store(Request $request){

        // Validate incoming request data
        $request->validate([
            'asset_name' => 'required|string|max:50|unique:cpt_coin_prices,asset_name',
            'asset_id' => 'required|string|max:50',
            'asset_quantity' => 'required|numeric',
            'average_asset_price' => 'required|numeric',
            'current_price' => 'nullable|numeric',
        ]);

        // Create new record
        $data = new cptCoinPrice();

        $data->asset_name       = $request->asset_name;
        $data->asset_id         = $request->asset_id;
        $data->asset_quantity   = $request->asset_quantity;
        $data->average_asset_price = $request->average_asset_price;
        $data->current_price    = $request->current_price;

        // Auto-calculate total invested amount
        $data->total_invested   = (is_numeric($request->asset_quantity) && is_numeric($request->average_asset_price))
                                    ? $request->asset_quantity * $request->average_asset_price
                                    : null;

        $data->details     = $request->details;

        $data->save();

        return response()->json(['msg'=>'Stored Successfully &#128513;', 'icon'=>'success'], 200);

    }


    /**
     * Update an existing cryptocurrency transaction
     *
     * @param Request $request
     * @param int $id Record ID to update
     * @return \Illuminate\Http\JsonResponse
     *
     * Validation:
     * - Same as store(), except asset_name uniqueness ignores current record
     *
     * Note:
     * - Does NOT update current_price (fetched dynamically from API)
     * - Recalculates total_invested
     */
    public function update(Request $request, $id){

        // Validate request (unique check ignores current record)
        $request->validate([
            'asset_name' => 'required|string|max:50|unique:cpt_coin_prices,asset_name,'.$id,
            'asset_id' => 'required|string|max:50',
            'asset_quantity' => 'required|numeric',
            'average_asset_price' => 'required|numeric',
        ]);

        // Find and update record
        $data = cptCoinPrice::find($id);

        $data->asset_name       = $request->asset_name;
        $data->asset_id         = $request->asset_id;
        $data->asset_quantity   = $request->asset_quantity;
        $data->average_asset_price = $request->average_asset_price;

        // Recalculate total invested
        $data->total_invested   = (is_numeric($request->asset_quantity) && is_numeric($request->average_asset_price))
                                    ? $request->asset_quantity * $request->average_asset_price
                                    : null;

        $data->details     = $request->details;

        $data->save();

        return response()->json(['msg'=>'Updated Successfully &#128515;', 'icon'=>'success'], 200);

    }

    /**
     * Soft delete a transaction (temporary deletion)
     *
     * @param int $id Record ID to delete
     * @return \Illuminate\Http\JsonResponse
     *
     * Note: May be used for trash/restore functionality
     */
    public function destroy_temp($id)
    {
        $data    = cptCoinPrice::find($id);
        $success = $data->delete();

        return response()->json('success', 200);

    }

    /**
     * Permanently delete a transaction
     *
     * @param int $id Record ID to delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data    = cptCoinPrice::find($id);
        $success = $data->delete();

        return response()->json('success', 200);

    }


    // status
    public function status($id){

        $data = cptCoinPrice::find($id);
        if($data){

           $status = $data->status;
           
            if($status == 1){
                $data->status = null;
            }else{
                $data->status = 1;
            }
            $success    =  $data->save();
            return response()->json('success', 200);

        }

    }

   

}
