<?php
include_once __DIR__ . "/crest/crest.php";
include_once __DIR__ . "/crest/settings.php";
include('includes/header.php');

// include the fetch deals page
include_once __DIR__ . "/data/fetch_deals.php";
include_once __DIR__ . "/data/fetch_users.php";

include_once __DIR__ . "/controllers/calculate_agent_rank.php";

$global_ranking = calculateAgentRank();

//get the filter data from get request
$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');

$selected_agent_id = isset($_GET['agent_id']) ? $_GET['agent_id'] : null;

$current_agent = getUser($selected_agent_id);
$current_agent_name = $current_agent['NAME'] ?? '' . ' ' . $current_agent['LAST_NAME'] ?? '' . ' ( ID: ' . $current_agent['ID'] . ')';

function getMothwiseFilteredRankings($monthwise_rank_data, $selected_agent_id)
{
    $ranking = [
        // 'id' => [
        //     'name' => 'Name',
        //     'rankings' => [
        //         'Jan' => [
        //             'gross_comms' => 0,
        //             'rank' => 0
        //         ],
        //     ]
        // ]
    ];

    foreach ($monthwise_rank_data as $month => $agents) {
        foreach ($agents as $agent_id => $agent) {
            if ($agent_id == $selected_agent_id) {
                //set the name only once
                if (!isset($ranking[$agent_id]['name'])) {
                    $ranking[$agent_id]['name'] = $agent['name'];
                }
                $ranking[$agent_id]['rankings'][$month]['gross_comms'] = $agent['gross_comms'];
                $ranking[$agent_id]['rankings'][$month]['rank'] = $agent['rank'];
            }
        }
    }

    return $ranking;
}
function getQuaterlyFilteredRankings($quarterly_rank_data, $selected_agent_id)
{
    $ranking = [
        // 'id' => [
        //     'name' => 'Name',
        //     'rankings' => [
        //         'Q1' => [
        //             'gross_comms' => 0,
        //             'rank' => 0
        //         ],
        //     ]
        // ]
    ];

    foreach ($quarterly_rank_data as $quater => $agents) {
        foreach ($agents as $agent_id => $agent) {
            if ($agent_id == $selected_agent_id) {
                //set the name only once
                if (!isset($ranking[$agent_id]['name'])) {
                    $ranking[$agent_id]['name'] = $agent['name'];
                }
                $ranking[$agent_id]['rankings'][$quater]['gross_comms'] = $agent['gross_comms'];
                $ranking[$agent_id]['rankings'][$quater]['rank'] = $agent['rank'];
            }
        }
    }

    return $ranking;
}
function getYearlyFilteredRankings($global_ranking, $selected_agent_id)
{
    $ranking = [
        // 'id' => [
        //     'name' => 'Name',
        //     'rankings' => [
        //         '2024' => [
        //             'gross_comms' => 0,
        //             'rank' => 0
        //         ],
        //     ]
        // ]
    ];

    foreach ($global_ranking as $year => $year_data) {
        foreach ($year_data['yearly_rank'] as $agent_id => $agent) {
            if ($agent_id == $selected_agent_id) {
                //set the name only once
                if (!isset($ranking[$agent_id]['name'])) {
                    $ranking[$agent_id]['name'] = $agent['name'];
                }
                $ranking[$agent_id]['rankings'][$year]['gross_comms'] = $agent['gross_comms'];
                $ranking[$agent_id]['rankings'][$year]['rank'] = $agent['rank'];
            }
        }
    }

    return $ranking;
}

$monthwise_ranked_agents = [];
$quarterly_ranked_agents = [];
$yearly_ranked_agents = [];

if (isset($global_ranking[$selected_year]['monthwise_rank'])) {
    $monthwise_ranked_agents = getMothwiseFilteredRankings($global_ranking[$selected_year]['monthwise_rank'], $selected_agent_id) ?? [];
}

if (isset($global_ranking[$selected_year]['quarterly_rank'])) {
    $quarterly_ranked_agents = getQuaterlyFilteredRankings($global_ranking[$selected_year]['quarterly_rank'], $selected_agent_id) ?? [];
}

$yearly_ranked_agents = getYearlyFilteredRankings($global_ranking, $selected_agent_id) ?? [];

echo "<pre>";
// print_r($monthwise_ranked_agents);
// print_r($quarterly_ranked_agents);
// print_r($yearly_ranked_agents);
echo "</pre>";
?>

