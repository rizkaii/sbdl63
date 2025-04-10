<?php
// Include database connection
require_once '../../config/database.php';

// Process form submission
$message = '';
$messageType = '';
$formData = [
    'id_produk' => '',
    'nama_produk' => '',
    'harga_produk' => '',
    'stok_produk' => '',
    'jenis_produk' => '',
    'exp_produk' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $formData = [
        'id_produk' => trim($_POST['id_produk']),
        'nama_produk' => trim($_POST['nama_produk']),
        'harga_produk' => trim($_POST['harga_produk']),
        'stok_produk' => trim($_POST['stok_produk']),
        'jenis_produk' => trim($_POST['jenis_produk']),
        'exp_produk' => trim($_POST['exp_produk'])
    ];
    
    // Validate form data
    $errors = [];
    
    if (empty($formData['id_produk'])) {
        $errors[] = 'Product ID is required';
    } else {
        // Check if product ID already exists
        $checkQuery = "SELECT id_produk FROM tb_produk WHERE id_produk = '{$formData['id_produk']}'";
        $checkResult = mysqli_query($conn, $checkQuery);
        if (mysqli_num_rows($checkResult) > 0) {
            $errors[] = 'Product ID already exists';
        }
    }
    
    if (empty($formData['nama_produk'])) {
        $errors[] = 'Product name is required';
    }
    
    if (empty($formData['harga_produk'])) {
        $errors[] = 'Product price is required';
    } elseif (!is_numeric($formData['harga_produk'])) {
        $errors[] = 'Product price must be a number';
    }
    
    if (empty($formData['stok_produk'])) {
        $errors[] = 'Product stock is required';
    } elseif (!is_numeric($formData['stok_produk'])) {
        $errors[] = 'Product stock must be a number';
    }
    
    if (empty($formData['jenis_produk'])) {
        $errors[] = 'Product type is required';
    }
    
    if (empty($formData['exp_produk'])) {
        $errors[] = 'Product expiry date is required';
    }
    
    // If no errors, insert data
    if (empty($errors)) {
        $query = "INSERT INTO tb_produk (id_produk, nama_produk, harga_produk, stok_produk, jenis_produk, exp_produk) 
                  VALUES ('{$formData['id_produk']}', '{$formData['nama_produk']}', '{$formData['harga_produk']}', 
                          '{$formData['stok_produk']}', '{$formData['jenis_produk']}', '{$formData['exp_produk']}')";
        
        if (mysqli_query($conn, $query)) {
            $message = 'Product added successfully';
            $messageType = 'success';
            
            // Reset form data after successful submission
            $formData = [
                'id_produk' => '',
                'nama_produk' => '',
                'harga_produk' => '',
                'stok_produk' => '',
                'jenis_produk' => '',
                'exp_produk' => ''
            ];
        } else {
            $message = 'Error adding product: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'danger';
    }
}

// Include header
include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Add New Product</h1>
    <a href="index.php" class="btn btn-secondary">Back to Products</a>
</div>

<?php if (!empty($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_produk" class="form-label">Product ID</label>
                    <input type="text" class="form-control" id="id_produk" name="id_produk" value="<?php echo htmlspecialchars($formData['id_produk']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="nama_produk" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($formData['nama_produk']); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="harga_produk" class="form-label">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga_produk" name="harga_produk" value="<?php echo htmlspecialchars($formData['harga_produk']); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="stok_produk" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stok_produk" name="stok_produk" value="<?php echo htmlspecialchars($formData['stok_produk']); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="jenis_produk" class="form-label">Product Type</label>
                    <input type="text" class="form-control" id="jenis_produk" name="jenis_produk" value="<?php echo htmlspecialchars($formData['jenis_produk']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="exp_produk" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" id="exp_produk" name="exp_produk" value="<?php echo htmlspecialchars($formData['exp_produk']); ?>" required>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include '../../includes/footer.php';
?>