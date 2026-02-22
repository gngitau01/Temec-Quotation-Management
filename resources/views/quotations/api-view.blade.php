@extends('layouts.app')

@section('title', 'Quotation Information')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Quotation Information</h2>
        <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Quotations
        </a>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex gap-4">
            <button type="button" onclick="switchTab('list')" class="tab-btn active px-4 py-3 text-gray-700 border-b-2 border-transparent font-medium hover:text-purple-600">
                <i class="fas fa-list mr-2"></i> Browse All Quotations
            </button>
            <button type="button" onclick="switchTab('search')" class="tab-btn  px-4 py-3 text-gray-700 border-b-2 border-purple-600 font-medium">
                <i class="fas fa-search mr-2"></i> Search Quotation
            </button>
        </div>
    </div>

    <!-- Search Tab -->
    <div id="search-tab" class="tab-content">
        <!-- Search/Filter Section -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <form id="quotationForm" class="flex gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation ID</label>
                    <input type="number" id="quotationId" placeholder="Enter quotation ID" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-600">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="fetchQuotation()" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-purple-600"></i>
            <p class="text-gray-600 mt-4">Loading quotation details...</p>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden mb-6 p-4 bg-red-100 text-red-700 rounded flex items-center justify-between">
            <span id="errorText"></span>
            <button type="button" onclick="document.getElementById('errorMessage').classList.add('hidden')" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Quotation Details Section -->
        <div id="quotationContent" class="hidden">
            <!-- Quotation Header -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow">
                    <p class="text-sm text-gray-600">Document Number</p>
                    <p class="text-lg font-semibold text-gray-900" id="docNumber">-</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <p class="text-sm text-gray-600">Document Date</p>
                    <p class="text-lg font-semibold text-gray-900" id="docDate">-</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <p class="text-sm text-gray-600">Collective No.</p>
                    <p class="text-lg font-semibold text-gray-900" id="collectiveNo">-</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <p class="text-sm text-gray-600">Closing Date</p>
                    <p class="text-lg font-semibold text-gray-900" id="closingDate">-</p>
                </div>
            </div>

            <!-- Vendor and Buyer Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Vendor -->
                <div class="bg-white p-6 rounded shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendor Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Vendor No.</p>
                            <p class="text-gray-900 font-medium" id="vendorNo">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-gray-900 font-medium" id="vendorName">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="text-gray-900 font-medium" id="vendorLocation">-</p>
                        </div>
                    </div>
                </div>

                <!-- Buyer -->
                <div class="bg-white p-6 rounded shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Buyer Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-gray-900 font-medium" id="buyerName">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Address</p>
                            <p class="text-gray-900 font-medium" id="buyerAddress">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Town / Country</p>
                            <p class="text-gray-900 font-medium"><span id="buyerTown">-</span> / <span id="buyerCountry">-</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-gray-900 font-medium" id="email">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Closing Time</p>
                        <p class="text-gray-900 font-medium" id="closingTime">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Prepared By</p>
                        <p class="text-gray-900 font-medium" id="preparedBy">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Approved By</p>
                        <p class="text-gray-900 font-medium" id="approvedBy">-</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded shadow overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Quotation Items</h3>
                    <button type="button" onclick="openEditModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-edit mr-2"></i> Edit Prices
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Item #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Unit Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Discount (%)</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">VAT</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                                    No items found
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="7" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 uppercase">Grand Total</td>
                                <td id="itemsGrandTotal" class="px-6 py-3 text-right text-sm font-bold text-gray-900">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-between">
                <a href="{{ route('quotations.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Quotations
                </a>
                <a id="viewDetailsBtn" href="#" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-eye mr-2"></i> View Full Details
                </a>
            </div>
        </div>

        <!-- Edit Prices Modal -->
        <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Item Prices</h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Item to Edit</label>
                        <select id="itemSelect" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600">
                            <option value="">Choose an item...</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Spare Part for Price</label>
                        <select id="sparePartSelect" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600">
                            <option value="">Choose a spare part...</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                        <input type="number" id="unitPriceInput" step="0.01" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">VAT Percentage (%)</label>
                        <input type="number" id="vatPercentageInput" step="0.01" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount (%)</label>
                        <input type="number" id="discountInput" step="0.01" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-600">
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEditModal()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                            Cancel
                        </button>
                        <button type="button" onclick="updateItemPrice()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Update Price
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="bg-white p-12 rounded shadow text-center">
            <i class="fas fa-search text-6xl text-gray-300 mb-4 block"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Search for a Quotation</h3>
            <p class="text-gray-500">Enter a quotation ID above to view detailed information</p>
        </div>
    </div>

    <!-- List Tab -->
    <div id="list-tab" class="tab-content hidden">
        <!-- Loading State for List -->
        <div id="listLoadingSpinner" class="hidden text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-purple-600"></i>
            <p class="text-gray-600 mt-4">Loading quotations...</p>
        </div>

        <!-- Quotations List Table -->
        <div id="quotationsListContainer" class="bg-white rounded shadow overflow-hidden hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Document Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Buyer</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase">Items</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Total Cost</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody id="quotationsTableBody">
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                                No quotations found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State for List -->
        <div id="emptyStateList" class="bg-white p-12 rounded shadow text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No quotations available</h3>
        </div>
    </div>
