
<div class="card mb-3">
	<!-- Header for Summary of Fines -->
        <div class="card">
            <div class="card-header">
                <div class="row flex-between-center">
                    <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                        <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">Summary of Fines</h5>
                    </div>
                    <div class="col-8 col-sm-auto text-end ps-2">
                        <div class="d-none" id="table-customers-actions"></div>
                        <div id="table-customers-replace-element">
                            <!-- Filter button -->
                            <button class="btn btn-falcon-default btn-sm mx-2" type="button" id="filterButton">
                                <span class="fas fa-filter" data-fa-transform="shrink-3 down-2"></span>
                                <span class="d-none d-sm-inline-block ms-1">Filter</span>
                            </button>
                            <button class="btn btn-falcon-default btn-sm " type="button">
                                <span class="fas fa-external-link-alt" data-fa-transform="shrink-3 down-2"></span>
                                <span class="d-none d-sm-inline-block ms-1">Export</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Fines Table -->
                <table class="table-container" style="width: 100%; table-layout: fixed; border: 1px solid #ddd;">
                    <thead class="bg-200">
                        <tr>
                            <th style="border: 1px solid #ddd; word-wrap: break-word;">Source of Fine</th>
                            <th style="border: 1px solid #ddd; word-wrap: break-word;">Total Amount</th>
                            <th style="border: 1px solid #ddd; word-wrap: break-word;">Status</th>
                            <th style="border: 1px solid #ddd; word-wrap: break-word;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-bs-toggle="modal" data-bs-target="#organizationModal">
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">Organization</td>
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">₱500</td>
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">Unpaid</td>
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">
                                <button class="btn btn-primary btn-sm pay-btn" data-bs-toggle="modal" data-bs-target="#payModal">PAY</button>
                            </td>
                        </tr>
                        <tr data-bs-toggle="modal" data-bs-target="#departmentModal">
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">Department</td>
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">₱1,000</td>
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">Paid</td>
                            <td style="border: 1px solid #ddd; word-wrap: break-word;">
                                <button class="btn btn-primary btn-sm pay-btn" disabled>PAY</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Total Fines Section -->
                <div class="bg-200" style="margin-top: 20px; padding: 10px 20px; border-radius: 8px; font-weight: bold;">
                    <div>Total Fines Imposed: ₱1,500</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Modal -->
    <div class="modal fade" id="organizationModal" tabindex="-1" aria-labelledby="organizationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" style="color: white;" id="organizationModalLabel">Breakdown of Fines: Organization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead class="bg-200">
                            <tr>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">Activity</th>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">Date</th>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">AM IN</th>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">AM OUT</th>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">PM IN</th>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">PM OUT</th>
							<th style="border: 1px solid #ddd; word-wrap: break-word;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Activity 1</td>
                                <td>2025-01-01</td>
                                <td>8:00 AM</td>
                                <td>10:00 AM</td>
                                <td>1:00 PM</td>
                                <td>3:00 PM</td>
                                <td>Completed</td>
                            </tr>
                            <tr>
                                <td>Activity 2</td>
                                <td>2025-01-02</td>
                                <td>9:00 AM</td>
                                <td>11:00 AM</td>
                                <td>2:00 PM</td>
                                <td>4:00 PM</td>
                                <td>Pending</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="departmentModalLabel">Breakdown of Fines: Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Activity</th>
                                <th>Date</th>
                                <th>AM IN</th>
                                <th>AM OUT</th>
                                <th>PM IN</th>
                                <th>PM OUT</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Activity A</td>
                                <td>2025-01-03</td>
                                <td>7:00 AM</td>
                                <td>9:00 AM</td>
                                <td>12:00 PM</td>
                                <td>2:00 PM</td>
                                <td>Completed</td>
                            </tr>
                            <tr>
                                <td>Activity B</td>
                                <td>2025-01-04</td>
                                <td>8:30 AM</td>
                                <td>10:30 AM</td>
                                <td>1:30 PM</td>
                                <td>3:30 PM</td>
                                <td>Completed</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
   <!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="payModalLabel">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Amount to Pay</label>
                        <input type="text" class="form-control" id="paymentAmount" readonly value="">
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" onchange="checkPaymentMethod()">
                            <option selected disabled>Select a method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Confirm Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cash Payment Instructions Modal -->
<!-- Cash Payment Instructions Modal -->
<div class="modal fade" id="cashInstructionsModal" tabindex="-1" aria-labelledby="cashInstructionsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"> <!-- Changed bg-warning to bg-primary -->
			<h5 class="modal-title" style="color: white;" id="cashInstructionsLabel">Cash Payment Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol>
                    <li>Go to the school cashier's office.</li>
                    <li>Inform them about your payment details.</li>
                    <li>Provide your student ID for verification.</li>
                    <li>Pay the required amount in cash.</li>
                    <li>Obtain the official receipt as proof of payment.</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<!-- <-- for how to pay thru cash -->
<script>
function checkPaymentMethod() {
    var paymentMethod = document.getElementById('paymentMethod').value;
    
    if (paymentMethod === 'cash') {
        var cashInstructionsModal = new bootstrap.Modal(document.getElementById('cashInstructionsModal'));
        cashInstructionsModal.show();
    }
}
</script>

