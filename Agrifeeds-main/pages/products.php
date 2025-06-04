<?php 
session_start();

require_once('../includes/db.php');
$con = new database();
$sweetAlertConfig = "";

$allProducts = $con->viewProducts();
$totalProducts = count($allProducts);

$lowStockItems = 0;
$outOfStock = 0;
$activeProducts = 0;

foreach ($allProducts as $prod) {
    if ($prod['Prod_Stock'] == 0) {
        $outOfStock++;
    } elseif ($prod['Prod_Stock'] > 0 && $prod['Prod_Stock'] <= 10) {
        $lowStockItems++;
        $activeProducts++;
    } else {
        $activeProducts++;
    }
}


if (isset($_POST['add'])) {

  $productName = $_POST['productName'];
  $category = $_POST['category'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $productID = $con->addProduct($productName, $category, $description, $price, $stock);


  if ($productID) {

    $sweetAlertConfig = "
    <script>
    
    Swal.fire({
        icon: 'success',
        title: 'Book Has Been Added Successfully',
        text: 'A new book has been added to the library!',
        confirmationButtontext: 'Continue'
     }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'admin_homepage.php'
        }
            });

    </script>";

  } else {

    $sweetAlertConfig = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Something went wrong',
                text: 'Please try again.'
            });
        </script>";

  }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriFeeds - Products</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/sidebar.css" rel="stylesheet">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/sidebar.php';  ?>

    <!-- Main Content -->
    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-lg"></i> Add Product
            </button>
        </div>

        <!-- Product Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text" id="totalProducts"><?php echo $totalProducts; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Low Stock Items</h5>
                        <p class="card-text" id="lowStockItems"><?php echo $lowStockItems; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Out of Stock</h5>
                        <p class="card-text" id="outOfStock"><?php echo $outOfStock; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5 class="card-title">Active Products</h5>
                        <p class="card-text" id="activeProducts"><?php echo $activeProducts; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="productSearch" 
                           placeholder="Search products..." aria-label="Search products">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="categoryFilter">
                    <option value="all">All Categories</option>
                    <option value="feed">Animal Feed</option>
                    <option value="supplements">Supplements</option>
                    <option value="equipment">Equipment</option>
                    <option value="accessories">Accessories</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="stockFilter">
                    <option value="all">All Stock Status</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
            
            $data = $con->viewProducts();
            foreach ($data as $rows) {

                if ($rows['Prod_Stock'] == 0) {
                            $statusClass = 'bg-danger';
                            $status = 'Out of Stock';
                        } elseif ($rows['Prod_Stock'] > 0 && $rows['Prod_Stock'] <= 10) {
                            $statusClass = 'bg-warning text-dark';
                            $status = 'Low Stock';
                        } else {
                            $statusClass = 'bg-success';
                            $status = 'In Stock';
                        }

            ?>

              <tr>
                <td><?php echo $rows['ProductID']?></td>
                <td><?php echo $rows['Prod_Name']?></td>
                <td><?php echo $rows['Prod_Cat']?></td>
                <td><?php echo $rows['Prod_Desc']?></td>
                <td>₱<?php echo number_format($rows['Prod_Price'], 2); ?></td>
                <td><?php echo $rows['Prod_Stock']?></td>
                <td><span class="badge <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                <td>
                  <div class="btn-group" role="group">
                    <form action="update_products.php" method="post">
                    
                    <input type="hidden" name="id" value="<?php echo $rows['ProductID']; ?>">  
                    <button type="submit" class="btn btn-warning btn-sm">
                      <i class="bi bi-pencil-square"></i>
                    </button>
  
                    </form>
                    
                    <form method="POST" class="mx-1">
                      <input type="hidden" name="id" value="<?php echo $rows['ProductID']; ?>">
                      <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                        <i class="bi bi-x-square"></i>
                      </button>
                    </form>
        </div>
 
                </td>
              </tr>

              <?php
            }
            ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" method="POST">
                        <input type="hidden" name="add_product" value="1">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="feed">Animal Feed</option>
                                <option value="supplements">Supplements</option>
                                <option value="equipment">Equipment</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Initial Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add" class="btn btn-primary">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    
</body>
</html> 