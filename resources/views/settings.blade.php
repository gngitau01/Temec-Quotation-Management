@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Settings</h2>

        <div class="bg-white rounded shadow p-6">
            <div class="space-y-6">
                <!-- Application Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Endpoint</label>
                            <input type="url" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Enter API endpoint URL">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Folder</label>
                            <input type="text" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="storage/uploads">
                        </div>
                    </div>
                </div>

                <!-- VAT Settings -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">VAT Settings (per item)</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Item ID</label>
                                <input id="vat_item_id" type="number" min="1" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Enter item ID">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">VAT Percentage (%)</label>
                                <input id="vat_percentage" type="number" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="e.g. 16">
                            </div>
                            <div class="flex gap-2">
                                <button id="btnUpdateVat" type="button" onclick="updateVatPercentage()" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                                    <i class="fas fa-percentage mr-2"></i> Update VAT
                                </button>
                                <button id="btnFetchPricing" type="button" onclick="fetchItemPricing()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                                    <i class="fas fa-sync mr-2"></i> Fetch
                                </button>
                            </div>
                        </div>

                        <div id="vatResult" class="hidden bg-gray-50 border rounded p-4">
                            <p class="text-sm text-gray-600">Item: <span id="res_item_no">-</span></p>
                            <p class="text-sm text-gray-600">Quantity: <span id="res_quantity">-</span></p>
                            <p class="text-sm text-gray-600">Unit Price: <span id="res_unit_price">-</span></p>
                            <p class="text-sm text-gray-600">Discount: <span id="res_discount">-</span></p>
                            <p class="text-sm text-gray-600">VAT %: <span id="res_vat_percentage">-</span></p>
                            <p class="text-sm text-gray-600">VAT: <span id="res_vat">-</span></p>
                            <p class="text-sm text-gray-600">Total Cost: <span id="res_total_cost">-</span></p>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Change Password</label>
                            <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Save Settings -->
                <div class="border-t pt-6 flex gap-4">
                    <button class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                    <button class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function fetchItemPricing() {
            const itemId = document.getElementById('vat_item_id').value.trim();
            if (!itemId) {
                alert('Please enter an Item ID');
                return;
            }

            const btn = document.getElementById('btnFetchPricing');
            btn.disabled = true;
            btn.textContent = 'Fetching...';

            try {
                const res = await fetch(`/api/item/${itemId}/pricing`);
                if (!res.ok) {
                    const err = await res.json().catch(() => null);
                    throw new Error(err?.message || 'Failed to fetch item pricing');
                }
                const payload = await res.json();
                if (payload.status !== 'success') throw new Error(payload.message || 'Failed to fetch item');

                const data = payload.data;
                document.getElementById('res_item_no').textContent = data.item_no || '-';
                document.getElementById('res_quantity').textContent = data.quantity ?? '-';
                document.getElementById('res_unit_price').textContent = data.unit_price ?? '-';
                document.getElementById('res_discount').textContent = data.discount ?? '-';
                document.getElementById('res_vat_percentage').textContent = data.vat_percentage ?? '-';
                document.getElementById('res_vat').textContent = data.vat ?? '-';
                document.getElementById('res_total_cost').textContent = data.total_cost ?? '-';

                document.getElementById('vatResult').classList.remove('hidden');
            } catch (e) {
                alert(e.message || 'Error fetching item pricing');
            } finally {
                btn.disabled = false;
                btn.textContent = '';
                // restore icon + text
                btn.innerHTML = '<i class="fas fa-sync mr-2"></i> Fetch';
            }
        }

        async function updateVatPercentage() {
            const itemId = document.getElementById('vat_item_id').value.trim();
            const vat = document.getElementById('vat_percentage').value.trim();

            if (!itemId) { alert('Please enter an Item ID'); return; }
            if (vat === '') { alert('Please enter a VAT percentage'); return; }

            const btn = document.getElementById('btnUpdateVat');
            btn.disabled = true;
            btn.textContent = 'Updating...';

            try {
                const res = await fetch(`/api/item/${itemId}/vat-percentage`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ vat_percentage: parseFloat(vat) })
                });

                const payload = await res.json().catch(() => null);
                if (!res.ok || payload?.status !== 'success') {
                    throw new Error(payload?.message || 'Failed to update VAT');
                }

                alert('VAT percentage updated successfully');
                // refresh pricing
                fetchItemPricing();
            } catch (e) {
                alert(e.message || 'Error updating VAT');
            } finally {
                btn.disabled = false;
                btn.textContent = '';
                btn.innerHTML = '<i class="fas fa-percentage mr-2"></i> Update VAT';
            }
        }
    </script>
@endsection
