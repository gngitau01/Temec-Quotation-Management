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
            <h3 class="text-lg font-semibold text-gray-900 p-6 border-b">Quotation Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Item #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Unit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Unit Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Discount</th>
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
                </table>
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

<script>
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
        const quotation = data.quotation;
        const vendor = data.vendor;
        const buyer = data.buyer;
        const items = data.items || [];

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
        } else {
            tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                            No items found
                        </td>
                    </tr>
                `;
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
</script>
@endsection