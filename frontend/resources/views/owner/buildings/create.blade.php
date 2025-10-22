@extends('layouts.owner')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Tạo Tòa Nhà Mới</h1>
            <p class="text-gray-600 mt-2">Thêm thông tin tòa nhà và hình ảnh</p>
        </div>

        <!-- Form -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <form id="createBuildingForm" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Building Name -->
                <div>
                    <label for="buildingName" class="block text-sm font-medium text-gray-700 mb-2">
                        Tên Tòa Nhà *
                    </label>
                    <input type="text" id="buildingName" name="buildingName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Nhập tên tòa nhà" value="Sunshine Apartments Tran Khanhivu">
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Địa Chỉ *
                    </label>
                    <textarea id="address" name="address" required rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Nhập địa chỉ đầy đủ">123 Sunshine Street, District 1, HCMC</textarea>
                </div>

                <!-- Coordinates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Latitude -->
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Vĩ Độ *
                        </label>
                        <input type="number" id="latitude" name="latitude" step="any" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="10.823099" value="10">
                    </div>

                    <!-- Longitude -->
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Kinh Độ *
                        </label>
                        <input type="number" id="longitude" name="longitude" step="any" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="106.629662" value="10">
                    </div>
                </div>

                <!-- Total Rooms & Manager ID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Total Rooms -->
                    <div>
                        <label for="totalRooms" class="block text-sm font-medium text-gray-700 mb-2">
                            Tổng Số Phòng *
                        </label>
                        <input type="number" id="totalRooms" name="totalRooms" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="30" value="30">
                    </div>

                    <!-- Manager ID -->
                    <div>
                        <label for="managerId" class="block text-sm font-medium text-gray-700 mb-2">
                            ID Quản Lý *
                        </label>
                        <input type="number" id="managerId" name="managerId" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="1" value="{{ $user['id'] }}">
                    </div>
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hình Ảnh Tòa Nhà *
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
                        <i class="fas fa-plus mr-2"></i>Tạo Tòa Nhà
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading Spinner -->
        <div id="loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mr-3"></i>
                <span class="text-gray-700">Đang xử lý...</span>
            </div>
        </div>
    </div>

    <script>
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

        // Form submission
        document.getElementById('createBuildingForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const loading = document.getElementById('loading');

            // Show loading
            loading.classList.remove('hidden');

            try {
                const response = await fetch('http://127.0.0.1:8000/api/buildings/create', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    // Success
                    showNotification('Tạo tòa nhà thành công!', 'success');
                    setTimeout(() => {
                        window.location.href = '/dashboard'; // Redirect to buildings list
                    }, 2000);
                } else {
                    // Error
                    let errorMessage = 'Có lỗi xảy ra!';
                    if (result.errors) {
                        errorMessage = Object.values(result.errors).flat().join(', ');
                    } else if (result.message) {
                        errorMessage = result.message;
                    }
                    showNotification(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Lỗi kết nối! Vui lòng thử lại.', 'error');
            } finally {
                // Hide loading
                loading.classList.add('hidden');
            }
        });

        // Notification function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
            notification.innerHTML = `
                            <div class="flex items-center">
                                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                                <span>${message}</span>
                            </div>
                        `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
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