@extends('layouts.owner')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tạo Phiếu Bồi Thường</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.compensations.store', ['room_id' =>$roomId]) }}" method="POST" id="compensationForm">
                        @csrf
                        
                        <!-- Mini Tabs Navigation -->
                        <ul class="nav nav-tabs mb-4" id="compensationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
                                    <i class="fas fa-list"></i> Danh Sách Hư Hại
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contract-tab" data-bs-toggle="tab" data-bs-target="#contract" type="button" role="tab">
                                    <i class="fas fa-file-contract"></i> Thông Tin Hợp Đồng
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                                    <i class="fas fa-money-bill"></i> Thanh Toán & Hoàn Trả
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="compensationTabContent">
                            
                            <!-- Tab 1: Items -->
                            <div class="tab-pane fade show active" id="items" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Danh Sách Hư Hại</h5>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                                        <i class="fas fa-plus"></i> Thêm Hạng Mục
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">STT</th>
                                                <th width="40%">Mô Tả Hư Hại</th>
                                                <th width="20%">Chi Phí Bồi Thường</th>
                                                <th width="60">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            <tr>
                                                <td class="text-center item-stt">1</td>
                                                <td>
                                                    <textarea name="items[0][info]" class="form-control" rows="2" placeholder="Nhập mô tả hư hại..." required></textarea>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[0][cost]" class="form-control item-cost" placeholder="0" min="0" step="1000" required onchange="calculateTotal()">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td colspan="2" class="text-end fw-bold">Tổng Chi Phí:</td>
                                                <td colspan="2">
                                                    <strong id="totalCost">0 VNĐ</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab 2: Contract & Violations -->
                            <div class="tab-pane fade" id="contract" role="tabpanel">
                                <h5 class="mb-3">Thông Tin Hợp Đồng</h5>
                                
                                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-info">
                                            <div class="card-body">
                                                <h6 class="text-info mb-3">Thông Tin Chung</h6>
                                                <p><strong>Mã HĐ:</strong> {{ $contract->id }}</p>
                                                <p><strong>Ngày bắt đầu:</strong> {{ date('d/m/Y', strtotime($contract->start_date)) }}</p>
                                                <p><strong>Ngày kết thúc:</strong> {{ date('d/m/Y', strtotime($contract->end_date)) }} - {{ date('d/m/Y', strtotime($contract->end_date)) }}</p>
                                                <p><strong>Trạng thái:</strong> 
                                                    <span class="badge bg-success">{{ ucfirst($contract->status) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <h6 class="text-warning mb-3">Thông Tin Tài Chính</h6>
                                                <p><strong>Giá thuê:</strong> {{ number_format($contract->rent_amount) }} VNĐ</p>
                                                <p><strong>Tiền cọc:</strong> {{ number_format($contract->deposit_amount) }} VNĐ</p>
                                                <p><strong>Bên cho thuê:</strong> {{ $contract->owner->name }}</p>
                                                <p><strong>Bên thuê:</strong> {{ $contract->tenant->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-secondary mb-3">
                                    <div class="card-body">
                                        <p><strong>Tên phòng:</strong> {{ $contract->room->title }}</p>
                                        <p><strong>Mô tả:</strong> {{  $contract->room->description }}</p>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mb-3">Vi Phạm Điều Khoản Hợp Đồng</h5>
                                
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addViolation()">
                                        <i class="fas fa-plus"></i> Thêm Vi Phạm
                                    </button>
                                </div>

                                <div id="violationsContainer">
                                    <div class="violation-item card mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <label class="form-label">Điều Khoản Vi Phạm</label>
                                                    <select name="violation_terms[]" class="form-select mb-2">
                                                        <option value="">-- Chọn loại vi phạm --</option>
                                                        @foreach($violationTemplates as $template)
                                                        <option value="{{ $template }}">{{ $template }}</option>
                                                        @endforeach
                                                    </select>
                                                    <textarea name="violation_details[]" class="form-control" rows="2" placeholder="Mô tả chi tiết vi phạm..."></textarea>
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeViolation(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Payment -->
                            <div class="tab-pane fade" id="payment" role="tabpanel">
                                <h5 class="mb-4">Thanh Toán & Hoàn Trả Tiền Cọc</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-primary mb-4">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">Thông Tin Tiền Cọc</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Số Tiền Cọc Ban Đầu</label>
                                                    <input type="text" class="form-control" value="{{ number_format($contract->deposit_amount) }} VNĐ" readonly>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Tổng Chi Phí Bồi Thường</label>
                                                    <input type="text" id="totalCompensation" class="form-control" value="0 VNĐ" readonly>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Số Tiền Hoàn Trả</label>
                                                    <input type="text" id="refundAmount" class="form-control fw-bold text-success" value="0 VNĐ" readonly>
                                                    <input type="hidden" name="refund_amount" id="refundAmountValue" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-info mb-4">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">Thông Tin Thanh Toán</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Phương Thức Thanh Toán</label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="">-- Chọn phương thức --</option>
                                                        <option value="cash">Tiền Mặt</option>
                                                        <option value="bank_transfer">Chuyển Khoản</option>
                                                        <option value="check">Séc</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Ngày Thanh Toán</label>
                                                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Ghi Chú Thanh Toán</label>
                                                    <textarea name="payment_note" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Lưu ý:</strong> Số tiền hoàn trả = Tiền cọc - Tổng chi phí bồi thường. Nếu chi phí bồi thường lớn hơn tiền cọc, bên thuê sẽ phải bồi thường thêm.
                                </div>
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="mt-4 text-end">
                            <a href="#" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu Phiếu Bồi Thường
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
    }
    .violation-item {
        border-left: 4px solid #dc3545;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<script>
let itemIndex = 1;
const contractDeposit = <?php echo json_encode($contract->deposit_amount); ?>;

function addItem() {
    const tbody = document.getElementById('itemsTableBody');
    const newRow = document.createElement('tr');
    const stt = tbody.children.length + 1;
    
    newRow.innerHTML = `
        <td class="text-center item-stt">${stt}</td>
        <td>
            <textarea name="items[${itemIndex}][info]" class="form-control" rows="2" placeholder="Nhập mô tả hư hại..." required></textarea>
        </td>
        <td>
            <input type="number" name="items[${itemIndex}][cost]" class="form-control item-cost" placeholder="0" min="0" step="1000" required onchange="calculateTotal()">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    itemIndex++;
}

function removeItem(button) {
    const row = button.closest('tr');
    row.remove();
    updateSTT();
    calculateTotal();
}

function updateSTT() {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    rows.forEach((row, index) => {
        row.querySelector('.item-stt').textContent = index + 1;
    });
}
const depositAmount = <?php echo json_encode($contract->deposit_amount); ?>;
function calculateTotal() {
    const costs = document.querySelectorAll('.item-cost');
    let total = 0;
    
    costs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    document.getElementById('totalCost').textContent = formatCurrency(total);
    document.getElementById('totalCompensation').value = formatCurrency(total);
    
    const refund = depositAmount - total;
    document.getElementById('refundAmount').value = formatCurrency(refund);
    document.getElementById('refundAmountValue').value = refund;
    
    if (refund < 0) {
        document.getElementById('refundAmount').classList.remove('text-success');
        document.getElementById('refundAmount').classList.add('text-danger');
    } else {
        document.getElementById('refundAmount').classList.remove('text-danger');
        document.getElementById('refundAmount').classList.add('text-success');
    }
}

function addViolation() {
    const container = document.getElementById('violationsContainer');
    const newViolation = document.createElement('div');
    newViolation.className = 'violation-item card mb-3';
    newViolation.innerHTML = `
        <div class="card-body">
            <div class="row">
                <div class="col-md-11">
                    <label class="form-label">Điều Khoản Vi Phạm</label>
                    <select name="violation_terms[]" class="form-select mb-2">
                        <option value="">-- Chọn loại vi phạm --</option>
                        @foreach($violationTemplates as $template)
                        <option value="{{ $template }}">{{ $template }}</option>
                        @endforeach
                    </select>
                    <textarea name="violation_details[]" class="form-control" rows="2" placeholder="Mô tả chi tiết vi phạm..."></textarea>
                </div>
                <div class="col-md-1 text-center">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeViolation(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newViolation);
}

function removeViolation(button) {
    const violationItem = button.closest('.violation-item');
    violationItem.remove();
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endsection