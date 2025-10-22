@extends('layouts.owner')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Tạo Phòng Mới</h1>
            <p class="text-gray-600 mt-2">Thêm thông tin phòng và hình ảnh</p>
        </div>

        <!-- Form -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('rooms.create.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Building Selection -->
                <div>
                    <label for="buildingId" class="block text-sm font-medium text-gray-700 mb-2">
                        Chọn Tòa Nhà *
                    </label>
                    <select id="buildingId" name="buildingId" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="{{ $buildingId }}">-- Chọn tòa nhà --</option>
                        <!-- Options sẽ được load bằng JavaScript -->
                    </select>
                </div>

                <!-- Title & Price -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tiêu Đề *
                        </label>
                        <input type="text" id="title" name="title" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="HaiCanVip2" value="{{ old('title') }}">
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Giá Thuê (VNĐ) *
                        </label>
                        <input type="number" id="price" name="price" required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="6000000" value="{{ old('price') }}">
                    </div>
                </div>

                <!-- Area & Capacity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">
                            Diện Tích (m²) *
                        </label>
                        <input type="number" id="area" name="area" required min="0" step="0.1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="40" value="{{ old('area') }}">
                    </div>
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                            Sức Chứa (người) *
                        </label>
                        <input type="number" id="capacity" name="capacity" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="3" value="{{ old('capacity') }}">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Mô Tả
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Trọ cho người giàu">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Trạng Thái
                    </label>
                    <select id="status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Có sẵn</option>
                        <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Đã thuê</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                    </select>
                </div>

                <!-- Amenities -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tiện Nghi
                    </label>
                    <div class="space-y-2" id="amenitiesContainer">
                        <div class="flex items-center space-x-2">
                            <input type="text" name="amenities[]"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="day du" value="{{ old('amenities.0') }}">
                            <button type="button" onclick="addAmenity()"
                                class="bg-green-500 text-white p-2 rounded-md hover:bg-green-600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="sodien" class="block text-sm font-medium text-gray-700 mb-2">
                            Số Điện
                        </label>
                        <input type="number" id="sodien" name="sodien" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0" value="{{ old('sodien') }}">
                    </div>
                    <div>
                        <label for="ownerId" class="block text-sm font-medium text-gray-700 mb-2">
                            ID Chủ Sở Hữu
                        </label>
                        <input type="text" id="ownerId" name="ownerId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Owner ID" value="{{ $user['id'] }}">
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hình Ảnh Phòng *
                    </label>

                    <!-- File Input -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 mb-2">Kéo thả ảnh vào đây hoặc click để chọn</p>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                        <button type="button" onclick="document.getElementById('images').click()"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-folder-open mr-2"></i>Chọn Ảnh
                        </button>
                    </div>

                    <!-- Selected Files Preview -->
                    <div id="filePreview" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4 hidden">
                        <!-- Files will be previewed here -->
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="window.history.back()"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Quay Lại
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Tạo Phòng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'http://127.0.0.1:8000/api';

        // Load buildings on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadBuildings();
        });

        // Load buildings for dropdown
        async function loadBuildings() {
            try {
                const response = await fetch(`${API_BASE_URL}/buildings`);
                const result = await response.json();

                const select = document.getElementById('buildingId');

                if (response.ok && result.data) {
                    result.data.forEach(building => {
                        const option = document.createElement('option');
                        option.value = building.id;
                        option.textContent = `${building.buildingName} - ${building.address}`;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading buildings:', error);
            }
        }

        // File input change handler
        document.getElementById('images').addEventListener('change', function (e) {
            const preview = document.getElementById('filePreview');
            preview.innerHTML = '';

            if (this.files.length > 0) {
                preview.classList.remove('hidden');

                Array.from(this.files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const div = document.createElement('div');
                            div.className = 'relative group';
                            div.innerHTML = `
                                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-32 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200 rounded-lg">
                                        <button type="button" onclick="removeImage(${index})" class="bg-red-500 text-white p-1 rounded-full">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1 truncate">${file.name}</p>
                                `;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                preview.classList.add('hidden');
            }
        });

        // Remove image from preview
        function removeImage(index) {
            const dt = new DataTransfer();
            const input = document.getElementById('images');
            const files = Array.from(input.files);

            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));

            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }

        // Amenities management
        function addAmenity() {
            const container = document.getElementById('amenitiesContainer');
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2';
            div.innerHTML = `
                    <input type="text" name="amenities[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md" placeholder="Tiện nghi khác">
                    <button type="button" onclick="removeAmenity(this)" class="bg-red-500 text-white p-2 rounded-md hover:bg-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            container.appendChild(div);
        }

        function removeAmenity(button) {
            button.parentElement.remove();
        }

        // Drag and drop functionality
        const dropArea = document.querySelector('.border-dashed');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropArea.classList.add('border-blue-400', 'bg-blue-50');
        }

        function unhighlight() {
            dropArea.classList.remove('border-blue-400', 'bg-blue-50');
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('images').files = files;
            document.getElementById('images').dispatchEvent(new Event('change'));
        }
    </script>
@endsection