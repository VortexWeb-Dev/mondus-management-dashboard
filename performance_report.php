<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="p-10 w-[80%]">
    <h1 class="text-2xl font-bold mb-6 text-center">July 2024 Performance Evaluation</h1>
    <h2 class="text-xl font-semibold mb-4 text-center">Ahmed Godil</h2>

    <!-- Form Begins -->
    <form class="space-y-6">
        <!-- Marketing Leads -->
        <div>
            <label class="block font-medium">No. of Marketing Leads Received (Including repeat inquiries)</label>
            <input type="text" value="20" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block font-medium">Active Leads</label>
            <input type="text" value="5" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div>
            <label class="block font-medium">Unqualified/Reassigned Leads</label>
            <input type="text" value="15" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <!-- Hubspot Lead Update Statistics -->
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-2">Hubspot Lead Update Statistics (as of 3 July 2024)</h3>

            <div>
                <label class="block font-medium">Leads without Update for more than 2 weeks</label>
                <input type="text" value="2 leads no update for 2 weeks" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block font-medium">New Deals Created in Hubspot (July 2024)</label>
                <input type="text" value="no new deals in the pipeline" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block font-medium">No. of Meetings Conducted in Hubspot (July 2024)</label>
                <input type="text" value="no new meeting logged in Hubspot" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- PropertyFinder Ads and Leads -->
        <div>
            <h3 class="text-lg font-semibold mb-2">PropertyFinder Ads and Leads (as of 5 July 2024)</h3>

            <div>
                <label class="block font-medium">No. Live Ads</label>
                <input type="text" value="3" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block font-medium">No. of Leads Received from PropertyFinder Ads</label>
                <input type="text" value="5" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- Deals in 2024 -->
        <div>
            <h3 class="text-lg font-semibold mb-2">Deals in 2024</h3>

            <table class="min-w-full bg-white border">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Unit Details</th>
                        <th class="border px-4 py-2">Property Price</th>
                        <th class="border px-4 py-2">Type</th>
                        <th class="border px-4 py-2">Net Commission</th>
                        <th class="border px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border px-4 py-2">B101Z-F34-3414</td>
                        <td class="border px-4 py-2">AED 2,019,000.00</td>
                        <td class="border px-4 py-2">Sale</td>
                        <td class="border px-4 py-2">AED 50,475.00</td>
                        <td class="border px-4 py-2">29-Feb</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">Silicon Gates 4</td>
                        <td class="border px-4 py-2">AED 512,000.00</td>
                        <td class="border px-4 py-2">Sale</td>
                        <td class="border px-4 py-2">AED 2,560.00</td>
                        <td class="border px-4 py-2">12-Mar</td>
                    </tr>
                    <!-- Add other rows similarly -->
                </tbody>
            </table>
        </div>

        <!-- Summary Information -->
        <div class="mt-6">
            <div>
                <label class="block font-medium">Total Earnings Year to Date</label>
                <input type="text" value="AED 103,890.00" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block font-medium">Total Worth of Properties Sold Year to Date</label>
                <input type="text" value="AED 6,420,000.00" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block font-medium">Average Monthly Earnings</label>
                <input type="text" value="AED 14,841.43" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <!-- Ranking Information -->
        <div class="mt-6">
            <div>
                <label class="block font-medium">Rank for July 2024</label>
                <input type="text" value="N/A (No Deal in July)" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block font-medium">YTD Ranking</label>
                <input type="text" value="52" class="mt-1 p-2 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>