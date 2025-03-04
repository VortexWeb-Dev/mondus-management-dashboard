<?php
include_once __DIR__ .  "/crest/crest.php";
include_once __DIR__ .  "/crest/settings.php";
include_once __DIR__ .  "/utils/index.php";
include('includes/header.php');

include_once __DIR__ . "/data/fetch_deals.php";
include_once __DIR__ . "/data/fetch_users.php";

$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$deal_type = isset($_GET['deal_type']) ? $_GET['deal_type'] : null;

$filter = [
    'CATEGORY_ID' => 0,
    '>=BEGINDATE' => "$selected_year-01-01",
    '<=BEGINDATE' => "$selected_year-12-31",
    'UF_CRM_1741000861839' => $deal_type
];

$dealsData = get_paginated_deals($page) ?? [];
$deals = $dealsData['deals'] ?? [];

$total_deals = $dealsData['total'] ?? 0;
$total_pages = ceil($total_deals / 50);

$fields = get_deal_fileds();
$overall_deals = [];

if (!empty($deals)) {
    foreach ($deals as $index => $deal) {
        $overall_deals[$index]['Date'] = date('Y-m-d', strtotime($deal['BEGINDATE'] ?? ''));
        $overall_deals[$index]['Transaction Type'] = $deal['UF_CRM_1741000822473'] ?? '--';
        $overall_deals[$index]['Deal Type'] = $deal['UF_CRM_1741000861839'] ?? '--';
        $overall_deals[$index]['Project Name'] = $deal['UF_CRM_1741000869656'] ?? '--';
        $overall_deals[$index]['Unit No'] = $deal['UF_CRM_1741000878426'] ?? '--';
        $overall_deals[$index]['Developer Name'] = $deal['UF_CRM_1741000886208'] ?? '--';
        $overall_deals[$index]['Property Type'] = $deal['UF_CRM_1741000894474'] ?? '--';
        $overall_deals[$index]['No Of Br'] = $deal['UF_CRM_1741000903941'] ?? '--';
        $overall_deals[$index]['Client Name'] = $deal['UF_CRM_1741063576078'] ?? '--';

        if (isset($deal['ASSIGNED_BY_ID'])) {
            $agent = getUser($deal['ASSIGNED_BY_ID']);
            $agentName = trim(($agent['NAME'] ?? '') . ' ' . ($agent['SECOND_NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? ''));
            $overall_deals[$index]['Agent Name'] = !empty($agentName) ? $agentName : '--';
        } else {
            $overall_deals[$index]['Agent Name'] = '--';
        }

        $overall_deals[$index]['Property Price'] = $deal['OPPORTUNITY'] ?? '--';
        $overall_deals[$index]['Gross Commission (Incl. VAT)'] = $deal['UF_CRM_1741000938260'];
        $overall_deals[$index]['Gross Commission'] = $deal['UF_CRM_1741000946843'] ?? '--';
        $overall_deals[$index]['VAT'] = (int)$deal['UF_CRM_1741000938260'] - (int)($deal['UF_CRM_1741000946843'] ?? 0);
        $overall_deals[$index]['Lead Source'] = map_enum($fields, 'SOURCE_ID', $deal['SOURCE_ID'] ?? null) ?? '--';

        // $overall_deals[$index]['Team'] = map_enum($fields, 'UF_CRM_1727854555607', $deal['UF_CRM_1727854555607'] ?? null) ?? '--';
        // $overall_deals[$index]['Mondus Commission'] = $deal['UF_CRM_1736316474504'] ?? '--';
        // $overall_deals[$index]['Invoice Status'] = map_enum($fields, 'UF_CRM_1727872815184', $deal['UF_CRM_1727872815184'] ?? null) ?? '--';
        // $overall_deals[$index]['Payment Received'] = map_enum($fields, 'UF_CRM_1727627289760', $deal['UF_CRM_1727627289760'] ?? null) ?? '--';
        // $overall_deals[$index]['1st Payment Received'] = $deal['UF_CRM_1727874909907'] ?? '--';
        // $overall_deals[$index]['2nd Payment Received'] = $deal['UF_CRM_1727874935109'] ?? '--';
        // $overall_deals[$index]['3rd Payment Received'] = $deal['UF_CRM_1727874959670'] ?? '--';
        // $overall_deals[$index]['Total Payment Received'] = $deal['UF_CRM_1727628185464'] ?? '--';
        // $overall_deals[$index]['Amount Receivable'] = $deal['UF_CRM_1727628203466'] ?? '--';
    }
}
?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900">
        <?php include('includes/navbar.php'); ?>
        <div class="px-8 py-6">
            <!-- Date picker -->
            <?php include('./includes/datepicker.php'); ?>

            <?php if (empty($deals)): ?>
                <div class="h-[65vh] flex justify-center items-center">
                    <h1 class="text-2xl font-bold mb-6 dark:text-white">No data available</h1>
                </div>
            <?php else: ?>
                <div class="p-4 shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="text-gray-700 dark:text-gray-300" for="deal_type">Deal Type:</label>
                        <select id="deal_type" class="bg-white dark:text-gray-300 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900">
                            <option value="">All</option>
                            <?php
                            $deal_types = [
                                '1171' => 'offplan',
                                '1169' => 'secondary'
                            ];
                            $selected_type = $_GET['deal_type'] ?? '';

                            foreach ($deal_types as $id => $type): ?>
                                <option value="<?= $id ?>" <?= $selected_type == $id ? 'selected' : '' ?>><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none" onclick="applyFilter()">Apply</button>
                        <?php if (isset($_GET['deal_type'])): ?>
                            <button onclick="clearFilter()" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <p class="ml-2">Clear</p>
                            </button>
                        <?php endif; ?>
                    </div>

                    <script>
                        function applyFilter() {
                            let deal_type = document.getElementById('deal_type').value;
                            let url = new URL(window.location.href);
                            url.searchParams.set('deal_type', deal_type);
                            window.location.href = url.toString();
                        }

                        function clearFilter() {
                            let url = new URL(window.location.href);
                            url.searchParams.delete('deal_type');
                            window.location.href = url.toString();
                        }
                    </script>

                    <div class="pb-4 rounded-lg border-0 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg">
                        <!-- Overall deals -->
                        <div class="relative rounded-lg border-b border-gray-200 dark:border-gray-700 w-full overflow-auto">
                            <table class="w-full h-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Date</th>
                                        <th scope="col" class="px-6 py-3">Transaction Type</th>
                                        <th scope="col" class="px-6 py-3">Deal Type</th>
                                        <th scope="col" class="px-6 py-3">Project Name</th>
                                        <th scope="col" class="px-6 py-3">Unit No</th>
                                        <th scope="col" class="px-6 py-3">Developer Name</th>
                                        <th scope="col" class="px-6 py-3">Property Type</th>
                                        <th scope="col" class="px-6 py-3">No of Bedrooms</th>
                                        <th scope="col" class="px-6 py-3">Client Name</th>
                                        <th scope="col" class="px-6 py-3">Agent Name</th>
                                        <th scope="col" class="px-6 py-3">Property Price</th>
                                        <th scope="col" class="px-6 py-3">Gross Commission (Incl. VAT)</th>
                                        <th scope="col" class="px-6 py-3">Gross Commission</th>
                                        <th scope="col" class="px-6 py-3">VAT</th>
                                        <th scope="col" class="px-6 py-3">Lead Source</th>
                                        <!-- <th scope="col" class="px-6 py-3">Team</th>
                                        <th scope="col" class="px-6 py-3">Mondus Commission</th>
                                        <th scope="col" class="px-6 py-3">Invoice Status</th>
                                        <th scope="col" class="px-6 py-3">Payment Received</th>
                                        <th scope="col" class="px-6 py-3">1st Payment Received</th>
                                        <th scope="col" class="px-6 py-3">2nd Payment Received</th>
                                        <th scope="col" class="px-6 py-3">3rd Payment Received</th>
                                        <th scope="col" class="px-6 py-3">Total Payment Received</th>
                                        <th scope="col" class="px-6 py-3">Amount Receivable</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($overall_deals as $deal): ?>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <?php echo $deal['Date']; ?>
                                            </th>
                                            <td class="px-6 py-4"><?php echo $deal['Transaction Type']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Deal Type']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Project Name']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Unit No']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Developer Name']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Property Type']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['No Of Br']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Client Name']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Agent Name']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Property Price']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Gross Commission (Incl. VAT)']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Gross Commission']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['VAT']; ?></td>
                                            <!-- <td class="px-6 py-4"><?php echo $deal['Team']; ?></td> 
                                            <td class="px-6 py-4"><?php echo $deal['Mondus Commission']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Lead Source']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Invoice Status']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Payment Received']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['1st Payment Received']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['2nd Payment Received']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['3rd Payment Received']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Total Payment Received']; ?></td>
                                            <td class="px-6 py-4"><?php echo $deal['Amount Receivable']; ?></td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination control -->
                        <?php if (!empty($overall_deals)): ?>
                            <?php include('includes/pagination_control.php'); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>