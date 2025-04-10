<?php
// Include database connection
require_once '../../config/database.php';

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Check if the customer exists
$checkQuery = "SELECT id_customer FROM tb_customer WHERE id_customer = '$id'";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) == 0) {
    header('Location: index.php');
    exit();
}

// Check if customer has related purchases
$relatedPurchasesQuery = "SELECT COUNT(*) as count FROM tb_pembelian WHERE id_customer = '$id'";
$relatedPurchasesResult = mysqli_query($conn, $relatedPurchasesQuery);
$relatedPurchasesCount = mysqli_fetch_assoc($relatedPurchasesResult)['count'];

// Process deletion
if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
    // Delete customer (and related purchases if force delete is checked)
    if ($relatedPurchasesCount > 0 && isset($_POST['force_delete']) && $_POST['force_delete'] == 'yes') {
        // First delete related purchases
        $deletePurchasesQuery = "DELETE FROM tb_pembelian WHERE id_customer = '$id'";
        mysqli_query($conn, $deletePurchasesQuery);
    }
    
    // Delete customer
    $deleteQuery = "DELETE FROM tb_customer WHERE id_customer = '$id'";
    
    if (mysqli_query($conn, $deleteQuery)) {
        // Redirect to customers page with success message
        header('Location: index.php?message=Customer deleted successfully&type=success');
        exit();
    } else {
        // Redirect to customers page with error message
        header('Location: index.php?message=Error deleting customer: ' . mysqli_error($conn) . '&type=danger');
        exit();
    }
}

// Include header
include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Delete Customer</h1>
    <a href="index.php" class="btn btn-secondary">Back to Customers</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-danger">
            <h4 class="alert-heading">Warning!</h4>
            <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
            
            <?php if ($relatedPurchasesCount > 0): ?>
            <hr>
            <p class="mb-0">This customer has <?php echo $relatedPurchasesCount; ?> related purchase record(s). Deleting this customer will affect these records.</p>
            <?php endif; ?>
        </div>
        
        <?php
        // Get customer details for confirmation
        $customerQuery = "SELECT * FROM tb_customer WHERE id_customer = '$id'";
        $customerResult = mysqli_query($conn, $customerQuery);
        $customer = mysqli_fetch_assoc($customerResult);
        ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Details</h5>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($customer['id_customer']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['nama_customer']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['alamat_customer']); ?></p>
                <p><strong>WhatsApp:</strong> <?php echo htmlspecialchars($customer['no_wa_cutomer']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email_customer']); ?></p>
            </div>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="confirm_delete" value="yes">
            
            <?php if ($relatedPurchasesCount > 0): ?>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="force_delete" value="yes" id="force_delete">
                <label class="form-check-label" for="force_delete">
                    I understand that deleting this customer will also delete all related purchase records
                </label>
            </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="index.php" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-danger">Delete Customer</button>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include '../../includes/footer.php';
?>