</div>

<script>
    // Tab Switching Function
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-purple-600', 'border-b-2');
            btn.classList.add('border-transparent');
        });
        
        // Show selected tab
        const selectedTab = document.getElementById(tabName + '-tab');
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        }
        
        // Add active class to clicked button
        event.target.closest('.tab-btn').classList.remove('border-transparent');
        event.target.closest('.tab-btn').classList.add('border-purple-600', 'border-b-2');
        
        // Load quotations list if switching to list tab
        if (tabName === 'list') {
            loadQuotationsList();
        }
    }

    // Load all quotations
    function loadQuotationsList() {
        document.getElementById('listLoadingSpinner').classList.remove('hidden');
        document.getElementById('quotationsListContainer').classList.add('hidden');
        document.getElementById('emptyStateList').classList.add('hidden');

        fetch('/api/quotations/with-summary')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load quotations');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' && data.data.length > 0) {
                    displayQuotationsList(data.data);
                } else {
                    document.getElementById('emptyStateList').classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('emptyStateList').classList.remove('hidden');
            })
            .finally(() => {
                document.getElementById('listLoadingSpinner').classList.add('hidden');
            });
    }

    // Display quotations in table
    function displayQuotationsList(quotations) {
        const tableBody = document.getElementById('quotationsTableBody');
        
        if (quotations.length > 0) {
            tableBody.innerHTML = quotations.map(q => `
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${q.document_number || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${formatDate(q.date)}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${q.vendor_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${q.buyer_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-600"><span class="bg-gray-100 px-3 py-1 rounded-full">${q.item_count}</span></td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">${formatCurrency(q.total_cost)}</td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" id="viewDetailsBtn" onclick="selectQuotation(${q.id})" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition text-sm">
                            <i class="fas fa-check mr-1"></i> View
                        </button>
                    </td>
                </tr>
            `).join('');
            document.getElementById('quotationsListContainer').classList.remove('hidden');
        } else {
            document.getElementById('emptyStateList').classList.remove('hidden');
        }
    }
