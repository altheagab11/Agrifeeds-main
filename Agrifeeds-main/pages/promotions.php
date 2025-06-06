<?php
session_start();
require_once('../includes/db.php');
$con = new database();
$sweetAlertConfig = "";

// SweetAlert config from session
if (isset($_SESSION['sweetAlertConfig'])) {
    $sweetAlertConfig = $_SESSION['sweetAlertConfig'];
    unset($_SESSION['sweetAlertConfig']);
}

// Handle Add Promotion
if (isset($_POST['add'])) {
    $promCode = $_POST['Prom_Code'];
    $promoDescription = $_POST['Promo_Description'];
    $promoDiscAmnt = $_POST['Promo_DiscAmnt'];
    $promoDiscountType = $_POST['Promo_DiscountType'];
    $promoStartDate = $_POST['Promo_StartDate'];
    $promoEndDate = $_POST['Promo_EndDate'];
    $usageLimit = $_POST['UsageLimit'];
    $promoIsActive = $_POST['Promo_IsActive'];

    $result = $con->addPromotion($promCode, $promoDescription, $promoDiscAmnt, $promoDiscountType, $promoStartDate, $promoEndDate, $usageLimit, $promoIsActive);

    if ($result) {
        $_SESSION['sweetAlertConfig'] = "<script>
            Swal.fire({
                icon: 'success',
                title: 'Promotion Added',
                text: 'A new promotion has been added!',
                confirmButtonText: 'Continue'
            });
        </script>";
    } else {
        $_SESSION['sweetAlertConfig'] = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Something went wrong',
                text: 'Please try again.'
            });
        </script>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all promotions
$allPromotions = $con->viewPromotions();

?>

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
</head>
<body>
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
                </select>
            </div>
        </div>

        <!-- Promotions Table '_' -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Promotion ID</th>
                <th>Promotion Code</th>
                <th>Description</th>
                <th>Discount</th>
                <th>Discount Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Usage Limit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="promotionsTableBody">
            <?php if (!empty($allPromotions)): ?>
            <?php foreach ($allPromotions as $promo): ?>
            <tr>
                <td><?php echo htmlspecialchars($promo['PromotionID']); ?></td>
                <td><?php echo htmlspecialchars($promo['Prom_Code']); ?></td>
                <td><?php echo htmlspecialchars($promo['Promo_Description']); ?></td>
                <td><?php echo htmlspecialchars($promo['Promo_DiscAmnt']); ?></td>
                <td><?php echo htmlspecialchars($promo['Promo_DiscountType']); ?></td>
                <td><?php echo htmlspecialchars($promo['Promo_StartDate']); ?></td>
                <td><?php echo htmlspecialchars($promo['Promo_EndDate']); ?></td>
                <td><?php echo htmlspecialchars($promo['UsageLimit']); ?></td>
                <td>
                    <?php
        $today = date('Y-m-d');
        $start = $promo['Promo_StartDate'];
        $end = $promo['Promo_EndDate'];
        $isActive = $promo['Promo_IsActive'];

        if ($isActive) {
            if ($today < $start) {
                $status = 'Scheduled';
                $badge = 'bg-info text-dark';
            } elseif ($today > $end) {
                $status = 'Expired';
                $badge = 'bg-danger';
            } else {
                $status = 'Active';
                $badge = 'bg-success';
            }
        } else {
            $status = 'Inactive';
            $badge = 'bg-secondary';
        }
        echo "<span class='badge $badge'>$status</span>";
    ?>
                </td>
                <td>
                    <!-- Actions: Edit/Delete can be added here -->
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="10" class="text-center">No promotions found.</td></tr>
    <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Promotion Modal -->
<div class="modal fade" id="addPromotionModal" tabindex="-1" aria-labelledby="addPromotionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addPromotionForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPromotionModalLabel">New Promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="promCode" class="form-label">Promotion Code</label>
                        <input type="text" class="form-control" id="promCode" name="Prom_Code" required>
                    </div>
                    <div class="mb-3">
                        <label for="promoDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="promoDescription" name="Promo_Description" rows="2" required></textarea>
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
                            <label for="promoStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="promoStartDate" name="Promo_StartDate" required>
                        </div>
                        <div class="col-md-6">
                            <label for="promoEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="promoEndDate" name="Promo_EndDate" required>
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
                    <button type="submit" name="add" class="btn btn-primary">Save Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo $sweetAlertConfig; ?>
    
</body>
</html> 