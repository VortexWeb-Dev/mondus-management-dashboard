<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include('includes/header.php');
include('includes/sidebar.php');

// include the api endpoint pages
include_once "./data/fetch_deals.php";
include_once "./data/fetch_users.php";

// import utils
include_once "./utils/index.php";

$all_deals = get_all_deals();
$deal_fields = get_deal_fileds();
$all_users = getUsers();
$user_fields = get_user_fields();

$current_user_id = getCurrentUser()['ID'];
// $filter = ['CLOSED' => 'Y'];
// $filter = ['ID' => $current_user_id,'STAGE_ID' => 'WON'];
$filter = ['CATEGORY_ID' => 0];

$select = [
    "ID",
    'BEGINDATE',
    'UF_CRM_1727625723908', // enum: transaction type
    'UF_CRM_1727625752721', // enum: deal type missing so using pipeline
    'UF_CRM_1727625779110', // string: project name
    'UF_CRM_1727625804043', // string: unit no
    'UF_CRM_1727625822094', // string: developer name
    'UF_CRM_66E3D8D1A13F7', // enum: property type
    'UF_CRM_1727854068559', // enum: no of br
    'UF_CRM_1727854143005', // string: client name
    'ASSIGNED_BY_ID', // string: agent ID
    'UF_CRM_1727854555607', // enum: team
    'OPPORTUNITY', // float: property price
    'UF_CRM_1727626089404', // string: commission slab
    'UF_CRM_1728042953037', // enum: referral approved status
    'UF_CRM_1727628203466', // if yes then show -> string: amount receivable
    'UF_CRM_1727854893657', // enum: lead source
    // doubt: documents upload ---------
    // booking form
    // pastport copy
    // EID
    // kyc
    // proof of address
    // down payment receipts
    // notification for agents until completing the required docs
    //--------------------------------
    'UF_CRM_1727626897246', // enum: down payment (completed)
    'UF_CRM_1727626932600', // enum: dld payment (completed)
    'UF_CRM_1727855585703', // enum: spa (executed)
    //then accounts should get an email notification
    'UF_CRM_1727855739514', // enum: deal status (completed)
    // There should be pop up to write the reason for cancellation and Accounts should get notification for the cancellation with the reason
];

$filtered_deals = getFilteredDeals($filter, $select);

$current_agent_deals = [];

foreach ($filtered_deals as $id => $deal) {
    $current_agent_deals[$id]['id'] = $deal['ID'];
    $current_agent_deals[$id]['date'] = date('Y-m-d', strtotime($deal['BEGINDATE']));

    if (isset($deal['UF_CRM_1727625723908'])) {
        $transactionType = map_enum($deal_fields, 'UF_CRM_1727625723908', $deal['UF_CRM_1727625723908']);
        $current_agent_deals[$id]['transaction_type'] = $transactionType ?? null;
    } else {
        $current_agent_deals[$id]['transaction_type'] = 'field_not_defined';
    }

    if (isset($deal['UF_CRM_1727625752721'])) {
        $dealType = map_enum($deal_fields, 'UF_CRM_1727625752721', $deal['UF_CRM_1727625752721']);
        $current_agent_deals[$id]['deal_type'] = $dealType ?? null;
    } else {
        $current_agent_deals[$id]['deal_type'] = 'field_not_defined';
    }

    $current_agent_deals[$id]['project_name'] = $deal['UF_CRM_1727625779110'] ?? null;
    $current_agent_deals[$id]['unit_no'] = $deal['UF_CRM_1727625804043'] ?? null;
    $current_agent_deals[$id]['developer_name'] = $deal['UF_CRM_1727625822094'] ?? null;
    if (isset($deal['UF_CRM_66E3D8D1A13F7'])) {
        $propertyType = map_enum($deal_fields, 'UF_CRM_66E3D8D1A13F7', $deal['UF_CRM_66E3D8D1A13F7']);
        $current_agent_deals[$id]['property_type'] = $propertyType ?? null;
    } else {
        $current_agent_deals[$id]['property_type'] = 'field_not_defined';
    }

    if (isset($deal['UF_CRM_1727854068559'])) {
        $noOfBr = map_enum($deal_fields, 'UF_CRM_1727854068559', $deal['UF_CRM_1727854068559']);
        $current_agent_deals[$id]['no_of_br'] = $noOfBr ?? null;
    } else {
        $current_agent_deals[$id]['no_of_br'] = 'field_not_defined';
    }

    $current_agent_deals[$id]['client_name'] = $deal['UF_CRM_1727854143005'] ?? null;

    if (isset($deal['ASSIGNED_BY_ID'])) {
        $agentId = $deal['ASSIGNED_BY_ID'];
        $currentAgent = getUser($agentId);
        $current_agent_deals[$id]['agent_full_name'] = $currentAgent['NAME'] ?? '' . ' ' . $currentAgent['SECOND_NAME'] ?? '' . ' ' . $currentAgent['LAST_NAME'] ?? '';
    } else {
        $current_agent_deals[$id]['agent_full_name'] = 'field_not_defined';
    }

    if (isset($deal['UF_CRM_1727854555607'])) {
        $teamName = map_enum($deal_fields, 'UF_CRM_1727854555607', $deal['UF_CRM_1727854555607']);
        $current_agent_deals[$id]['team'] = $teamName ?? null;
    } else {
        $current_agent_deals[$id]['team'] = 'field_not_defined';
    }

    $current_agent_deals[$id]['property_price'] = $deal['OPPORTUNITY'] ?? null;
    $current_agent_deals[$id]['commission_slab'] = $deal['UF_CRM_1727626089404'] ?? null;

    if (isset($deal['UF_CRM_1728042953037'])) {
        $current_agent_deals[$id]['referral'] = map_enum($deal_fields, 'UF_CRM_1728042953037', $deal['UF_CRM_1728042953037']) ?? null;
        // show the amount receivable if the referral is approved
        if ($deal['UF_CRM_1728042953037'] == 1295) {
            $current_agent_deals[$id]['amount'] = $deal['UF_CRM_1727628203466'] ?? null;
        } else {
            $current_agent_deals[$id]['amount'] = 'Approve Referral First';
        }
    } else {
        $current_agent_deals[$id]['referral'] = 'field_not_defined';
        $current_agent_deals[$id]['amount'] = 'Approve Referral First';
    }

    if (isset($deal['UF_CRM_1727854893657'])) {
        $leadSource = map_enum($deal_fields, 'UF_CRM_1727854893657', $deal['UF_CRM_1727854893657']);
        $current_agent_deals[$id]['lead_source'] = $leadSource ?? null;
    } else {
        $current_agent_deals[$id]['lead_source'] = 'field_not_defined';
    }

    // $current_agent_deals[$id]['documents_upload'] = $deal['UF_CRM_1727855739514'];

    if (isset($deal['UF_CRM_1727626897246'])) {
        $downPayment = map_enum($deal_fields, 'UF_CRM_1727626897246', $deal['UF_CRM_1727626897246']);
        $current_agent_deals[$id]['down_payment'] = $downPayment ?? null;
    } else {
        $current_agent_deals[$id]['down_payment'] = 'field_not_defined';
    }

    if (isset($deal['UF_CRM_1727626932600'])) {
        $dldPayment = map_enum($deal_fields, 'UF_CRM_1727626932600', $deal['UF_CRM_1727626932600']);
        $current_agent_deals[$id]['dld_payment'] = $dldPayment ?? null;
    } else {
        $current_agent_deals[$id]['dld_payment'] = 'field_not_defined';
    }

    if (isset($deal['UF_CRM_1727855585703'])) {
        $spa = map_enum($deal_fields, 'UF_CRM_1727855585703', $deal['UF_CRM_1727855585703']);
        $current_agent_deals[$id]['spa'] = $spa ?? null;
    } else {
        $current_agent_deals[$id]['spa'] = 'field_not_defined';
    }

    if (isset($deal['UF_CRM_1727855739514'])) {
        $dealStatus = map_enum($deal_fields, 'UF_CRM_1727855739514', $deal['UF_CRM_1727855739514']);
        $current_agent_deals[$id]['deal_status'] = $dealStatus ?? null;
    } else {
        $current_agent_deals[$id]['deal_status'] = 'field_not_defined';
    }
}

