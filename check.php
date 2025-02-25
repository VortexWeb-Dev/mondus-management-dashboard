<?php
session_start();

include_once 'crest/crest.php';

// Define the pipeline ID
define("SUPRAJA_PIPELINE_ID", 23); //  

function fetchDeals() {
    $deals = CRest::call('crm.deal.list', [
        'filter' => ['CATEGORY_ID' => SUPRAJA_PIPELINE_ID,23], // Use the defined pipeline ID
        'select' => [
            '', 'UF_'
        ]
    ]);

    // Extract only the deals' results, if available
    $dealResults = $deals['result'] ?? [];

    // echo '<pre>';
    // print_r($dealResults); // Display only the deals data
    // echo '</pre>';
    
    return $dealResults;
}


function calculateTotals($deals)
{
    $dealsToday = 0;
    $totalAmount = 0;
    $referralAmountToday = 0;
    $today = date('Y-m-d');  // Today's date in Y-m-d format

    foreach ($deals as $deal) {
        // Extract and validate close date of the deal
        $closeDate = isset($deal['CLOSEDATE']) ? date('Y-m-d', strtotime($deal['CLOSEDATE'])) : null;

        // Debugging: Log the close date for each deal
        error_log("Deal Close Date: " . $closeDate);

        // Add to total property price
        $propertyPrice = isset($deal[' UF_CRM_1729499970282']) ? floatval($deal[' UF_CRM_1729499970282']) : 0;
        $totalAmount += $propertyPrice;

        // Debugging: Log the property price
        error_log("Property Price: " . $propertyPrice);

        // Check if the deal was closed today
        if ($closeDate === $today) {
            $dealsToday++;  // Increment count of deals closed today

            // Check if referral fee is approved (709 means approved, as per your condition)
            if (isset($deal['UF_CRM_1729500007347']) && $deal['UF_CRM_1729500007347'] == 709) {
                // Add the referral fee amount for today
                $referralFeeAmount = isset($deal['UF_CRM_1729500007347']) ? floatval($deal['UF_CRM_1729500007347']) : 0;
                $referralAmountToday += $referralFeeAmount;

                // Debugging: Log the referral fee amount added
                error_log("Referral Amount Today: " . $referralFeeAmount);
            }
        }
    }

    // Return the calculated totals as an associative array
    return [
        'dealsToday' => $dealsToday,
        'totalAmount' => $totalAmount,
        'referralAmountToday' => $referralAmountToday
    ];
}

$deals = fetchDeals();
$totals = calculateTotals($deals);

