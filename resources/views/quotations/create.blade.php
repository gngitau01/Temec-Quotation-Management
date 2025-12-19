@extends('layouts.app')

@section('title', 'Upload Quotation')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Upload Quotation</h2>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quotations.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded shadow">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded px-4 py-2 @error('customer_id') border-red-500 @enderror" required>
                    <option value="">Select a customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded p-8 text-center cursor-pointer hover:border-purple-500 transition">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4 block"></i>
                    <p class="text-gray-600">Drag and drop your file here or click to select</p>
                    <p class="text-xs text-gray-500 mt-2">PDF, XLS, XLSX, CSV, TXT (Max 2MB)</p>
                </div>
                <input type="file" name="file" id="fileInput" class="hidden" accept=".pdf,.xls,.xlsx,.csv,.txt" required>
                <p id="fileName" class="text-sm text-gray-700 mt-2"></p>
                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                    <i class="fas fa-upload mr-2"></i> Upload
                </button>
                <a href="{{ route('quotations.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileName = document.getElementById('fileName');

        dropZone.addEventListener('click', () => fileInput.click());
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-purple-500', 'bg-purple-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-purple-500', 'bg-purple-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-purple-500', 'bg-purple-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileName.textContent = `Selected: ${files[0].name}`;
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                fileName.textContent = `Selected: ${e.target.files[0].name}`;
            }
        });
    </script>
@endsection