echo "<pre>";
// print_r($filtered_deals);
echo "</pre>";

?>

<div class="main-content-area bg-gray-100 dark:bg-gray-900">
    <?php include('includes/navbar.php'); ?>
    <div class="px-8 py-6">
        <p class="text-2xl font-bold dark:text-white mb-4">Deals Completion Progress</p>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Date</th>
                        <th scope="col" class="px-6 py-3">Transaction Type</th>
                        <th scope="col" class="px-6 py-3">Deal Type</th>
                        <th scope="col" class="px-6 py-3">Project Name</th>
                        <th scope="col" class="px-6 py-3">Unit No</th>
                        <th scope="col" class="px-6 py-3">Developer Name</th>
                        <th scope="col" class="px-6 py-3">Type</th>
                        <th scope="col" class="px-6 py-3">No Of BR</th>
                        <th scope="col" class="px-6 py-3">Client Name</th>
                        <th scope="col" class="px-6 py-3">Agent Name</th>
                        <th scope="col" class="px-6 py-3">Team</th>
                        <th scope="col" class="px-6 py-3">Property Price</th>
                        <th scope="col" class="px-6 py-3">Commission Slab (%)</th>
                        <th scope="col" class="px-6 py-3">Referral</th>
                        <th scope="col" class="px-6 py-3">If Yes, Amount</th>
                        <th scope="col" class="px-6 py-3">Lead Source</th>
                        <th scope="col" class="px-6 py-3">Documents Upload</th>
                        <th scope="col" class="px-6 py-3">Down payment</th>
                        <th scope="col" class="px-6 py-3">DLD Payment</th>
                        <th scope="col" class="px-6 py-3">SPA</th>
                        <th scope="col" class="px-6 py-3">Deal Status</th>
                        <th scope="col" class="px-6 py-3">Notification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($current_agent_deals as $deal): ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <?= $deal['date'] ?? '--' ?>
                            </th>
                            <td class="px-6 py-4">
                                <?= $deal['transaction_type'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['deal_type'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['project_name'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['unit_no'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['developer_name'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['type'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['no_of_br'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['client_name'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['agent_full_name'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['team'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['property_price'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['commission_slab'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['referral'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['amount'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['lead_source'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['documents_upload'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['down_payment'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['dld_payment'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['spa'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['deal_status'] ?? '--' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $deal['notification'] ?? '--' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>