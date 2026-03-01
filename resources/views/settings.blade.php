@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Settings</h2>

        <div class="bg-white rounded shadow p-6">
            <div class="space-y-6">
                @if(auth()->user()->role === 'admin')
                <!-- Website Settings -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Website Settings</h3>
                        <button id="btnReloadWebsiteSettings" type="button" onclick="loadWebsiteSettings()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                            <i class="fas fa-sync mr-2"></i> Reload
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                                <input id="ws_time_zone" type="text" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="e.g. UTC or Asia/Kolkata">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <input id="ws_currency" type="text" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="e.g. USD, EUR, INR">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">VAT Percentage (%)</label>
                                <input id="ws_vat_percentage" type="number" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="e.g. 16">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">EXTERNAL_API_UPLOAD_URL</label>
                            <input id="ws_external_api_upload_url" type="url" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="http://.../api/pdf/upload">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">EXTERNAL_API_CREATE_QUOTATION_URL</label>
                            <input id="ws_external_api_create_quotation_url" type="url" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="http://.../api/pdf/create">
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button id="btnSaveWebsiteSettings" type="button" onclick="saveWebsiteSettings()" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                                <i class="fas fa-save mr-2"></i> Save Website Settings
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- VAT Settings per item were removed; VAT is controlled globally via Website Settings. -->

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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function loadWebsiteSettings() {
            const btn = document.getElementById('btnReloadWebsiteSettings');
            if (btn) btn.disabled = true;

            try {
                const res = await fetch('/api/settings');
                const payload = await res.json().catch(() => null);
                if (!res.ok || payload?.status !== 'success') {
                    throw new Error(payload?.message || 'Failed to load website settings');
                }

                const s = payload.data || {};
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val ?? '';
                };

                setVal('ws_time_zone', s.time_zone);
                setVal('ws_currency', s.currency);
                setVal('ws_vat_percentage', s.vat_percentage);
                setVal('ws_external_api_upload_url', s.external_api_upload_url);
                setVal('ws_external_api_create_quotation_url', s.external_api_create_quotation_url);
            } catch (e) {
                alert(e.message || 'Error loading website settings');
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        async function saveWebsiteSettings() {
            const btn = document.getElementById('btnSaveWebsiteSettings');
            if (btn) btn.disabled = true;

            try {
                const body = {
                    time_zone: document.getElementById('ws_time_zone')?.value?.trim() || null,
                    currency: document.getElementById('ws_currency')?.value?.trim() || null,
                    vat_percentage: document.getElementById('ws_vat_percentage')?.value !== '' ? parseFloat(document.getElementById('ws_vat_percentage').value) : null,
                    external_api_upload_url: document.getElementById('ws_external_api_upload_url')?.value?.trim() || null,
                    external_api_create_quotation_url: document.getElementById('ws_external_api_create_quotation_url')?.value?.trim() || null,
                };

                const res = await fetch('/api/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body),
                });

                const payload = await res.json().catch(() => null);
                if (!res.ok || payload?.status !== 'success') {
                    throw new Error(payload?.message || 'Failed to save website settings');
                }

                alert('Website settings saved successfully');
                loadWebsiteSettings();
            } catch (e) {
                alert(e.message || 'Error saving website settings');
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('ws_time_zone')) {
                loadWebsiteSettings();
            }
        });
    </script>
@endsection
