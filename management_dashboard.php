<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include('includes/header.php');

// get deals
include_once "./data/fetch_deals.php";

// import utils 
include_once "./utils/index.php";

//get the year from get request
$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');
$developer_name = isset($_GET['developer_name']) ? $_GET['developer_name'] : null;

// echo "<pre>";
// print_r($_GET);
// echo "</pre>";

$filter = [
    // 'CATEGORY_ID' => 0,
    '>=BEGINDATE' => "$selected_year-01-01",
    '<=BEGINDATE' => "$selected_year-12-31",
];

$deals = getFilteredDeals($filter) ?? [];
$deal_fields = get_deal_fileds();

$developerwise_deals = $deals;

if (!empty($developer_name)) {
    $developerwise_deals = array_filter($deals, function ($deal) use ($developer_name) {
        return $deal['UF_CRM_1727625822094'] == $developer_name;
    });
}

// get the develpers name
include_once "./static/developers.php";
$developers = getDevelopers();

if (!empty($deals)) {

    // get deals per deal type
    function get_deals_per_deal_type($deals, $deal_fields)
    {
        $deal_types = $deal_fields['UF_CRM_1727625752721']['items'] ?? [];

        $deals_per_deal_type = [];

        foreach ($deal_types as $deal_type) {
            $type_id = $deal_type['ID'] ?? null;
            $deal_type_name = map_enum($deal_fields, 'UF_CRM_1727625752721', $type_id) ?? 'Unknown';

            $deals_per_deal_type[$deal_type_name] = array_filter($deals, function ($deal) use ($type_id) {
                return $deal['UF_CRM_1727625752721'] ==  $type_id;
            });
        }

        return $deals_per_deal_type;
    }

    $deals_per_deal_type = get_deals_per_deal_type($deals, $deal_fields);

    // echo "<pre>";
    // print_r($deals_per_deal_type);
    // echo "</pre>";

    function get_closed_deals($deals)
    {
        return array_filter($deals, function ($deal) {
            return $deal['CLOSED'] == 'Y';
        });
    }

    $closed_deals = get_closed_deals($deals);

    function get_formatted_deals(&$deals)
    {

        $final_deals = [
            'January' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'February' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'March' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'April' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'May' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'June' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'July' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'August' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'September' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'October' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'November' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'December' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
            'total' => [
                'count_of_closed_deals' => 0,
                'property_price' => 0,
                'gross_commission' => 0,
                'net_commission' => 0,
                'total_payment_received' => 0,
                'amount_receivable' => 0,
            ],
        ];

        foreach ($deals as $deal) {
            $final_deals['total']['count_of_closed_deals'] += $deal['CATEGORY_ID'] == 0 ? 1 : 0;
            $final_deals['total']['property_price'] += (int)$deal['OPPORTUNITY'] ?? 0;
            $final_deals['total']['gross_commission'] += (int)explode('|', $deal['UF_CRM_1727628122686'])[0] ?? 0;
            $final_deals['total']['net_commission'] += (int)explode('|', $deal['UF_CRM_1736316474504'])[0] ?? 0;
            $final_deals['total']['total_payment_received'] += (int)explode('|', $deal['UF_CRM_1727628185464'])[0] ?? 0;
            $final_deals['total']['amount_receivable'] += $deal['UF_CRM_1727628203466'] ?? 0;

            $month = date('F', strtotime($deal['BEGINDATE']));
            $final_deals[$month]['count_of_closed_deals'] += $deal['CATEGORY_ID'] == 0 ? 1 : 0;
            $final_deals[$month]['property_price'] += (int)$deal['OPPORTUNITY'] ?? 0;
            $final_deals[$month]['gross_commission'] += (int)explode('|', $deal['UF_CRM_1727628122686'])[0] ?? 0;
            $final_deals[$month]['net_commission'] += (int)explode('|', $deal['UF_CRM_1736316474504'])[0] ?? 0;
            $final_deals[$month]['total_payment_received'] += (int)explode('|', $deal['UF_CRM_1727628185464'])[0] ?? 0;
            $final_deals[$month]['amount_receivable'] += $deal['UF_CRM_1727628203466'] ?? 0;
        };

        return $final_deals;
    }

    $final_deals = get_formatted_deals($deals);
    $developerwise_final_deals = get_formatted_deals($developerwise_deals);

    $total_deals = array_pop($final_deals);
    $developerwise_total_deals = array_pop($developerwise_final_deals);


    // monthly deals per developer with total monthly and yearly property value
    function get_monthly_deals_per_developer($deals, &$developers)
    {
        $monthlyDealsPerDeveloper = [];
        $quarters = [
            'Q1' => ['Jan', 'Feb', 'Mar'],
            'Q2' => ['Apr', 'May', 'Jun'],
            'Q3' => ['Jul', 'Aug', 'Sep'],
            'Q4' => ['Oct', 'Nov', 'Dec']
        ];

        foreach ($developers as $developer) {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            // Monthly calculations
            foreach ($months as $month) {
                $monthwiseDeals = array_filter($deals, function ($deal) use ($month) {
                    return date('M', strtotime($deal['BEGINDATE'])) == $month;
                });

                $monthlyDealsPerDeveloper[$developer]['monthly_deals'][$month]['deals'] = array_filter($monthwiseDeals, function ($deal) use ($developer) {
                    return $deal['UF_CRM_1727625822094'] == $developer;
                });

                $monthlyDealsPerDeveloper[$developer]['monthly_deals'][$month]['total_monthly_property_value'] = array_reduce($monthlyDealsPerDeveloper[$developer]['monthly_deals'][$month]['deals'], function ($total, $deal) {
                    return isset($deal['OPPORTUNITY']) ? $total + (int)$deal['OPPORTUNITY'] : $total;
                }, 0);
            }

            // Quarterly calculations
            foreach ($quarters as $quarter => $quarterMonths) {
                $quarterlyDeals = array_filter($deals, function ($deal) use ($quarterMonths) {
                    return in_array(date('M', strtotime($deal['BEGINDATE'])), $quarterMonths);
                });

                $monthlyDealsPerDeveloper[$developer]['quarterly_deals'][$quarter]['deals'] = array_filter($quarterlyDeals, function ($deal) use ($developer) {
                    return $deal['UF_CRM_1727625822094'] == $developer;
                });

                $monthlyDealsPerDeveloper[$developer]['quarterly_deals'][$quarter]['total_quarterly_property_value'] = array_reduce($monthlyDealsPerDeveloper[$developer]['quarterly_deals'][$quarter]['deals'], function ($total, $deal) {
                    return isset($deal['OPPORTUNITY']) ? $total + (int)$deal['OPPORTUNITY'] : $total;
                }, 0);
            }

            // Total property value calculation
            $monthlyDealsPerDeveloper[$developer]['total_property_value'] = array_reduce($monthlyDealsPerDeveloper[$developer]['monthly_deals'], function ($total, $prev) {
                return isset($prev['total_monthly_property_value']) ? $total + (int)$prev['total_monthly_property_value'] : $total;
            });
        }

        return $monthlyDealsPerDeveloper;
    }

    $monthly_deals_per_developer = get_monthly_deals_per_developer($deals, $developers);

    // echo "<pre>";
    // print_r($monthly_deals_per_developer);
    // echo "</pre>";


    // Deals per lead source
    function get_deals_per_lead_source($deals, $deal_fields)
    {
        $lead_sources = $deal_fields['UF_CRM_1727854893657']['items'];
        echo "<pre>";
        // print_r($lead_sources);
        echo "</pre>";
        $deals_per_lead_source = [];
        foreach ($lead_sources as $lead_source) {
            $lead_source_id = $lead_source['ID'];
            $lead_source_name = map_enum($deal_fields, 'UF_CRM_1727854893657',  $lead_source_id) ?? 'Unknown';

            $deals_per_lead_source[$lead_source_name] = array_filter($deals, function ($deal) use ($lead_source_id) {
                return $deal['UF_CRM_1727854893657'] ==  $lead_source_id;
            });
        }

        return $deals_per_lead_source;
    }

    $deals_per_lead_source = get_deals_per_lead_source($deals, $deal_fields) ?? [];

    // echo "<pre>";
    // print_r($deals_per_lead_source);
    // echo "</pre>";
}
// echo "<pre>";
// print_r($deals);
// echo "</pre>";