function selectQuotation(id) {
    // Do whatever logic you need
    console.log("Selected quotation id:", id);

    // Navigate to details page
    window.location.href = `/quotations/${id}/details`;
}
    // Select a quotation and display its details
    function selectQuotation(quotationId) {
        document.getElementById('quotationId').value = quotationId;
        fetchQuotation();
        // Switch to search tab to show details
        switchTab('search');
    }

    function fetchQuotation(e) {
        if (e) {
            event.preventDefault();
            event.stopPropagation();
        }
        console.log('fetchQuotation called');
        const quotationId = document.getElementById('quotationId').value.trim();

        if (!quotationId) {
            showError('Please enter a quotation ID');
            return;
        }

        // Show loading state
        document.getElementById('loadingSpinner').classList.remove('hidden');
        document.getElementById('quotationContent').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');

        // Fetch from API
        fetch(`/api/quotation/${quotationId}`)
            .then(response => {
                if (response.status === 404) {
                    throw new Error('Quotation not found');
                }
                if (!response.ok) {
                    throw new Error('Failed to fetch quotation data');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    displayQuotation(data.data);
                } else {
                    showError(data.message || 'Failed to retrieve quotation');
                }
            })
            .catch(error => {
                showError(error.message);
            })
            .finally(() => {
                document.getElementById('loadingSpinner').classList.add('hidden');
            });
    }

    function displayQuotation(data) {
        currentQuotationData = data; // Store for modal use
        const quotation = data.quotation;
        const vendor = data.vendor;
        const buyer = data.buyer;
        const items = data.items || [];

        // Set the view details link
        document.getElementById('viewDetailsBtn').href = `/quotations/${quotation.id}/details`;

        // Populate quotation details
        document.getElementById('docNumber').textContent = quotation.document_number || '-';
        document.getElementById('docDate').textContent = formatDate(quotation.date);
        document.getElementById('collectiveNo').textContent = quotation.collective_no || '-';
        document.getElementById('closingDate').textContent = formatDate(quotation.closing_date);

        // Populate vendor info
        document.getElementById('vendorNo').textContent = vendor ? vendor.vendorNo || '-' : '-';
        document.getElementById('vendorName').textContent = vendor ? vendor.name || '-' : '-';
        document.getElementById('vendorLocation').textContent = vendor ? vendor.location || '-' : '-';

        // Populate buyer info
        document.getElementById('buyerName').textContent = buyer ? buyer.name || '-' : '-';
        document.getElementById('buyerAddress').textContent = buyer ? buyer.address || '-' : '-';
        document.getElementById('buyerTown').textContent = buyer ? buyer.town || '-' : '-';
        document.getElementById('buyerCountry').textContent = buyer ? buyer.country || '-' : '-';

        // Populate additional details
        document.getElementById('email').textContent = quotation.email || '-';
        document.getElementById('closingTime').textContent = quotation.closingTime || '-';
        document.getElementById('preparedBy').textContent = quotation.prepared_by || '-';
        document.getElementById('approvedBy').textContent = quotation.approved_by || '-';

        // Populate items table
        const tableBody = document.getElementById('itemsTableBody');
        if (items.length > 0) {
            tableBody.innerHTML = items.map(item => `
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">${item.item_no || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${item.description || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-right">${formatNumber(item.quantity)}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${item.unit || '-'}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-right">${formatCurrency(item.unit_price)}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-right">${formatCurrency(item.discount)}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 text-right">${formatCurrency(item.vat)}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">${formatCurrency(item.total_cost)}</td>
                    </tr>
                `).join('');
            const grandTotal = items.reduce((sum, item) => sum + parseFloat(item.total_cost || 0), 0);
            document.getElementById('itemsGrandTotal').textContent = formatCurrency(grandTotal);
        } else {
            tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                            No items found
                        </td>
                    </tr>
                `;
            document.getElementById('itemsGrandTotal').textContent = formatCurrency(0);
        }

        document.getElementById('quotationContent').classList.remove('hidden');
        document.getElementById('emptyState').classList.add('hidden');
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function formatNumber(value) {
        if (!value && value !== 0) return '-';
        return parseFloat(value).toFixed(2);
    }

    function formatCurrency(value) {
        if (!value && value !== 0) return '-';
        return parseFloat(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function showError(message) {
        document.getElementById('errorText').textContent = message;
        document.getElementById('errorMessage').classList.remove('hidden');
        document.getElementById('quotationContent').classList.add('hidden');
        document.getElementById('emptyState').classList.add('hidden');
    }

    // Allow Enter key to search
    document.getElementById('quotationId').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchQuotation();
        }
    });

    // Global variables for modal
    let currentQuotationData = null;
    let sparePartsData = [];

    // Open edit modal
    function openEditModal() {
        if (!currentQuotationData || !currentQuotationData.items || currentQuotationData.items.length === 0) {
            alert('No items available to edit');
            return;
        }

        // Populate item select
        const itemSelect = document.getElementById('itemSelect');
        itemSelect.innerHTML = '<option value="">Choose an item...</option>';
        currentQuotationData.items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.item_no} - ${item.description}`;
            itemSelect.appendChild(option);
        });

        // Load spare parts if not already loaded
        if (sparePartsData.length === 0) {
            fetch('/api/spare-parts')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        sparePartsData = data.data;
                        populateSparePartsSelect();
                    }
                })
                .catch(error => {
                    console.error('Error loading spare parts:', error);
                    alert('Failed to load spare parts');
                });
        } else {
            populateSparePartsSelect();
        }

        document.getElementById('editModal').classList.remove('hidden');
    }

    // Populate spare parts select
    function populateSparePartsSelect() {
        const sparePartSelect = document.getElementById('sparePartSelect');
        sparePartSelect.innerHTML = '<option value="">Choose a spare part...</option>';
        sparePartsData.forEach(part => {
            const option = document.createElement('option');
            option.value = part.id;
            option.textContent = `${part.part_number} - ${part.name} (${formatCurrency(part.amount_per_unit)})`;
            sparePartSelect.appendChild(option);
        });
    }

    // When user selects an item, fetch current pricing and fill the form
    document.getElementById('itemSelect').addEventListener('change', function() {
        const itemId = this.value;
        if (!itemId) {
            document.getElementById('unitPriceInput').value = '';
            document.getElementById('vatPercentageInput').value = '';
            document.getElementById('discountInput').value = '';
            return;
        }
        fetch(`/api/item/${itemId}/pricing`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const d = data.data;
                    document.getElementById('unitPriceInput').value = d.unit_price != null ? d.unit_price : '';
                    document.getElementById('vatPercentageInput').value = d.vat_percentage != null ? d.vat_percentage : '';
                    document.getElementById('discountInput').value = d.discount != null ? d.discount : '';
                }
            })
            .catch(err => {
                console.error('Error loading item pricing:', err);
            });
    });

    // When user selects a spare part, pre-fill unit price, VAT %, and discount from catalog
    document.getElementById('sparePartSelect').addEventListener('change', function() {
        const selectedId = this.value;
        const selectedPart = sparePartsData.find(part => part.id == selectedId);
        if (selectedPart) {
            document.getElementById('unitPriceInput').value = selectedPart.amount_per_unit != null ? selectedPart.amount_per_unit : '';
            document.getElementById('vatPercentageInput').value = selectedPart.vat != null ? selectedPart.vat : '';
            document.getElementById('discountInput').value = selectedPart.discount != null ? selectedPart.discount : '';
        } else {
            document.getElementById('unitPriceInput').value = '';
            document.getElementById('vatPercentageInput').value = '';
            document.getElementById('discountInput').value = '';
        }
    });

    // Close edit modal
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('itemSelect').value = '';
        document.getElementById('sparePartSelect').value = '';
        document.getElementById('unitPriceInput').value = '';
        document.getElementById('vatPercentageInput').value = '';
        document.getElementById('discountInput').value = '';
    }

    // Update item price
    function updateItemPrice() {
        const itemId = document.getElementById('itemSelect').value;
        const unitPrice = document.getElementById('unitPriceInput').value;
        const vatPercentage = document.getElementById('vatPercentageInput').value;
        const discount = document.getElementById('discountInput').value;

        if (!itemId || !unitPrice) {
            alert('Please select an item and ensure a price is set');
            return;
        }
console.log(discount,vatPercentage,unitPrice);

        fetch(`/api/item/${itemId}/pricing`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                unit_price: parseFloat(unitPrice),
                vat_percentage: vatPercentage !== '' ? parseFloat(vatPercentage) : undefined,
                discount: discount !== '' ? parseFloat(discount) : undefined
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                closeEditModal();
                alert('Price updated successfully');
                // Refresh the quotation display
                fetchQuotation();
            } else {
                alert('Failed to update price: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating price:', error);
            alert('Failed to update price');
        });
    }
</script>
@endsection