<div class="flex w-full h-screen">
    <?php include('includes/sidebar.php'); ?>
    <div class="main-content-area overflow-y-auto w-full bg-gray-100 dark:bg-gray-900">
        <?php include('includes/navbar.php'); ?>

        <div class="px-8 py-6">
            <?php include('./includes/datepicker.php'); ?>
            <?php if ($selected_agent_id): ?>
                <h1 class="text-xl text-center font-bold mb-4 dark:text-gray-200"><?= $current_agent_name ?>'s Rankings</h1>
            <?php endif; ?>
            <div class="mx-auto">
                <!-- agent searchbox -->
                <div class="pb-4">
                    <?php include('includes/select_agents.php'); ?>
                </div>
                <!-- main content -->
                <?php if ($selected_agent_id): ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Monthly Ranking -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-[400px] flex flex-col gap-1">
                            <div class="flex justify-between gap-2">
                                <h2 class="text-xl font-semibold mb-6 dark:text-white">Monthly Ranking</h2>
                                <div class="flex items-start justify-end gap-2">
                                    <a href="#" onclick="download_table_as_csv('monthly_rankings');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="monthly-agent" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white"><?= $agent_name ?></label>
                                <input type="text" id="monthly-agent" name="monthly-agent" class="mt-1 block w-full border-b border-gray-600 dark:bg-gray-800">
                            </div>
                            <?php if (empty($monthwise_ranked_agents)): ?>
                                <p class="text-gray-600 dark:text-gray-400">No data available.</p>
                            <?php else: ?>
                                <div class="overflow-auto">
                                    <table id="monthly_rankings" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Month</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rank</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gross Comm</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700">
                                            <?php foreach ($monthwise_ranked_agents[$selected_agent_id]['rankings'] as  $month_name => $month): ?>
                                                <tr class="whitespace-nowrap text-sm font-medium hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $month_name ?></td>
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $month['rank'] ?></td>
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $month['gross_comms'] ?> AED</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Quaterly Ranking -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-[400px] flex flex-col gap-1">
                            <div class="flex justify-between gap-2">
                                <h2 class="text-xl font-semibold mb-6 dark:text-white">Quaterly Ranking</h2>
                                <div class="flex items-start justify-end gap-2">
                                    <a href="#" onclick="download_table_as_csv('quarterly_rankings');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="monthly-agent" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white"><?= $agent_name ?></label>
                                <input type="text" id="quaterly-agent" name="quaterly-agent" class="mt-1 block w-full border-b border-gray-600 dark:bg-gray-800">
                            </div>
                            <!-- Add more content for quaterly ranking here -->
                            <?php if (empty($quarterly_ranked_agents)): ?>
                                <p class="text-gray-600 dark:text-gray-400">No data available.</p>
                            <?php else: ?>
                                <div class="overflow-auto">
                                    <table id="quarterly_rankings" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quarter</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rank</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gross Comm</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700">
                                            <?php foreach ($quarterly_ranked_agents[$selected_agent_id]['rankings'] as $quarter_name => $quarter): ?>
                                                <tr class="whitespace-nowrap text-sm font-medium hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200 font-medium"><?= $quarter_name ?></td>
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $quarter['rank'] ?></td>
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $quarter['gross_comms'] ?> AED</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Yearly Ranking -->
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-[400px] flex flex-col gap-1">
                            <div class="flex justify-between gap-2">
                                <h2 class="text-xl font-semibold mb-6 dark:text-white">Yearly Ranking</h2>
                                <div class="flex items-start justify-end gap-2">
                                    <a href="#" onclick="download_table_as_csv('yearly_rankings');" class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-500 transition duration-300" title="Download as CSV">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="monthly-agent" class="block text-sm font-medium text-gray-700 mb-2 dark:text-white"><?= $agent_name ?></label>
                                <input type="text" id="yearly-agent" name="yearly-agent" class="mt-1 block w-full border-b border-gray-600 dark:bg-gray-800">
                            </div>
                            <!-- Add more content for yearly ranking here -->
                            <?php if (empty($yearly_ranked_agents)): ?>
                                <p class="text-gray-600 dark:text-gray-400">No data available.</p>
                            <?php else: ?>
                                <div class="overflow-auto">
                                    <table id="yearly_rankings" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Year</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rank</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700">
                                            <?php foreach ($yearly_ranked_agents[$selected_agent_id]['rankings'] as $year_name => $year): ?>
                                                <tr class="whitespace-nowrap text-sm font-medium hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200 font-medium"><?= $year_name ?></td>
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $year['rank'] ?></td>
                                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-200"><?= $year['gross_comms'] ?> AED</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center justify-center h-96">
                        <p class="text-xl text-gray-500 dark:text-gray-400 font-medium">Select an agent to view rank</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<?php include('includes/footer.php'); ?>