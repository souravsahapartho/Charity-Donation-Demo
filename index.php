<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UniDonate: A Unified, real-time charity donation and management system for financial, food, and goods contributions in Bangladesh.">
    <title>UniDonate - Unified Charity Donation System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
        }

        /* Standardized Button Animation */
        .dynamic-btn {
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
        }
        .dynamic-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        /* Header Animation */
        .pulsing-heart {
            animation: pulse 1.5s infinite ease-in-out;
            color: #ef4444; /* Red-500 */
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        /* Loader Animation */
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #06B6D4; /* Cyan-500 */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Modal Pop-In Animation */
        @keyframes modal-pop {
            0% { opacity: 0; transform: scale(0.8) translateY(-20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-content {
            animation: modal-pop 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Quote Transition */
        .quote-slide {
            transition: opacity 0.5s ease-in-out;
        }

        /* Charity Card Hover Effect */
        .charity-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .charity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Matching Highlight */
        .match-highlight {
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.8); /* Strong Cyan glow */
            border-color: #06B6D4;
        }

        /* Hide the default radio/checkbox */
        .tab-radio {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-hand-holding-heart text-2xl text-teal-600"></i>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800">UniDonate Platform</h1>
                <div class="text-2xl hidden md:block">
                    <i class="fas fa-heart pulsing-heart"></i>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Donor Notifications (Visible only in Donor View) -->
                <button id="notification-button" class="relative text-gray-600 hover:text-teal-600 transition-colors hidden" onclick="window.toggleNotifications()">
                    <i class="fas fa-bell text-xl"></i>
                    <span id="notification-count" class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center opacity-0 transition-opacity">0</span>
                </button>
                
                <!-- Role Toggle Button -->
                <button id="role-toggle-btn" class="dynamic-btn bg-teal-600 text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-lg hover:bg-teal-700 transition-colors" onclick="window.toggleRole()">
                    Switch to Staff/Admin Login
                </button>
            </div>
        </div>
        <!-- Notification Panel -->
        <div id="notification-panel" class="absolute right-4 mt-2 w-72 bg-white rounded-lg shadow-xl z-50 border border-gray-200 hidden">
            <div class="p-3 border-b font-semibold text-gray-700">Recent Confirmed Donations</div>
            <div id="notification-list" class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                <!-- Notifications load here -->
                <p class="p-3 text-gray-500 text-sm italic text-center">No new confirmed donations.</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Role Display Banner -->
        <div id="role-banner" class="mb-6 p-4 rounded-lg text-center font-bold text-lg shadow-md transition-colors">
            <!-- Content updated by JavaScript -->
        </div>

        <!-- LOADER SCREEN -->
        <div id="app-loader" class="fixed inset-0 bg-white z-[101] flex flex-col items-center justify-center transition-opacity duration-500">
            <div class="loader"></div>
            <p class="mt-4 text-xl font-semibold text-gray-700">Connecting to UniDonate System...</p>
        </div>

        <!-- APPLICATION VIEWS (Hidden until loaded) -->
        <div id="app-content" class="hidden">

            <!-- Donor View -->
            <div id="donor-view">
                
                <!-- NEW: Global Statistics Banner (Injected by JS) -->
                <div id="stats-banner" class="mb-8">
                    <!-- Statistics will be injected here by renderStats() -->
                </div>

                <div class="grid lg:grid-cols-3 gap-8 mb-8">
                    <!-- Column 1: Quotes -->
                    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg border-t-4 border-teal-500 flex flex-col justify-center">
                        <h2 class="text-xl font-bold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-hands-helping text-teal-500 mr-2"></i>
                            Inspiring Humanity
                        </h2>
                        <div id="quote-section" class="text-center h-20 flex items-center justify-center">
                            <!-- Quote loads here -->
                            <p class="text-lg italic text-gray-600 quote-slide">Loading motivation...</p>
                        </div>
                    </div>

                    <!-- Column 2 & 3: Donation Input Form -->
                    <div class="lg:col-span-2">
                        <!-- Donation Form -->
                        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-500">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Submit Your Contribution</h2>
                            
                            <form id="donation-form" onsubmit="window.handleDonationSubmit(event)">
                                
                                <!-- Charity Selection Dropdown -->
                                <div class="mb-6">
                                    <label for="target-charity-id" class="block text-sm font-medium text-gray-700 mb-1">Select Charity Partner</label>
                                    <select name="targetCharityId" id="target-charity-id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-shadow" required>
                                        <!-- Options populated by JS -->
                                    </select>
                                </div>
                                <!-- END DROPDOWN -->

                                <div class="flex space-x-2 border-b mb-6">
                                    <button type="button" class="tab-button px-4 py-2 text-sm font-medium rounded-t-lg transition-colors" data-type="financial" onclick="window.selectDonationType('financial')">Financial</button>
                                    <button type="button" class="tab-button px-4 py-2 text-sm font-medium rounded-t-lg transition-colors" data-type="food" onclick="window.selectDonationType('food')">Food Items</button>
                                    <button type="button" class="tab-button px-4 py-2 text-sm font-medium rounded-t-lg transition-colors" data-type="goods" onclick="window.selectDonationType('goods')">Clothing/Supplies</button>
                                </div>

                                <!-- Financial Donation Tab -->
                                <div id="financial-tab" class="donation-tab space-y-4 hidden">
                                    <input type="hidden" name="donationType" value="financial">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Donation Amount (BDT)</label>
                                        <input type="number" name="amount" id="financial-amount" placeholder="e.g., 500 BDT" min="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow" required>
                                    </div>
                                </div>

                                <!-- Food Donation Tab -->
                                <div id="food-tab" class="donation-tab space-y-4 hidden">
                                    <input type="hidden" name="donationType" value="food">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Food Item Details</label>
                                        <textarea name="items" id="food-items" rows="3" placeholder="e.g., 50 kg Rice, 10 kg Lentils, Fresh vegetables" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow" required disabled></textarea>
                                    </div>
                                </div>

                                <!-- Goods Donation Tab -->
                                <div id="goods-tab" class="donation-tab space-y-4 hidden">
                                    <input type="hidden" name="donationType" value="goods">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Goods/Supplies Details</label>
                                        <textarea name="items" id="goods-items" rows="3" placeholder="e.g., 20 Winter blankets (New), 5 boxes of children's books" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-shadow" required disabled></textarea>
                                    </div>
                                </div>
                                
                                <button type="submit" class="dynamic-btn w-full mt-6 bg-teal-600 text-white font-semibold py-3 rounded-lg hover:bg-teal-700 transition-colors">
                                    Confirm & Log Donation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Charity Needs Section -->
                <div class="mt-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-map-marker-alt text-red-500 mr-3"></i>
                        Charity Partners & Urgent Needs (Real-Time)
                    </h2>
                    <div id="charity-needs-list" class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <!-- Charity cards load here -->
                        <p class="text-gray-600 col-span-full">Waiting for real-time charity needs data...</p>
                    </div>
                </div>

                <!-- Donation Tracking & Audit -->
                <div class="mt-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-receipt text-blue-500 mr-3"></i>
                        My Donation Tracking & Audit
                    </h2>
                    <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details / Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="my-donations-table" class="bg-white divide-y divide-gray-200">
                                <!-- My donations load here -->
                                <tr>
                                    <td colspan="4" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No donations tracked yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Staff/Admin Login View -->
            <div id="login-view" class="hidden max-w-md mx-auto bg-white p-8 rounded-xl shadow-2xl border-t-4 border-red-600">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Staff / Admin Login</h2>
                <form id="login-form">
                    <div class="mb-4">
                        <label for="login-id" class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                        <input type="text" id="login-id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-shadow" placeholder="e.g., admin or brac" required>
                    </div>
                    <div class="mb-6">
                        <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="login-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition-shadow" placeholder="password" required>
                    </div>
                    <button type="submit" class="dynamic-btn w-full bg-red-600 text-white font-semibold py-3 rounded-lg hover:bg-red-700 transition-colors">
                        Log In
                    </button>
                    <p class="mt-4 text-sm text-gray-500 text-center">Admin ID: `admin`, Pass: `uniadmin`</p>
                    <p class="text-sm text-gray-500 text-center">Charity IDs: `as_sunnah`, `brac`, `bidyanondo` etc., Pass: e.g., `brac123`</p>
                </form>
            </div>

            <!-- Admin Portal View -->
            <div id="admin-view" class="hidden">
                <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-red-600">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                        <i class="fas fa-user-shield text-red-600 mr-2"></i>
                        Admin Portal: Pending Donations
                    </h2>
                    
                    <!-- Admin Action Confirmation Message -->
                    <div id="admin-action-message" class="mb-4 hidden p-3 rounded-lg border text-sm transition-opacity"></div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details / Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charity ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody id="pending-donations-table" class="bg-white divide-y divide-gray-200">
                                <!-- Pending donations load here -->
                                <tr>
                                    <td colspan="5" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No pending donations to review.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Audit History -->
                <div class="mt-8 bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        Audit History (Confirmed Donations)
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Confirmed</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details / Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charity ID</th>
                                </tr>
                            </thead>
                            <tbody id="audit-history-table" class="bg-white divide-y divide-gray-200">
                                <!-- Audit history loads here -->
                                <tr>
                                    <td colspan="4" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No history recorded yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Charity Partner Portal View -->
            <div id="charity-portal-view" class="hidden max-w-xl mx-auto bg-white p-8 rounded-xl shadow-2xl border-t-4 border-teal-600">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-teal-600 mr-3"></i>
                    Charity Partner Portal
                </h2>
                <div id="charity-info" class="text-center mb-6">
                    <!-- Charity name and ID load here -->
                </div>
                <form id="update-needs-form">
                    <div class="mb-4">
                        <label for="needs-update" class="block text-sm font-medium text-gray-700 mb-1">Update Urgent Needs List</label>
                        <textarea id="needs-update" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 transition-shadow" placeholder="Enter a comma-separated list of your current urgent needs (e.g., Winter coats, $5000 for flood relief, Rice bags)"></textarea>
                    </div>
                    <button type="submit" class="dynamic-btn w-full bg-teal-600 text-white font-semibold py-3 rounded-lg hover:bg-teal-700 transition-colors">
                        Publish Needs Update
                    </button>
                    <p class="mt-4 text-xs text-gray-500 text-center">Updates are reflected instantly on the Donor Dashboard.</p>
                </form>
            </div>
        </div>
    </main>

    <!-- Modal Popup for Notifications/Confirmations -->
    <div id="modal-backdrop" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-[100] hidden transition-opacity" onclick="window.hideModal()">
        <div id="modal-container" class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-sm modal-content transition-transform" onclick="event.stopPropagation()">
            <div id="modal-header" class="p-4 flex items-center justify-between text-white">
                <h3 id="modal-title" class="text-lg font-semibold flex items-center space-x-2"></h3>
                <button onclick="window.hideModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <p id="modal-message" class="text-gray-700"></p>
                <button onclick="window.hideModal()" class="dynamic-btn mt-6 w-full py-2 rounded-lg text-white font-semibold transition-colors" id="modal-close-button"></button>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-100 py-4 mt-8 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-600">
            <p>&copy; 2024 UniDonate Platform. All Rights Reserved.</p>
            <p class="mt-1 font-semibold text-gray-700">Powered by PHP & MySQL (via XAMPP)</p>
        </div>
    </footer>

    <!-- JavaScript Module (PHP/MySQL API Integration) -->
    <script>
        
        let userRole = 'donor'; // 'donor', 'charity', 'admin'
        let currentUserId = 'anonymous-' + Math.random().toString(36).substring(2, 9); // Anonymous user ID
        
        // Global data stores (populated by API calls)
        window.charityNeeds = [];
        window.donorDonations = [];
        window.pendingDonations = [];
        window.auditHistory = [];
        window.lastNotificationCount = 0;

        // Mock Charity Data for local login checking (should ideally be fetched/checked by PHP)
        const MOCK_CHARITIES = [
            { id: 'brac', name: 'BRAC', region: 'Nationwide', needs: 'Microfinance aid, $100000 for education, Sanitary pads', password: 'brac123' },
            { id: 'bidyanondo', name: 'Bidyanondo Foundation', region: 'Dhaka', needs: 'School supplies, Rice bags, Volunteer funding', password: 'bidyanondo123' },
            { id: 'as_sunnah', name: 'As-Sunnah Foundation', region: 'Chittagong', needs: 'Winter blankets, Disaster relief funds, Medicine', password: 'assunnah123' },
            { id: 'jaago', name: 'Jaago Foundation', region: 'Dhaka', needs: 'Laptops, Children\'s books, Teacher salaries', password: 'jaago123' },
            { id: 'mastul', name: 'Mastul Foundation', region: 'Rajshahi', needs: 'Orphanage support, Food packages, Clothing', password: 'mastul123' },
        ];


        // Quotes for motivation
        const motivationQuotes = [
            "Your small help can make a big difference.",
            "Give a little. Change a life.",
            "Together, we can build hope.",
            "A helping hand is the most powerful gift.",
            "Your kindness today becomes someone’s tomorrow.",
            "Be the reason someone smiles today.",
            "Every donation counts. Every heart matters.",
            "Compassion is the world’s strongest currency.",
            "Hope grows when we share what we have.",
            "You can’t help everyone, but everyone can help someone."
        ];


        // --- API Calls and Data Submission ---

        /**
         * Renders the global statistics banner.
         */
        const renderStats = (stats) => {
            const container = document.getElementById('stats-banner');
            if (!container) return;

            // Simple check to prevent errors if stats are missing
            const totalDonations = parseFloat(stats.total_donations) || 0;
            const totalDonors = parseInt(stats.total_donors) || 0;
            const totalContributions = parseInt(stats.total_contributions) || 0;
            
            container.innerHTML = `
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-green-100 p-4 rounded-xl shadow-md border-b-4 border-green-500">
                        <p class="text-3xl font-extrabold text-green-700">BDT ${(totalDonations / 1000).toFixed(1)}K</p>
                        <p class="text-sm text-green-600 font-semibold">Total Funds Confirmed</p>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-xl shadow-md border-b-4 border-blue-500">
                        <p class="text-3xl font-extrabold text-blue-700">${totalDonors.toLocaleString()}</p>
                        <p class="text-sm text-blue-600 font-semibold">Total Unique Donors</p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-xl shadow-md border-b-4 border-yellow-500">
                        <p class="text-3xl font-extrabold text-yellow-700">${totalContributions.toLocaleString()}</p>
                        <p class="text-sm text-yellow-600 font-semibold">Total Contributions</p>
                    </div>
                </div>
            `;
        };

        /**
         * Sends donation data to the PHP API.
         */
        /**
         * Sends donation data to the PHP API using FORM DATA (Fix for InfinityFree 400 Error)
         */
        window.handleDonationSubmit = async (event) => {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const type = formData.get('donationType');
            const targetCharityId = formData.get('targetCharityId'); 
            let itemsText = "";
            let amount = 0;

            if (!targetCharityId) {
                window.showModal('error', 'Charity Selection', 'Please select a charity partner.');
                return;
            }

            if (type === 'financial') {
                amount = parseFloat(formData.get('amount'));
                if (isNaN(amount) || amount <= 0) {
                    window.showModal('error', 'Invalid Amount', 'Please enter a valid amount.');
                    return;
                }
            } else {
                itemsText = formData.get('items').trim();
                if (!itemsText) {
                    window.showModal('error', 'Missing Details', `Please list the items.`);
                    return;
                }
                amount = 0;
            }

            // --- THE FIX: USE URLSearchParams INSTEAD OF JSON ---
            const payload = new URLSearchParams();
            payload.append('donorId', currentUserId);
            payload.append('charityId', targetCharityId);
            payload.append('type', type);
            payload.append('amount', amount);
            payload.append('itemsData', itemsText); // PHP expects 'itemsData'
            
            try {
                const response = await fetch('api_donations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded', // This is what the firewall likes!
                    },
                    body: payload
                });

                // Check if response is okay before trying to read JSON
                if (!response.ok) {
                    throw new Error(`Server returned ${response.status} ${response.statusText}`);
                }

                const result = await response.json();

                if (result.success) {
                    form.reset();
                    window.selectDonationType('financial');
                    window.showModal('success', 'Donation Logged!', result.message);
                    await fetchDataAndRender(); 
                } else {
                    window.showModal('error', 'Submission Failed', result.message);
                }
            } catch (error) {
                console.error("Fetch Error:", error);
                window.showModal('error', 'Connection Error', 'Failed to connect. ' + error.message);
            }
        };


        /**
         * Admin confirms a donation by calling the relevant API endpoint.
         */
        window.handleConfirmDonation = async (donationId, type, amount, charityId) => {
            if (userRole !== 'admin' && userRole !== 'charity') {
                window.showModal('error', 'Permission Denied', 'You must be an authenticated administrator or charity partner to confirm donations.');
                return;
            }
            
            const payload = {
                donationId: donationId,
                action: 'confirm'
            };

            try {
                // NOTE: This assumes you create an api_admin.php script
                const response = await fetch('api_admin.php', { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();

                if (result.success) {
                    window.showModal('success', 'Confirmation Success', result.message);
                    await fetchDataAndRender(); // Refresh all data tables
                } else {
                    window.showModal('error', 'Confirmation Failed', result.message);
                }
            } catch (error) {
                console.error("Confirmation Error:", error);
                window.showModal('error', 'Network Error', 'Could not communicate with the admin API.');
            }
        };

        /**
         * Charity Partner updates their needs list.
         */
        window.handleUpdateNeeds = async (event, charityId) => {
            event.preventDefault();
            const needs = document.getElementById('needs-update').value;
            
            const payload = {
                charityId: charityId,
                needs: needs
            };

            try {
                // NOTE: This assumes you create an api_charity.php script
                const response = await fetch('api_charity.php', { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showModal('success', 'Update Successful', result.message);
                    await fetchDataAndRender(); // Refresh charity needs list
                } else {
                    window.showModal('error', 'Update Failed', result.message);
                }
            } catch (error) {
                console.error("Update Needs Error:", error);
                window.showModal('error', 'Network Error', 'Could not communicate with the charity API.');
            }
        };


        // --- Rendering Functions (Adapted for PHP array structure) ---
        
        /**
         * Populates the Charity selection dropdown.
         */
        const populateCharityDropdown = (needsArray) => {
            const selectElement = document.getElementById('target-charity-id');
            if (!selectElement) return;

            selectElement.innerHTML = needsArray.map(charity => 
                `<option value="${charity.id}">${charity.name} (${charity.region})</option>`
            ).join('');
        }

        /**
         * Renders the list of charity needs.
         */
        window.renderCharityNeeds = (needsArray) => {
            const container = document.getElementById('charity-needs-list');
            const selectedType = document.querySelector('.donation-tab:not(.hidden) input[name="donationType"]')?.value || 'financial';
            
            if (!container) return;
            
            // Note: Data is already sorted by totalGained by api_stats.php
            const sortedNeeds = needsArray; 

            if (sortedNeeds.length === 0) {
                container.innerHTML = `<p class="text-gray-600 col-span-full">No active charity needs found.</p>`;
                return;
            }

            container.innerHTML = sortedNeeds.map(charity => {
                const needsList = charity.needs ? charity.needs.split(',').map(n => n.trim()) : [];
                const isMatch = needsList.some(need => 
                    (selectedType === 'financial' && need.toLowerCase().includes('$')) ||
                    (selectedType === 'food' && (need.toLowerCase().includes('rice') || need.toLowerCase().includes('food') || need.toLowerCase().includes('lentils') || need.toLowerCase().includes('produce'))) ||
                    (selectedType === 'goods' && (need.toLowerCase().includes('coats') || need.toLowerCase().includes('blankets') || need.toLowerCase().includes('supplies') || need.toLowerCase().includes('books') || need.toLowerCase().includes('toys') || need.toLowerCase().includes('clothing')))
                );
                
                const matchClass = isMatch ? 'match-highlight border-teal-500' : 'border-gray-200';
                const matchIcon = isMatch ? '<i class="fas fa-check-circle text-teal-500 mr-2"></i> Matching Need!' : '';
                
                const needsHtml = needsList.slice(0, 3).map(n => `<span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">${n}</span>`).join(' ');

                // Ensure totalGained is treated as a number
                const totalGained = parseFloat(charity.totalGained || 0);

                return `
                    <div id="${charity.id}" class="charity-card bg-white p-6 rounded-xl shadow-md border-b-4 ${matchClass}">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xl font-bold text-gray-800">${charity.name}</h3>
                            <div class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">${charity.region}</div>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">ID: ${charity.id}</p>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Total Gained: <span class="text-green-600 font-extrabold">${totalGained.toLocaleString(undefined, { minimumFractionDigits: 2 })} BDT</span></p>
                        
                        <p class="text-xs font-medium text-gray-700 mb-3">${matchIcon} Top Needs:</p>
                        <div class="space-x-1 space-y-1">${needsHtml}</div>
                    </div>
                `;
            }).join('');

            populateCharityDropdown(needsArray);
        };


        /**
         * Renders the user's donation history.
         */
        window.renderMyDonations = (donations) => {
            const tbody = document.getElementById('my-donations-table');
            if (!tbody) return;

            // Sort by created_at descending (PHP returns timestamps as strings)
            const sortedDonations = [...donations].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            if (sortedDonations.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No donations tracked yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = sortedDonations.map(d => {
                const date = d.created_at ? new Date(d.created_at).toLocaleDateString() : 'N/A';
                const statusClass = d.status === 'Confirmed' ? 'text-green-600 font-bold' : 'text-yellow-600';
                
                let details;
                if (d.items_data) {
                    details = d.items_data;
                } else if (d.type === 'financial' && d.amount !== undefined) {
                    details = `${parseFloat(d.amount || 0).toLocaleString()} BDT`;
                } else {
                    details = 'N/A';
                }

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${d.type.charAt(0).toUpperCase() + d.type.slice(1)}</td>
                        <td class="px-4 py-4 whitespace-pre-wrap text-sm text-gray-700 max-w-xs">${details}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm ${statusClass}">${d.status}</td>
                    </tr>
                `;
            }).join('');
        };

        /**
         * Renders the admin pending donations table.
         */
        window.renderPendingDonations = (donations) => {
            const tbody = document.getElementById('pending-donations-table');
            if (!tbody) return;

            const pending = donations.filter(d => d.status === 'Pending Confirmation');
            
            // Sort by created_at descending
            pending.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            if (pending.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No pending donations to review.</td></tr>`;
                return;
            }

            tbody.innerHTML = pending.map(d => {
                const date = d.created_at ? new Date(d.created_at).toLocaleDateString() : 'N/A';
                
                let details;
                if (d.items_data) {
                    details = d.items_data;
                } else if (d.type === 'financial' && d.amount !== undefined) {
                    details = `${parseFloat(d.amount || 0).toLocaleString()} BDT`;
                } else {
                    details = 'N/A';
                }
                
                const typeDisplay = d.type.charAt(0).toUpperCase() + d.type.slice(1);
                const amount = parseFloat(d.amount || 0);

                return `
                    <tr class="hover:bg-yellow-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${typeDisplay}</td>
                        <td class="px-4 py-4 whitespace-pre-wrap text-sm text-gray-700 max-w-xs">${details}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">${d.charity_id}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <button class="dynamic-btn bg-green-500 text-white text-xs px-3 py-1 rounded-lg hover:bg-green-600" 
                                onclick="window.handleConfirmDonation('${d.donation_id}', '${d.type}', ${amount}, '${d.charity_id}')">
                                Confirm & Log
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        };

        /**
         * Renders the admin audit history table (confirmed donations).
         */
        window.renderAuditHistory = (history) => {
            const tbody = document.getElementById('audit-history-table');
            if (!tbody) return;
            
            if (!history || history.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No history recorded yet.</td></tr>`;
                return;
            }

            // Sort by confirmed_at descending
            history.sort((a, b) => new Date(b.confirmed_at) - new Date(a.confirmed_at));

            tbody.innerHTML = history.map(d => {
                const date = d.confirmed_at ? new Date(d.confirmed_at).toLocaleDateString() : 'N/A';
                
                let details;
                if (d.items_data) {
                    details = d.items_data;
                } else if (d.type === 'financial' && d.amount !== undefined) {
                    details = `${parseFloat(d.amount || 0).toLocaleString()} BDT`;
                } else {
                    details = 'N/A';
                }
                
                const typeDisplay = d.type.charAt(0).toUpperCase() + d.type.slice(1);


                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">${date}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${typeDisplay}</td>
                        <td class="px-4 py-4 whitespace-pre-wrap text-sm text-gray-700 max-w-xs">${details}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">${d.charity_id}</td>
                    </tr>
                `;
            }).join('');
        };


        // --- Data Fetching & Polling ---
        
        /**
         * Fetches all required data from PHP APIs and updates the UI.
         */
        const fetchDataAndRender = async () => {
            try {
                // Fetch All Data (Needs, Pending, Audit, My Donations)
                const [statsResponse, pendingResponse, auditResponse, myDonationsResponse] = await Promise.all([
                    fetch('api_stats.php').then(r => r.json()),
                    fetch('api_donations.php?status=pending').then(r => r.json()), // Assumes a GET endpoint to fetch pending
                    fetch('api_donations.php?status=confirmed_history').then(r => r.json()), // Assumes a GET endpoint to fetch audit
                    fetch(`api_donations.php?donorId=${currentUserId}&status=all`).then(r => r.json()) // Assumes a GET endpoint to fetch user's history
                ]);
                
                // 1. Stats and Charity Needs (from api_stats.php)
                if (statsResponse.success) {
                    renderStats(statsResponse.stats);
                    window.charityNeeds = statsResponse.charityNeeds;
                    window.renderCharityNeeds(window.charityNeeds);
                }

                // 2. Pending Donations (for Admin)
                if (pendingResponse.success) {
                    window.pendingDonations = pendingResponse.donations;
                    window.renderPendingDonations(window.pendingDonations);
                }

                // 3. Audit History (for Admin)
                if (auditResponse.success) {
                    window.auditHistory = auditResponse.donations;
                    window.renderAuditHistory(window.auditHistory.filter(d => d.status === 'Confirmed'));
                }

                // 4. My Donations (for Donor)
                if (myDonationsResponse.success) {
                    window.donorDonations = myDonationsResponse.donations;
                    window.renderMyDonations(window.donorDonations);
                }

                window.handleMatching();

            } catch (error) {
                console.error("Error during data polling:", error);
                // We show an error but don't halt the app
            }
        };

        /**
         * Main initial setup function.
         */
        const setupApp = async () => {
            window.selectDonationType('financial');
            startQuoteRotator();
            
            // Initial data fetch (blocking)
            try {
                const response = await fetch('api_stats.php');
                const data = await response.json();
                
                if (data.success) {
                    // Load required global data
                    window.charityNeeds = data.charityNeeds;
                    
                    // Render UI components
                    renderStats(data.stats); // Display initial counts
                    window.renderCharityNeeds(window.charityNeeds);
                    
                    // Fetch and render all other data for the first time
                    await fetchDataAndRender(); 

                    // Start regular polling for data updates (replaces real-time listener)
                    setInterval(fetchDataAndRender, 10000); // Poll every 10 seconds

                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error("Initialization Failed:", error);
                window.showModal('error', 'System Failure', 'Could not fetch initial data from the MySQL API. Check XAMPP and PHP files.');
            } finally {
                // Remove loader and show content regardless of API success
                document.getElementById('app-loader').classList.add('opacity-0');
                setTimeout(() => {
                    document.getElementById('app-loader').classList.add('hidden');
                    document.getElementById('app-content').classList.remove('hidden');
                    window.updateRoleView(userRole); 
                }, 500); 
            }
        };


        // --- UI Logic and Toggles ---

        /**
         * Renders the dynamic quote section.
         */
        const startQuoteRotator = () => {
            const quoteElement = document.getElementById('quote-section').querySelector('.quote-slide');
            let quoteIndex = Math.floor(Math.random() * motivationQuotes.length);

            const rotateQuote = () => {
                quoteElement.style.opacity = '0';
                setTimeout(() => {
                    quoteElement.textContent = motivationQuotes[quoteIndex];
                    quoteElement.style.opacity = '1';
                    quoteIndex = (quoteIndex + 1) % motivationQuotes.length;
                }, 500); // Wait for fade out
            };

            rotateQuote(); // Initial quote
            setInterval(rotateQuote, 8000); // Rotate every 8 seconds
        };
        
        // ... (Keep the rest of the UI/Modal logic as is)
        
        /**
         * Updates the UI based on the current user role.
         */
        window.updateRoleView = (role) => {
            // ... (Keep existing updateRoleView logic)
            const banner = document.getElementById('role-banner');
            const toggleBtn = document.getElementById('role-toggle-btn');
            const donorView = document.getElementById('donor-view');
            const loginView = document.getElementById('login-view');
            const adminView = document.getElementById('admin-view');
            const charityPortalView = document.getElementById('charity-portal-view');
            const notificationBtn = document.getElementById('notification-button');

            // Hide all
            donorView.classList.add('hidden');
            loginView.classList.add('hidden');
            adminView.classList.add('hidden');
            charityPortalView.classList.add('hidden');
            notificationBtn.classList.add('hidden');

            switch (role) {
                case 'donor':
                    banner.className = 'mb-6 p-4 rounded-lg text-center font-bold text-lg shadow-md bg-blue-100 text-blue-800 transition-colors';
                    banner.textContent = 'Welcome, Donor! View Needs and Contribute.';
                    toggleBtn.textContent = 'Switch to Staff/Admin Login';
                    donorView.classList.remove('hidden');
                    notificationBtn.classList.remove('hidden');
                    break;
                case 'login':
                    banner.className = 'mb-6 p-4 rounded-lg text-center font-bold text-lg shadow-md bg-gray-100 text-gray-800 transition-colors';
                    banner.textContent = 'Staff/Charity Login Interface';
                    toggleBtn.textContent = 'Switch to Donor View';
                    loginView.classList.remove('hidden');
                    break;
                case 'admin':
                    banner.className = 'mb-6 p-4 rounded-lg text-center font-bold text-lg shadow-md bg-red-100 text-red-800 transition-colors';
                    banner.textContent = 'ADMINISTRATOR PORTAL - Full Audit Access';
                    toggleBtn.textContent = 'Sign Out';
                    adminView.classList.remove('hidden');
                    break;
                case 'charity':
                    const charityId = document.getElementById('login-id')?.value?.toLowerCase() || 'brac'; // Fallback
                    const charityInfo = window.charityNeeds.find(c => c.id === charityId);
                    if (charityInfo) {
                            document.getElementById('charity-info').innerHTML = `<p class="text-xl font-bold">${charityInfo.name}</p><p class="text-sm text-gray-500">ID: ${charityId}</p>`;
                            document.getElementById('needs-update').value = charityInfo.needs || '';
                            document.getElementById('update-needs-form').onsubmit = (e) => window.handleUpdateNeeds(e, charityId);
                    }
                    banner.className = 'mb-6 p-4 rounded-lg text-center font-bold text-lg shadow-md bg-teal-100 text-teal-800 transition-colors';
                    banner.textContent = `CHARITY PARTNER PORTAL (${charityId.toUpperCase()})`;
                    toggleBtn.textContent = 'Sign Out';
                    charityPortalView.classList.remove('hidden');
                    break;
            }
            userRole = role === 'login' ? userRole : role;
            document.getElementById('notification-panel').classList.add('hidden'); // Hide panel on role switch
            window.handleMatching(); // Re-run matching in case of role change
        };

        window.toggleRole = async () => {
            if (userRole === 'donor') {
                updateRoleView('login');
            } else {
                // Clear local credentials on sign out
                document.getElementById('login-id').value = '';
                document.getElementById('login-password').value = '';
                currentUserId = 'anonymous-' + Math.random().toString(36).substring(2, 9);
                
                window.lastNotificationCount = 0;
                document.getElementById('notification-count').classList.add('opacity-0');
                updateRoleView('donor');
            }
        };

        window.selectDonationType = (type) => {
            const tabs = document.querySelectorAll('.donation-tab');
            const buttons = document.querySelectorAll('.tab-button');
            const formInputs = document.querySelectorAll('.donation-tab textarea, .donation-tab input[type="number"]');

            // 1. Hide all tabs and disable all inputs
            tabs.forEach(tab => tab.classList.add('hidden'));
            formInputs.forEach(input => input.setAttribute('disabled', 'true'));

            // 2. Reset button styles
            buttons.forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-blue-600', 'bg-blue-50', 'font-bold');
                btn.classList.add('text-gray-600', 'hover:text-blue-600', 'hover:bg-gray-50');
            });

            // 3. Show selected tab and enable its inputs
            const selectedTab = document.getElementById(`${type}-tab`);
            const selectedButton = document.querySelector(`.tab-button[data-type="${type}"]`);

            if (selectedTab && selectedButton) {
                selectedTab.classList.remove('hidden');
                selectedTab.querySelectorAll('textarea, input[type="number"]').forEach(input => input.removeAttribute('disabled'));
                
                selectedButton.classList.add('text-blue-600', 'border-blue-600', 'bg-blue-50', 'font-bold');
                selectedButton.classList.remove('text-gray-600', 'hover:text-blue-600', 'hover:bg-gray-50');
            }

            // 4. Rerun matching logic
            window.handleMatching(type);
        };

        window.handleMatching = (selectedType = null) => {
            if (!window.charityNeeds || window.charityNeeds.length === 0) return;
            // Rerenders charity list which applies the highlight based on selected type
            window.renderCharityNeeds(window.charityNeeds); 
        };

        // --- Modal Logic ---

        const modalBackdrop = document.getElementById('modal-backdrop');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalHeader = document.getElementById('modal-header');
        const modalCloseBtn = document.getElementById('modal-close-button');

        window.showModal = (type, title, message) => {
            modalTitle.innerHTML = ''; 

            if (type === 'success') {
                modalHeader.className = 'p-4 flex items-center justify-between text-white bg-green-500';
                modalTitle.innerHTML = `<i class="fas fa-check-circle text-2xl mr-2"></i> ${title}`;
                modalCloseBtn.className = 'dynamic-btn mt-6 w-full py-2 rounded-lg text-white font-semibold bg-green-600 hover:bg-green-700 transition-colors';
            } else if (type === 'error') {
                modalHeader.className = 'p-4 flex items-center justify-between text-white bg-red-500';
                modalTitle.innerHTML = `<i class="fas fa-exclamation-triangle text-2xl mr-2"></i> ${title}`;
                modalCloseBtn.className = 'dynamic-btn mt-6 w-full py-2 rounded-lg text-white font-semibold bg-red-600 hover:bg-red-700 transition-colors';
            } else {
                modalHeader.className = 'p-4 flex items-center justify-between text-white bg-blue-500';
                modalTitle.innerHTML = `<i class="fas fa-info-circle text-2xl mr-2"></i> ${title}`;
                modalCloseBtn.className = 'dynamic-btn mt-6 w-full py-2 rounded-lg text-white font-semibold bg-blue-600 hover:bg-blue-700 transition-colors';
            }
            
            modalMessage.textContent = message;
            modalCloseBtn.textContent = 'Close';
            modalBackdrop.classList.remove('hidden');
        };

        window.hideModal = () => {
            modalBackdrop.classList.add('hidden');
        };

        // --- Event Listeners ---
        
        document.getElementById('login-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const id = document.getElementById('login-id').value.toLowerCase();
            const password = document.getElementById('login-password').value;

            // In a real application, this would call api_auth.php which checks MySQL
            // For now, we use the hardcoded check for demonstration:
            
            // 1. Admin Check
            if (id === 'admin' && password === 'uniadmin') {
                window.showModal('success', 'Admin Login Success', 'Welcome to the Administrator Portal.');
                currentUserId = id;
                window.updateRoleView('admin');
                return;
            }

            // 2. Charity Check
            const charity = MOCK_CHARITIES.find(c => c.id === id); 
            if (charity && charity.password === password) { 
                window.showModal('success', 'Charity Login Success', `Welcome, ${charity.name} Partner!`);
                currentUserId = id;
                window.updateRoleView('charity');
                return;
            }

            window.showModal('error', 'Login Failed', 'Invalid User ID or Password. (Check `admin`/`uniadmin` or a charity ID/pass)');
        });

        window.toggleNotifications = () => {
            const panel = document.getElementById('notification-panel');
            panel.classList.toggle('hidden');
            if (panel.classList.contains('hidden')) {
                window.lastNotificationCount = 0; // Reset count when panel is closed
                document.getElementById('notification-count').classList.add('opacity-0');
            }
        };

        document.addEventListener('DOMContentLoaded', setupApp);
    </script>
</body>
</html>