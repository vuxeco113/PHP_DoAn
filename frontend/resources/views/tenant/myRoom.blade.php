@extends('layouts.app')

@section('content')

@if($room)
<x-bladewind::card compact="true">
    <div class="flex items-center">
        <div>
            <img src="{{ asset($room['imageUrls'][0]) }}" alt="Hình phòng" class="w-full h-48 object-cover rounded-t-md">
        </div>
        <div class="grow pl-2 pt-1">
            <h2 class="text-2xl font-bold mb-2">{{ $room['title'] }}</h2>
            <x-bladewind::tag color="green">{{ $room['status'] }}</x-bladewind::tag>

            <table class="w-full mt-4">
                <tr>
                    <td class="font-medium">Giá/đêm:</td>
                    <td>{{ number_format($room['price']) }} VND</td>
                </tr>
                <tr>
                    <td class="font-medium">Sức chứa:</td>
                    <td>{{ $room['capacity'] }} người</td>
                </tr>
                <tr>
                    <td class="font-medium">Diện tích:</td>
                    <td>{{ $room['area'] }} m²</td>
                </tr>
                <!-- thêm các mục khác -->
            </table>

            <p class="mt-4 text-gray-600">{{ $room['description'] }}</p>
            <div class="flex space-x-3 mt-5">
                <x-bladewind::button color="yellow" onclick="showModal('repair-request-modal')">
                    🛠️ Yêu cầu sửa chữa
                </x-bladewind::button>

                {{-- Modal chứa form --}}
                <x-bladewind::modal
                    size="large"
                    title="Gửi yêu cầu sửa chữa"
                    name="repair-request-modal"
                    show_action_buttons="false">

                    <form id="repair-request-form" method="POST" action="{{ route('tenant.requests.create_SuaChua') }}">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room['id'] }}">
                        <input type="hidden" name="loai_request" value="sua_chua">

                        <div class="space-y-4">
                            <x-bladewind::input
                                name="name"
                                label="Họ tên"
                                required="true"
                                placeholder="Nhập họ tên người yêu cầu" />

                            <x-bladewind::input
                                name="sdt"
                                label="Số điện thoại"
                                required="true"
                                placeholder="Nhập số điện thoại liên hệ" />

                            <x-bladewind::textarea
                                name="mo_ta"
                                label="Mô tả chi tiết vấn đề cần sửa"
                                placeholder="Ví dụ: Vòi nước bị rò rỉ, đèn không sáng..."
                                rows="3" />
                        </div>

                        <div class="flex justify-end mt-6 space-x-3">
                            <x-bladewind::button color="gray" onclick="hideModal('repair-request-modal')" html_type="button">
                                Hủy
                            </x-bladewind::button>

                            {{-- ✅ Nút này thực sự submit form POST --}}
                            <button color="green" type="submit" form="repair-request-form" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                Gửi yêu cầu
                            </button>
                        </div>
                    </form>
                </x-bladewind::modal>

                <x-bladewind::button color="green" onclick="showModal('traphong-request-modal')">
                    🛠️ Yêu cầu trả phòng
                </x-bladewind::button>

                {{-- Modal chứa form --}}
                <x-bladewind::modal
                    size="large"
                    title="Gửi yêu cầu sửa chữa"
                    name="traphong-request-modal"
                    show_action_buttons="false">

                    <form id="traphong-request-modal" method="POST" action="{{ route('tenant.requests.create_TraPhong') }}">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room['id'] }}">
                        <input type="hidden" name="loai_request" value="tra_phong">

                        <div class="space-y-4">
                            <x-bladewind::input
                                name="name"
                                label="Họ tên"
                                required="true"
                                placeholder="Nhập họ tên người yêu cầu" />

                            <x-bladewind::input
                                name="sdt"
                                label="Số điện thoại"
                                required="true"
                                placeholder="Nhập số điện thoại liên hệ" />

                            <x-bladewind::textarea
                                name="mo_ta"
                                label="Mô tả chi tiết vấn đề cần sửa"
                                placeholder="Ví dụ: Tôi muốn trả phòng vì lý do..."
                                rows="3" />
                        </div>

                        <div class="flex justify-end mt-6 space-x-3">
                            <x-bladewind::button color="gray" onclick="hideModal('repair-request-modal')" html_type="button">
                                Hủy
                            </x-bladewind::button>

                            {{-- ✅ Nút này thực sự submit form POST --}}
                            <button color="green" type="submit" form="traphong-request-modal" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                Gửi yêu cầu
                            </button>
                        </div>
                    </form>
                </x-bladewind::modal>
            </div>
        </div>
        <div>
            <a href="">
                <svg>
                    ...
                </svg>
            </a>
        </div>
    </div>
</x-bladewind::card>
@else
<h3>Bạn chưa thuê phòng nào </h3>
@endif

{{-- BẢNG 1 --}}
<div class="mt-6">
    <h3 class="text-lg font-semibold mb-2 text-gray-700">Hợp đồng của tôi</h3>

    <div class="max-h-40 overflow-y-auto rounded-lg border border-gray-200 shadow-sm">
        <x-bladewind::table>
            <x-slot name="header">
                <th>Mã phòng</th>
                <th>Tiền cọc</th>
                <th>Tiền thuê</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Điều khoản hợp đồng</th>
                <th>Trạng thái</th>
            </x-slot>
            @foreach($contracts as $contract)
            <tr>
                <td>{{ $contract['room_id'] }}</td>
                <td>{{ $contract['deposit_amount'] }}</td>
                <td>{{ $contract['rent_amount'] }}</td>
                <td>{{ date('d/m/Y', strtotime($contract['start_date'])) }}</td>
                <td>{{ date('d/m/Y', strtotime($contract['end_date'])) }}</td>
                <td>{{ $contract['terms_and_conditions'] }}</td>
                <td>{{ $contract['status'] }}</td>
            </tr>
            @endforeach
        </x-bladewind::table>
    </div>
</div>


{{-- BẢNG 2 --}}
<div class="mt-6">
    <h3 class="text-lg font-semibold mb-2 text-gray-700">Danh sách yêu cầu</h3>

    <div class="max-h-40 overflow-y-auto rounded-lg border border-gray-200 shadow-sm">
        <x-bladewind::table>
            <x-slot name="header">
                <th>Mã phòng</th>
                <th>Tên người gửi</th>
                <th>Sdt</th>
                <th>Ngày gửi</th>
                <th>Mô tả</th>
                <th>Loại yêu cầus</th>
                <th>Trạng thái</th>
            </x-slot>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request['room_id'] }}</td>
                <td>{{ $request['name'] }}</td>
                <td>{{ $request['sdt'] }}</td>
                <td>{{ date('d/m/Y', strtotime($request['thoi_gian'])) }}</td>

                <td>{{ $request['mo_ta'] }}</td>
                <td>{{ $request['loai_request'] }}</td>
                <td>{{ $request['status'] }}</td>
            </tr>
            @endforeach
        </x-bladewind::table>
    </div>
</div>

@endsection