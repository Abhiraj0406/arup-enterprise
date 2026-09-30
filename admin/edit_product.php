<?php
// admin/edit_product.php - Edit Product
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db.php";

date_default_timezone_set('Asia/Kolkata');

// Check if $conn is defined
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error. Please check db.php");
}

// ================= GET PRODUCT ID =================
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id == 0) {
    $_SESSION['error'] = "Invalid product ID";
    header("Location: products.php");
    exit();
}

// ================= FETCH PRODUCT =================
$product = null;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "Product not found";
    header("Location: products.php");
    exit();
}

// ================= FETCH CATEGORIES =================
$categories = [];
$catResult = $conn->query("SELECT * FROM categories WHERE status='active' ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

$page_title = "Edit Product";
$page_icon = "edit";

// ================= UPDATE PRODUCT =================
if (isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $sub_category = trim($_POST['sub_category'] ?? '');
    $description = trim($_POST['description']);
    $features = trim($_POST['features']);
    $specifications = trim($_POST['specifications']);
    $status = trim($_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $sort_order = intval($_POST['sort_order']);
    
    // ✅ Handle Image Upload
    $image_path = $product['image'];
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        if (in_array($file_ext, $allowed_ext)) {
            // Delete old image
            if (!empty($product['image']) && file_exists("../" . $product['image'])) {
                unlink("../" . $product['image']);
            }
            $file_name = strtolower(str_replace(' ', '-', $name)) . '-' . time() . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $image_path = 'assets/uploads/products/' . $file_name;
            }
        }
    }
    
    // If image URL provided
    if (!empty($_POST['image_url'])) {
        $image_path = trim($_POST['image_url']);
    }
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    if (empty($slug)) $slug = 'product-' . time();

    // Ensure slug uniqueness
    $slugCheck = $conn->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
    $slugCheck->bind_param("si", $slug, $product_id);
    $slugCheck->execute();
    if ($slugCheck->get_result()->num_rows > 0) {
        $slug .= '-' . time();
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, slug = ?, category = ?, sub_category = ?, description = ?, features = ?, specifications = ?, image = ?, status = ?, featured = ?, sort_order = ? WHERE id = ?");
    $stmt->bind_param("sssssssssiii", $name, $slug, $category, $sub_category, $description, $features, $specifications, $image_path, $status, $featured, $sort_order, $product_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product '{$name}' updated successfully!";
        header("Location: products.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Arup Enterprise Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #C9920A;
            --gold-dark: #a87a08;
            --gold-light: #e6c9a0;
            --cream: #fef7ed;
            --ivory: #faf3e8;
            --mustard: #d4a373;
            --charcoal: #1e1e1e;
            --mid-gray: #4a3f37;
            --light-gray: #e8ddd0;
            --white: #ffffff;
            --shadow-gold: 0 4px 20px rgba(201,146,10,0.15);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            font-size: 14px;
        }

        .admin-header {
            background: var(--white);
            border-radius: 16px;
            padding: 16px 24px;
            margin-bottom: 24px;
            border: 1px solid rgba(201,146,10,0.12);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }
        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--mustard), var(--gold-light));
        }

        .admin-header .title {
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--charcoal);
            margin: 0;
        }
        .admin-header .title i {
            color: var(--gold);
            margin-right: 10px;
        }
        .admin-header .title span { color: var(--gold); }
        .admin-header .subtitle {
            font-size: 0.75rem;
            color: var(--mid-gray);
            margin: 0;
        }

        .alert-custom {
            background: #f0fdf4;
            border-left: 4px solid var(--gold);
            border-radius: 12px;
            padding: 12px 18px;
            color: #166534;
            font-weight: 500;
            font-size: 0.85rem;
            border: 1px solid rgba(201,146,10,0.15);
        }
        .alert-custom i { color: var(--gold); margin-right: 10px; }

        .card-premium {
            background: var(--white);
            border: 1px solid rgba(201,146,10,0.1);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .card-premium .card-head {
            padding: 14px 22px;
            background: linear-gradient(135deg, var(--cream), var(--ivory));
            border-bottom: 2px solid rgba(201,146,10,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .card-premium .card-head h6 {
            font-weight: 800;
            margin: 0;
            font-size: 0.85rem;
            color: var(--charcoal);
        }
        .card-premium .card-head h6 i { color: var(--gold); margin-right: 10px; }

        .card-premium .card-body {
            padding: 18px 22px;
        }

        .btn-gold {
            background: var(--gold);
            color: white;
            border: none;
            transition: all 0.3s;
        }
        .btn-gold:hover {
            background: var(--gold-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201,146,10,0.3);
        }

        .btn-outline-gold {
            border: 2px solid var(--gold);
            color: var(--gold);
            background: transparent;
            transition: all 0.3s;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: white;
        }

        /* Image Preview */
        .image-preview-container {
            width: 100%;
            max-width: 200px;
            height: 150px;
            border: 2px dashed var(--light-gray);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--mid-gray);
            cursor: pointer;
            transition: all 0.3s;
            overflow: hidden;
            position: relative;
            background: var(--cream);
            margin-bottom: 10px;
        }
        .image-preview-container:hover {
            border-color: var(--gold);
            background: rgba(201,146,10,0.05);
        }
        .image-preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview-container .placeholder {
            text-align: center;
            padding: 10px;
        }
        .image-preview-container .placeholder i {
            font-size: 2rem;
            color: var(--gold);
            display: block;
            margin-bottom: 6px;
        }
        .image-preview-container .placeholder span {
            font-size: 0.75rem;
        }
        #imageInput {
            display: none;
        }

        .content {
            padding: 20px 20px 40px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--charcoal);
        }
        .form-control, .form-select {
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,146,10,0.1);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .form-switch .form-check-input {
            width: 2.5rem;
            height: 1.3rem;
            cursor: pointer;
        }
        .form-switch .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .current-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            border: 2px solid rgba(201,146,10,0.15);
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .admin-header { padding: 14px 18px; }
            .admin-header .title { font-size: 0.9rem; }
            .card-premium .card-body { padding: 14px 16px; }
            .image-preview-container { max-width: 100%; height: 120px; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <div class="content">
        <div class="container-fluid">
            
            <!-- ===== ADMIN HEADER ===== -->
            <div class="admin-header">
                <div class="row align-items-center g-2">
                    <div class="col-md-6">
                        <div class="title">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span> Product
                        </div>
                        <div class="subtitle">Update product details</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="products.php" class="btn btn-outline-gold">
                            <i class="fas fa-arrow-left me-1"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>

            <!-- ===== ALERTS ===== -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-custom mb-3">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-custom mb-3" style="background:#fef2f2; border-left-color:#b91c1c; color:#b91c1c;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- ===== MAIN CARD ===== -->
            <div class="card-premium">
                <div class="card-head">
                    <h6><i class="fas fa-edit"></i> Edit Product: <?php echo htmlspecialchars($product['name']); ?></h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select name="category" class="form-select" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($cat['name'] == $product['category']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sub Category</label>
                                        <input type="text" name="sub_category" class="form-control" placeholder="e.g., CNC, Hydraulic, Panel Processing" value="<?php echo htmlspecialchars($product['sub_category'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" value="<?php echo $product['sort_order']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Key Features</label>
                                        <textarea name="features" class="form-control" rows="3"><?php echo htmlspecialchars($product['features'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Specifications</label>
                                        <textarea name="specifications" class="form-control" rows="3"><?php echo htmlspecialchars($product['specifications'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="col-lg-4">
                                <!-- Image Upload -->
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    
                                    <?php
                                    $edit_img = trim($product['image'] ?? '');
                                    $edit_src = '';
                                    if (!empty($edit_img)) {
                                        if (stripos($edit_img, 'http://') === 0 || stripos($edit_img, 'https://') === 0 || strpos($edit_img, '../') === 0) {
                                            $edit_src = $edit_img;
                                        } elseif (strpos($edit_img, '/') === 0) {
                                            $edit_src = '..' . $edit_img;
                                        } else {
                                            $edit_src = '../' . $edit_img;
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($edit_src)): ?>
                                        <div class="text-center">
                                            <img src="<?php echo htmlspecialchars($edit_src); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="current-image" onerror="this.parentElement.style.display='none';">
                                            <p><small class="text-muted">Current image</small></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="image-preview-container" onclick="document.getElementById('imageInput').click()">
                                        <div class="placeholder" id="imagePlaceholder">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span>Click to upload new image</span>
                                            <small class="d-block text-muted" style="font-size:0.6rem;">Leave empty to keep current</small>
                                        </div>
                                        <img id="previewImg" style="display:none;">
                                    </div>
                                    <input type="file" name="product_image" id="imageInput" accept="image/*">
                                    <small class="text-muted">Or enter image URL below</small>
                                    <input type="text" name="image_url" class="form-control mt-1" placeholder="https://example.com/image.jpg" id="imageUrlInput" value="<?php echo (!empty($product['image']) && !file_exists("../" . $product['image'])) ? htmlspecialchars($product['image']) : ''; ?>">
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="draft" <?php echo $product['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                
                                <!-- Featured -->
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="featured" id="featured" value="1" <?php echo $product['featured'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="featured">
                                        <i class="fas fa-star" style="color:var(--gold);"></i> Featured Product
                                    </label>
                                </div>
                                
                                <!-- Submit -->
                                <div class="mt-4">
                                    <button type="submit" name="update_product" class="btn btn-gold w-100 py-2">
                                        <i class="fas fa-save me-2"></i> Update Product
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== IMAGE PREVIEW =====
        const imageInput = document.getElementById('imageInput');
        const previewImg = document.getElementById('previewImg');
        const imagePlaceholder = document.getElementById('imagePlaceholder');

        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    imagePlaceholder.style.display = 'none';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // ===== URL IMAGE PREVIEW =====
        document.getElementById('imageUrlInput').addEventListener('input', function() {
            if (this.value) {
                previewImg.src = this.value;
                previewImg.style.display = 'block';
                imagePlaceholder.style.display = 'none';
            } else {
                previewImg.style.display = 'none';
                imagePlaceholder.style.display = 'block';
            }
        });

        // ===== AUTO-HIDE ALERTS =====
        setTimeout(() => {
            document.querySelectorAll('.alert-custom').forEach(el => {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    </script>
</body>
</html>