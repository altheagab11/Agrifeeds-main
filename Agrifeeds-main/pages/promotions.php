<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFeeds - Promotions</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/sidebar.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php if (!empty($sweetAlertConfig)) echo $sweetAlertConfig; ?>
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Promotions</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromotionModal">
                <i class="bi bi-plus-lg"></i> New Promotion
            </button>
        </div>

        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="promotionSearch" 
                           placeholder="Search promotions..." aria-label="Search promotions">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="typeFilter">
                    <option value="all">All Types</option>
                    <option value="Discount">Discount</option>
                    <option value="BOGO">Buy One Get One</option>
                    <option value="Bundle">Bundle Deal</option>
                    <option value="Clearance">Clearance</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Expired">Expired</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Promotions Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Promotion Name</th>
                        <th>Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Discount</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="promotionsTableBody">
                    <!-- Table content will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Promotion Modal -->
    <div class="modal fade" id="addPromotionModal" tabindex="-1" aria-labelledby="addPromotionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPromotionModalLabel">New Promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPromotionForm">
                        <div class="mb-3">
                            <label for="promotionName" class="form-label">Promotion Name</label>
                            <input type="text" class="form-control" id="promotionName" required>
                        </div>
                        <div class="mb-3">
                            <label for="promotionType" class="form-label">Promotion Type</label>
                            <select class="form-select" id="promotionType" required>
                                <option value="">Select Type</option>
                                <option value="Discount">Discount</option>
                                <option value="BOGO">Buy One Get One</option>
                                <option value="Bundle">Bundle Deal</option>
                                <option value="Clearance">Clearance</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="promoDiscAmnt" class="form-label">Discount Amount</label>
                                <input type="number" class="form-control" id="promoDiscAmnt" name="Promo_DiscAmnt" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="promoDiscountType" class="form-label">Discount Type</label>
                                <select class="form-select" id="promoDiscountType" name="Promo_DiscountType" required>
                                    <option value="Percentage">Percentage</option>
                                    <option value="Fixed">Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="promoStartDate" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="promoStartDate" name="Promo_StartDate" required>
                            </div>
                            <div class="col-md-6">
                                <label for="promoEndDate" class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="promoEndDate" name="Promo_EndDate" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="usageLimit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" id="usageLimit" name="UsageLimit" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="promoIsActive" class="form-label">Status</label>
                            <select class="form-select" id="promoIsActive" name="Promo_IsActive" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="AddPromotion" class="btn btn-primary">Save Promotion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>