?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area overflow-y-auto flex-1 bg-gray-100 dark:bg-gray-900"> <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <!-- date picker -->
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($final_deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div>
                    <!-- cards container -->
                    <div class="mb-6 max-w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 justify-between gap-4">
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-green-500 hover:shadow-lg dark:bg-gray-800 dark:border-green-300/60 dark:hover:bg-green-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Closed Deals</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= $developerwise_total_deals['count_of_closed_deals'] ?> / <?= count($deals) ?></p>
                        </a>
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-red-500 hover:shadow-lg dark:bg-gray-800 dark:border-red-300/60 dark:hover:bg-red-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Total Property Price</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= number_format($developerwise_total_deals['property_price'], 2) ?> AED</p>
                        </a>
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-blue-500 hover:shadow-lg dark:bg-gray-800 dark:border-blue-300/60 dark:hover:bg-blue-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gross Commission</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= number_format($total_deals['gross_commission'], 2) ?> AED</p>
                        </a>
                        <a href="#" class="block max-w-sm p-6 bg-white border-l-8 rounded-lg shadow border-orange-500 hover:shadow-lg dark:bg-gray-800 dark:border-orange-300/60 dark:hover:bg-orange-200/10">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Net Commission</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400"><?= number_format($total_deals['net_commission'], 2) ?> AED</p>
                        </a>
                    </div>

                    <!-- main deals table and property type chart -->
                    <div class="my-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- table container -->
                        <div class="h-[30rem] col-span-2 bg-white dark:bg-gray-800 border shadow-xl border-gray-200 dark:border-gray-700 rounded-xl">
                            <!-- select developer filter -->
                            <div class="flex p-2 justify-between items-center">
                                <?php include './includes/select_developers.php'; ?>
                                <div class="flex items-center gap-2">
                                    <!-- <span class="text-gray-600 dark:text-gray-400 font-semibold text-lg">Developer: </span> -->
                                    <p class="text-gray-600 dark:text-gray-400 font-semibold text-lg bg-gray-200 dark:bg-gray-700 rounded px-2"><?= isset($_GET['developer_name']) ? ($_GET['developer_name'] == '' ? 'All Developers' : $_GET['developer_name']) : 'All Developers' ?></p>
                                </div>
                                <div class="flex items-center justify-end gap-2 p-2">
                                    <a href="#" onclick="download_table_as_csv('monthly_deals_table');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="relative h-[85%] overflow-auto sm:rounded-lg">
                                <table id="monthly_deals_table" class="text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="sticky top-0 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Month
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Count of Closed Deals
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Property Price
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Gross Commission
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Net Commission
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Total Payment Received
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Amount Receivable
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($developerwise_final_deals as $month => $details) : ?>
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <?= $month ?>
                                                </th>
                                                <td class="px-6 py-4">
                                                    <?= $details['count_of_closed_deals'] ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['property_price'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['gross_commission'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['net_commission'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['total_payment_received'], 2) ?>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <?= number_format($details['amount_receivable'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="sticky bottom-0 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="row" class="px-6 py-4 font-medium font-bold whitespace-nowrap">
                                                Total
                                            </th>
                                            <td class="px-6 py-4">
                                                <?= $developerwise_total_deals['count_of_closed_deals'] ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['property_price'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['gross_commission'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['net_commission'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['total_payment_received'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?= number_format($developerwise_total_deals['amount_receivable'], 2) ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- chart -->
                        <div class="flex flex-col justify-between gap-2 col-span-1 p-6 bg-white dark:bg-gray-800 border-t-8 shadow hover:shadow-xl border-green-500 dark:border-green-300/60 rounded-xl">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Deal Type</h3>
                            <div id="property-type-chart" class="flex justify-center items-center">

                            </div>
                        </div>
                    </div>

                    <!-- developer/property value -->
                    <div class="my-4 flex flex-col justify-between gap-2 p-6 bg-white dark:bg-gray-800 border-t-8 shadow hover:shadow-xl border-red-500 dark:border-red-300/60 rounded-xl">
                        <div class="flex flex-col lg:flex-row lg:justify-between px-2">
                            <div class="flex gap-2">
                                <div class="flex items-center justify-center">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Developers vs Property Value</h3>
                                </div>
                                <div class="flex items-center justify-end gap-2 p-2">
                                    <a href="#" onclick="download_table_as_csv('developer_vs_propertyValue');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <!-- developer search box -->
                            <div id="developer-search-box" class="max-w-lg w-full lg:w-[20rem]">
                                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                    </div>
                                    <input type="search" id="developer-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Developers..." required />
                                    <button type="button" id="clear-search" class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <script>
                            let clearSearch = document.getElementById('clear-search');
                            let developer_search = document.getElementById('developer-search'); // Get the developer-search

                            if (developer_search.value.length > 0) {
                                clearSearch.style.display = 'inline-block';
                            }

                            document.getElementById('developer-search').addEventListener('input', function() {
                                var input = this.value.toLowerCase();
                                let developers = <?= json_encode($developers) ?>;

                                // Loop through options and hide those that don't match the search query
                                developers.forEach(function(developer) {
                                    var option = document.getElementById(`developer-${developer}`);
                                    var optionText = developer.toLowerCase();
                                    option.style.display = optionText.includes(input) ? 'block' : 'none';
                                });
                            });
                        </script>
                        <div class="my-4 grid grid-cols-1 lg:grid-cols-5 gap-4">
                            <!-- table -->
                            <div id="" class="col-span-2 flex justify-center items-center">
                                <div class="w-full h-[35rem] col-span-2 bg-white dark:bg-gray-800 border shadow-xl border-gray-200 dark:border-gray-700 rounded-xl">
                                    <div class="relative h-full overflow-auto sm:rounded-lg">
                                        <table id="developer_vs_propertyValue" class="w-full h-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                            <thead class="sticky top-0 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3">
                                                        Developer
                                                    </th>
                                                    <!-- <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month) : ?>
                                                    <th scope="col" class="px-6 py-3"><?= $month ?></th>
                                                <?php endforeach; ?> -->
                                                    <th scope="col" class="px-4 py-3">
                                                        <?php
                                                        $sorted_monthly_deals_per_developer = $monthly_deals_per_developer;
                                                        $selected_developer_sort_order = $_GET['developer_sort_order'] ?? 'desc';
                                                        // decreasing order
                                                        if ($selected_developer_sort_order == 'desc') {
                                                            uasort($sorted_monthly_deals_per_developer, function ($a, $b) {
                                                                return $b['total_property_value'] <=> $a['total_property_value'];
                                                            });
                                                        }
                                                        // increasing order
                                                        else if ($selected_developer_sort_order == 'asc') {
                                                            uasort($sorted_monthly_deals_per_developer, function ($a, $b) {
                                                                return $a['total_property_value'] <=> $b['total_property_value'];
                                                            });
                                                        }
                                                        ?>
                                                        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="get" class="flex items-center gap-2">
                                                            <input type="hidden" name="developer_sort_order" value="<?= $selected_developer_sort_order == 'asc' ? 'desc' : 'asc' ?>">
                                                            <input type="hidden" name="year" value="<?= $_GET['year'] ?? date('d/m/Y') ?>">
                                                            <input type="hidden" name="developer_name" value="<?= $developer_name ?? '' ?>">
                                                            <p>Total Property value</p>
                                                            <button type="submit" class="">
                                                                <?php
                                                                if ($selected_developer_sort_order == 'asc') {
                                                                    echo '<i class="fa-solid fa-sort text-indigo-600"></i>';
                                                                } else {
                                                                    echo '<i class="fa-solid fa-sort-desc text-indigo-600"></i>';
                                                                }
                                                                ?>
                                                            </button>
                                                        </form>
                                                    </th>
                                                    <th scope="col" class="px-4 py-3">
                                                        Percentage
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="">
                                                <?php foreach ($sorted_monthly_deals_per_developer as $dev => $data) : ?>
                                                    <tr id="developer-<?= $dev ?>" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                            <?= $dev ?>
                                                        </th>
                                                        <!-- <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month) : ?>
                                                        <td class="px-6 py-4">
                                                            <?= number_format($monthly_deals_per_developer[$dev]['monthly_deals'][$month]['total_monthly_property_value'], 2) ?>
                                                        </td>
                                                    <?php endforeach; ?> -->
                                                        <td class="px-6 py-4">
                                                            <?= number_format($monthly_deals_per_developer[$dev]['total_property_value'], 2) ?>
                                                        </td>
                                                        <!-- get the total property value -->
                                                        <?php
                                                        $total_property_value = array_reduce(array_column($monthly_deals_per_developer, 'total_property_value'), function ($carry, $item) {
                                                            return $carry + $item;
                                                        }, 0);
                                                        ?>
                                                        <td class="px-6 py-4">
                                                            <?= number_format(($monthly_deals_per_developer[$dev]['total_property_value'] / $total_property_value) * 100, 2) ?>%
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="sticky bottom-0 bg-gray-50 dark:bg-gray-700">
                                                    <th scope="row" class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                                        Total
                                                    </th>
                                                    <td colspan="2" class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                                        <?= number_format($total_property_value, 2) ?>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- chart -->
                            <div id="developer-property-value-chart" class="col-span-3 flex justify-center items-center">

                            </div>
                        </div>
                    </div>

                    <!-- Quarter Wise Developer Quarterly Report  -->
                    <div class="my-4 flex flex-col justify-between gap-2 p-6 bg-white dark:bg-gray-800 border-t-8 shadow hover:shadow-xl border-blue-500 dark:border-blue-300/60 rounded-xl">
                        <duv class="flex gap-2 min-h-[3rem] mb-4">
                            <div class="py-4 flex items-center justify-center">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Developer Quarterly Report</h3>
                            </div>
                            <div class="flex items-center justify-end gap-2 p-2">
                                <a href="#" onclick="download_table_as_csv('developers_quarterly_report');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </div>
                        </duv>
                        <div class="w-full h-[35rem] col-span-2 bg-white dark:bg-gray-800 border shadow-xl border-gray-200 dark:border-gray-700 rounded-xl">
                            <div class="relative h-full overflow-auto sm:rounded-lg">
                                <table id="developers_quarterly_report" class="w-full h-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="sticky top-0 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">
                                                Developer
                                            </th>
                                            <?php foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) : ?>
                                                <th scope="col" class="px-6 py-3"><?= $quarter ?></th>
                                            <?php endforeach; ?>
                                            <th scope="col" class="px-4 py-3">
                                                <?php
                                                $sorted_monthly_deals_per_developer = $monthly_deals_per_developer;
                                                $selected_developer_sort_order = $_GET['developer_sort_order'] ?? 'desc';
                                                // decreasing order
                                                if ($selected_developer_sort_order == 'desc') {
                                                    uasort($sorted_monthly_deals_per_developer, function ($a, $b) {
                                                        return $b['total_property_value'] <=> $a['total_property_value'];
                                                    });
                                                }
                                                // increasing order
                                                else if ($selected_developer_sort_order == 'asc') {
                                                    uasort($sorted_monthly_deals_per_developer, function ($a, $b) {
                                                        return $a['total_property_value'] <=> $b['total_property_value'];
                                                    });
                                                }
                                                ?>
                                                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="get" class="flex items-center gap-2">
                                                    <input type="hidden" name="developer_sort_order" value="<?= $selected_developer_sort_order == 'asc' ? 'desc' : 'asc' ?>">
                                                    <input type="hidden" name="year" value="<?= $_GET['year'] ?? date('d/m/Y') ?>">
                                                    <input type="hidden" name="developer_name" value="<?= $developer_name ?? '' ?>">
                                                    <p>YTD Total Property value</p>
                                                    <button type="submit" class="">
                                                        <?php
                                                        if ($selected_developer_sort_order == 'asc') {
                                                            echo '<i class="fa-solid fa-sort text-indigo-600"></i>';
                                                        } else {
                                                            echo '<i class="fa-solid fa-sort-desc text-indigo-600"></i>';
                                                        }
                                                        ?>
                                                    </button>
                                                </form>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="">
                                        <?php foreach ($sorted_monthly_deals_per_developer as $dev => $data) : ?>
                                            <tr id="developer-<?= $dev ?>" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                    <?= $dev ?>
                                                </th>
                                                <?php foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) : ?>
                                                    <td class="px-6 py-4">
                                                        <?= number_format($monthly_deals_per_developer[$dev]['quarterly_deals'][$quarter]['total_quarterly_property_value'], 2) ?>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="px-6 py-4">
                                                    <?= number_format($monthly_deals_per_developer[$dev]['total_property_value'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="sticky bottom-0 bg-gray-50 dark:bg-gray-700">
                                            <th scope="row" class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                                Total
                                            </th>
                                            <?php foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $q) : ?>
                                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                                    <!-- get the quarterly total value -->
                                                    <?= number_format(array_reduce($monthly_deals_per_developer, function ($total, $item) use ($q) {
                                                        return $total + $item['quarterly_deals'][$q]['total_quarterly_property_value'];
                                                    }, 0), 2) ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                                <?= number_format(array_sum(array_column($monthly_deals_per_developer, 'total_property_value')), 2) ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deals per lead source -->
                <div class="my-4 flex flex-col justify-between gap-2 p-6 bg-white dark:bg-gray-800 border-t-8 shadow hover:shadow-xl border-blue-500 dark:border-blue-300/60 rounded-xl">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Transaction Breakdown Per Lead Source</h3>
                    <div id="lead-source-chart" class="flex justify-center items-center">

                    </div>
                </div>
        </div>

    <?php endif; ?>
    </div>
</div>
</div>

<script>
    // get the system theme
    // function getSystemTheme() {
    //     if (window.matchMedia) {
    //         return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    //     }
    // }


    function isDarkTheme() {
        return localStorage.getItem('darkMode') === 'true';
    }

    let isdark = isDarkTheme();

    function display_property_type_chart() {
        var dealsPerDealType = <?php echo json_encode($deals_per_deal_type); ?>;
        console.log('dealsPerDealType', dealsPerDealType);

        var offplanDeals = Object.keys(dealsPerDealType['Offplan']).length;
        var secondaryDeals = Object.keys(dealsPerDealType['Secondary']).length;

        // property type
        var options = {
            series: [offplanDeals, secondaryDeals],
            labels: ["Offplan", "Secondary"],
            chart: {
                width: 380,
                type: 'donut',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        const deal_types = {
                            'offplan': '1171',
                            'secondary': '1169'
                        };
                        var selectedType = config.w.config.labels[config.dataPointIndex].trim().toLowerCase();
                        var selectedTypeId = deal_types[selectedType];
                        if (confirm(`Would you like to apply the filter to show only ${selectedType} deals?`)) {
                            window.location.href = `overall_deals.php?deal_type=${selectedTypeId}`;
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        show: false
                    }
                }
            }],
            legend: {
                position: 'top',
                offsetY: 0,
                // height: 230,
                labels: {
                    colors: `${isdark ? '#ffffff' : '#000000'}`
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#property-type-chart"), options);
        chart.render();
    }

    display_property_type_chart();

    function display_developer_property_value_chart() {
        let developersData = <?php echo json_encode($sorted_monthly_deals_per_developer); ?>;

        let developers = [];
        let property_values = [];
        for (developer in developersData) {
            // console.log(developer);
            developers.push(developer)
            property_values.push(developersData[developer]['total_property_value']);
        }

        // console.log(developers);
        // console.log(property_values);


        let options = {
            series: [...property_values.slice(0, 10)],
            labels: [...developers.slice(0, 10)],
            chart: {
                width: 500,
                type: 'donut',
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 270
                }
            },
            dataLabels: {
                enabled: true
            },
            fill: {
                type: 'gradient',
            },
            legend: {
                formatter: function(val, opts) {
                    return val + " - " + opts.w.globals.series[opts.seriesIndex]
                },
                // position: 'top',
                offsetY: 0,
                labels: {
                    colors: `${isdark ? '#ffffff' : '#000000'}`
                }
            },
            title: {
                // text: 'Developers VS Property Value',
                style: {
                    color: `${isdark ? '#ffffff' : '#000000'}`
                }
            },
            responsive: [{
                breakpoint: 670,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom',
                        offsetY: 0,
                        labels: {
                            colors: `${isdark ? '#ffffff' : '#000000'}`
                        }
                    }
                }
            }]
        };

        let chart = new ApexCharts(document.querySelector("#developer-property-value-chart"), options);
        chart.render();
    }

    display_developer_property_value_chart();

    function display_lead_source_chart() {

        let deals_per_lead_source = <?php echo json_encode($deals_per_lead_source); ?>;
        let categories = [];
        let net_commission = {};
        let gross_commission = {};

        for (x in deals_per_lead_source) {
            // console.log(deals_per_lead_source[x]);
            categories.push(x);

            // initialise_commission_array for all types of leads
            if (net_commission[x] == null) {
                net_commission[x] = 0;
            }

            if (gross_commission[x] == null) {
                gross_commission[x] = 0;
            }

            if (Array.isArray(deals_per_lead_source[x]) && deals_per_lead_source[x].length > 0) {
                deals_per_lead_source[x].forEach(deal => {
                    if (deal['UF_CRM_1736316474504'] != null) {
                        net_commission[x] += parseFloat(deal['UF_CRM_1736316474504'].split('|')[0]);
                    }

                    if (deal['UF_CRM_1727628122686'] != null) {
                        gross_commission[x] += parseFloat(deal['UF_CRM_1727628122686'].split('|')[0]);
                    }
                });
            } else {
                for (deal in deals_per_lead_source[x]) {
                    if (deals_per_lead_source[x][deal]['UF_CRM_1736316474504'] != null) {
                        net_commission[x] += parseFloat(deals_per_lead_source[x][deal]['UF_CRM_1736316474504'].split('|')[0]);
                    }

                    if (deals_per_lead_source[x][deal]['UF_CRM_1727628122686'] != null) {
                        gross_commission[x] += parseFloat(deals_per_lead_source[x][deal]['UF_CRM_1727628122686'].split('|')[0]);
                    }
                }
            }
        }

        let net_commission_values = Object.values(net_commission);
        let gross_commission_values = Object.values(gross_commission);

        console.log(deals_per_lead_source);

        console.log(net_commission_values, gross_commission_values);

        let options = {
            series: [{
                name: 'Net Commission',
                data: [...net_commission_values]
            }, {
                name: 'Gross Commission',
                data: [...gross_commission_values]
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [...categories],
                labels: {
                    style: {
                        colors: `${isdark ? '#ffffff' : '#000000'}`
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'AED',
                    style: {
                        color: `${isdark ? '#ffffff' : '#000000'}`
                    }
                },
                labels: {
                    style: {
                        colors: `${isdark ? '#ffffff' : '#000000'}`
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " AED"
                    }
                }
            },
            legend: {
                labels: {
                    colors: `${isdark ? '#ffffff' : '#000000'}`
                }
            }
        };

        let chart = new ApexCharts(document.querySelector("#lead-source-chart"), options);
        chart.render();
    }

    display_lead_source_chart();
</script>

<?php include('includes/footer.php'); ?>