// Filtering by status
$statusFilter = $_GET['status'] ?? 'all';
if ($statusFilter !== 'all') {
    $deals = array_filter($deals, function($deal) use ($statusFilter) {
        return ($statusFilter === 'approved' && $deal['UF_CRM_1729500007347'] == 709) || 
               ($statusFilter === 'rejected' && $deal['UF_CRM_1729500007347'] == 711) || 
               ($statusFilter === 'not_selected' && $deal['UF_CRM_1729500007347'] == null);
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dealId = $_POST['dealId'] ?? null;
    $referralFeeId = $_POST['referralFeeId'] ?? null;

    if ($dealId && $referralFeeId && in_array($referralFeeId, [709, 711])) {
        $result = CRest::call('crm.item.update', [
            'entityTypeId' => 1256,
            'id' => $dealId,
            'fields' => ['UF_CRM_1729500007347' => $referralFeeId]
        ]);
        
        // Check result and set session message
        if (isset($result['result']) && $result['result']) {
            $_SESSION['message'] = "Referral fee updated successfully.";
        } else {
            $errorMessage = isset($result['error']) ? $result['error'] : 'Unknown error';
            error_log("Failed to update referral fee. Error: " . htmlspecialchars($errorMessage));
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}



// Retrieve the message and clear it from the session
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Application - Home</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
        }

        .container {
            flex: 1;
            max-width: 1200px;
            margin: 20px auto;
            padding-left: 220px; /* Leave space for the sidebar */
        }

        h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }

        .totals {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .totals div {
            font-size: 16px; /* Adjusted font size for better visibility */
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #20c997;
            color: white;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #f8f9fa;
        }

        button {
            padding: 10px 15px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn-blue {
            background-color: #007bff;
        }

        .btn-blue:hover {
            background-color: #0056b3;
        }

        .btn-red {
            background-color: #dc3545;
        }

        .btn-red:hover {
            background-color: #b02a37;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        @media screen and (max-width: 768px) {
            .totals {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3></h3>
        <a href="?status=not_selected">All Deals</a>
        <a href="?status=approved">Approved Deals</a>
        <a href="?status=rejected">Rejected Deals</a>
        
    </div>

    <div class="container">
        <!-- Display totals -->
        <div class="totals">
            <div>Deals Today: <span><?php echo $totals['dealsToday']; ?></span></div>
            <div>Total Amount: <span><?php echo number_format($totals['totalAmount'], 2); ?></span></div>
            <div>Referral Amount Approved Today: <span><?php echo number_format($totals['referralAmountToday'], 2); ?></span></div>
        </div>

        <h2>Current Deals</h2>
        <table>
            <thead>
                <tr>
                    <th>Deal Name</th>
                    <th>Responsible Person</th>
                    <th>Project Name</th>
                    <th>Unit Number</th>
                    <th>Property Price</th>
                    <th>Gross Commission</th>
                    <th>Referral Fee</th>
                    <th>Referral</th>
                    <th>Action</th>
                    <th>Referral Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($deals as $deal):
                    // Initialize referral fee status
                    $referralFeeStatus = 'Not Selected';

                    // Check if referral fee exists and set the status
                    if (isset($deal['UF_CRM_1729500007347'])) {
                        if ($deal['UF_CRM_1729500007347'] == 717) {
                            $referralFeeStatus = 'Approved';
                        } elseif ($deal['UF_CRM_1729500007347'] == 719) {
                            $referralFeeStatus = 'Rejected';
                        }
                    }
                ?>
                <tr>
                    <td><input type="text" name="DealName" value="<?php echo htmlspecialchars($deal['UF_CRM_1729499879083'] ?? ''); ?>" readonly></td>
                    <td><input type="text" name="Responsibleperson" value="<?php echo htmlspecialchars($deal['UF_CRM_1729499903050'] ?? ''); ?>" readonly></td>
                    <td><input type="text" name="ProjectName" value="<?php echo htmlspecialchars($deal['UF_CRM_1729499931812'] ?? ''); ?>" readonly></td>
                    <td><input type="text" name="UnitNumber" value="<?php echo htmlspecialchars($deal['UF_CRM_1729499952106'] ?? ''); ?>" readonly></td>
                    <td><input type="number" name="PropertyPrice" value="<?php echo htmlspecialchars($deal['UF_CRM_1729499970282'] ?? ''); ?>" readonly></td>
                    <td><input type="number" name="GrossCommission" value="<?php echo htmlspecialchars($deal['UF_CRM_1729499988571'] ?? ''); ?>" readonly></td>
                    <td><input type="text" name="ReferralFee" value="<?php echo htmlspecialchars($deal['UF_CRM_1729500007347'] ?? ''); ?>" readonly></td>
                    
                    <td>
                        <input type="text" name="Referral" 
                               value="<?php 
                                   echo htmlspecialchars($referralFeeStatus);
                               ?>" 
                               readonly>
                    </td>

                    <td>
                    <div class="action-buttons">
                    <form method="post" action="./index.php">
        <input type="hidden" name="dealId" value="<?php echo $deal['id']; ?>">
        <input type="hidden" name="referralFeeId" value="717">
        <button type="submit" class="btn-blue">Approve</button>
    </form>
    <form method="post" action="./index.php" style="display:inline;">
        <input type="hidden" name="dealId" value="<?php echo $deal['id']; ?>">
        <input type="hidden" name="referralFeeId" value="719">
        <button type="submit" class="btn-red">Reject</button>
    </form>
    </div>

                        </div>
                    </td>
                    <td><input type="text" name="Referral Comments" value="<?php echo htmlspecialchars($deal['UF_CRM_1729500122690'] ?? ''); ?>" readonly></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($message): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
