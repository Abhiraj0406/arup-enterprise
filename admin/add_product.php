<?php
// admin/add_product.php — Arup Enterprise Admin Panel
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db.php";
date_default_timezone_set('Asia/Kolkata');

if (!$conn) { die("<p style='padding:40px;font-family:sans-serif;color:red;'>Database connection failed. Check includes/db.php credentials.</p>"); }

$page_title  = "Add Product";
$edit_mode   = false;
$edit_id     = 0;
$form        = [
    'name'           => '',
    'category'       => '',
    'sub_category'   => '',
    'description'    => '',
    'features'       => '',
    'specifications' => '',
    'status'         => 'active',
    'featured'       => 0,
    'sort_order'     => 0,
    'image'          => '',
];

// ── Load categories ──────────────────────────────────────────
$categories = [];
$catR = $conn->query("SELECT * FROM categories WHERE status='active' ORDER BY name ASC");
if ($catR) { while ($r = $catR->fetch_assoc()) $categories[] = $r; }
if (empty($categories)) {
    $categories = [
        ['name'=>'Wood Working'],
        ['name'=>'Sheet Metal'],
        ['name'=>'CNC'],
        ['name'=>'Hydraulic'],
        ['name'=>'Other'],
    ];
}

// ── Edit mode: pre-fill form ─────────────────────────────────
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $r = $conn->query("SELECT * FROM products WHERE id=$edit_id");
    if ($r && $row = $r->fetch_assoc()) {
        $edit_mode = true;
        $form = array_merge($form, $row);
        $page_title = "Edit Product";
    }
}

// ── Save product (Add or Edit) ────────────────────────────────
if (isset($_POST['save_product'])) {
    $name           = trim($_POST['name']);
    $category       = trim($_POST['category']);
    $sub_category   = trim($_POST['sub_category'] ?? '');
    $description    = trim($_POST['description']);
    $features       = trim($_POST['features'] ?? '');
    $specifications = trim($_POST['specifications'] ?? '');
    $status         = $_POST['status'] ?? 'active';
    $featured       = isset($_POST['featured']) ? 1 : 0;
    $sort_order     = intval($_POST['sort_order'] ?? 0);
    $image_path     = trim($_POST['existing_image'] ?? '');

    // Handle file upload
    if (!empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/products/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $fname = 'prod_' . time() . '_' . rand(100,999) . '.' . $ext;
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_dir . $fname)) {
                $image_path = 'assets/uploads/products/' . $fname;
            }
        }
    }
    // If no upload, use URL field
    if (empty($image_path) && !empty($_POST['image_url'])) {
        $image_path = trim($_POST['image_url']);
    }

    if ($edit_mode && $edit_id > 0) {
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, sub_category=?, description=?, features=?, specifications=?, image=?, status=?, featured=?, sort_order=? WHERE id=?");
        $stmt->bind_param("ssssssssiii", $name, $category, $sub_category, $description, $features, $specifications, $image_path, $status, $featured, $sort_order, $edit_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, category, sub_category, description, features, specifications, image, status, featured, sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssii", $name, $category, $sub_category, $description, $features, $specifications, $image_path, $status, $featured, $sort_order);
    }

    if ($stmt && $stmt->execute()) {
        $_SESSION['success'] = $edit_mode
            ? "Product '{$name}' updated successfully!"
            : "Product '{$name}' added successfully!";
        header("Location: products.php");
        exit();
    } else {
        $_SESSION['error'] = "DB Error: " . $conn->error;
    }
}

// ✅ Include admin navbar
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title><?php echo $page_title; ?> | Arup Enterprise Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold:#C9920A; --gold-dark:#8B6508; --gold-light:#F0C040;
            --cream:#FAF6EE; --ivory:#F0E8D0; --charcoal:#1C1C1C;
            --mid:#4A4A4A; --light:#D5CDB8; --white:#fff;
            --shadow-md:0 6px 24px rgba(0,0,0,0.07);
            --shadow-gold:0 4px 20px rgba(201,146,10,0.2);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:var(--cream);color:var(--charcoal);font-size:14px}

        .content { padding: 16px 20px 30px; }

        .pg-header {
            background: var(--white);
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 18px;
            border: 1px solid rgba(201,146,10,0.12);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }
        .pg-header::before {
            content:'';
            position:absolute;
            top:0;left:0;right:0;
            height:3px;
            background:linear-gradient(90deg,var(--gold),var(--gold-light),var(--gold));
        }
        .pg-header-title {
            font-family:'Playfair Display',serif;
            font-size:1.1rem;
            font-weight:900;
            color:var(--charcoal);
        }
        .pg-header-title i { color:var(--gold); margin-right:8px; }
        .pg-header-title span { color:var(--gold); }
        .pg-header-sub { font-size:0.7rem; color:var(--mid); margin-top:2px; }

        .alert-ok {
            background:#f0fdf4;
            border:1px solid rgba(39,174,96,0.3);
            border-left:4px solid var(--gold);
            border-radius:10px;
            padding:10px 16px;
            color:#166534;
            font-size:0.82rem;
            font-weight:500;
            margin-bottom:14px;
        }
        .alert-err {
            background:#fef2f2;
            border:1px solid rgba(231,76,60,0.3);
            border-left:4px solid #e74c3c;
            border-radius:10px;
            padding:10px 16px;
            color:#b91c1c;
            font-size:0.82rem;
            font-weight:500;
            margin-bottom:14px;
        }

        .form-card {
            background:var(--white);
            border:1px solid rgba(201,146,10,0.1);
            border-radius:12px;
            box-shadow:var(--shadow-md);
            overflow:hidden;
            margin-bottom:18px;
        }
        .form-card-head {
            background:linear-gradient(135deg,var(--cream),var(--ivory));
            border-bottom:1px solid rgba(201,146,10,0.1);
            padding:10px 16px;
            display:flex;
            align-items:center;
            justify-content:space-between;
        }
        .form-card-head h5 {
            font-size:0.82rem;
            font-weight:800;
            color:var(--charcoal);
            margin:0;
            display:flex;
            align-items:center;
            gap:6px;
        }
        .form-card-head h5 i { color:var(--gold); }
        .form-card-body { padding:16px 18px; }

        .form-label {
            font-weight:700;
            font-size:0.78rem;
            color:var(--charcoal);
            margin-bottom:4px;
            display:block;
        }
        .form-label .req { color:#e74c3c; margin-left:2px; }
        .form-control,.form-select {
            border:2px solid var(--light);
            border-radius:8px;
            padding:8px 12px;
            font-size:0.85rem;
            font-family:'Inter',sans-serif;
            transition:all .2s;
            background:var(--cream);
            width:100%;
            color:var(--charcoal);
        }
        .form-control:focus,.form-select:focus {
            border-color:var(--gold);
            box-shadow:0 0 0 3px rgba(201,146,10,0.12);
            background:var(--white);
            outline:none;
        }
        textarea.form-control { resize:vertical; min-height:80px; }
        .form-hint { font-size:0.65rem; color:#888; margin-top:3px; }

        .img-upload-box {
            width:100%;
            height:150px;
            border:2px dashed var(--light);
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            cursor:pointer;
            transition:all .25s;
            background:var(--cream);
            position:relative;
            overflow:hidden;
            margin-bottom:8px;
        }
        .img-upload-box:hover { border-color:var(--gold); background:rgba(201,146,10,0.04); }
        .img-upload-box img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
        .img-upload-box .upload-ph {
            text-align:center;
            padding:12px;
            z-index:1;
        }
        .img-upload-box .upload-ph i { font-size:2rem; color:var(--gold); display:block; margin-bottom:4px; }
        .img-upload-box .upload-ph span { font-size:0.7rem; color:var(--mid); font-weight:500; }
        .img-upload-box .upload-ph small { font-size:0.6rem; color:#aaa; display:block; margin-top:2px; }
        #hiddenFileInput { display:none; }

        .status-card {
            background:var(--ivory);
            border:1px solid rgba(201,146,10,0.12);
            border-radius:10px;
            padding:14px;
            margin-bottom:10px;
        }
        .status-card label { font-weight:700; font-size:0.78rem; color:var(--charcoal); display:block; margin-bottom:4px; }

        .featured-toggle-wrap {
            display:flex;
            align-items:center;
            gap:8px;
            background:var(--ivory);
            border:1px solid rgba(201,146,10,0.12);
            border-radius:10px;
            padding:10px 14px;
            margin-bottom:10px;
            cursor:pointer;
        }
        .featured-toggle-wrap label {
            font-size:0.82rem;
            font-weight:700;
            color:var(--charcoal);
            cursor:pointer;
            margin:0;
        }
        .featured-toggle-wrap label i { color:var(--gold); margin-right:4px; }
        .form-check-input[type=checkbox] {
            width:2rem; height:1.1rem;
            cursor:pointer;
            accent-color:var(--gold);
        }

        .btn-save {
            width:100%;
            padding:11px;
            background:linear-gradient(135deg,var(--gold),var(--gold-dark));
            color:white;
            border:none;
            border-radius:8px;
            font-weight:800;
            font-size:0.88rem;
            cursor:pointer;
            transition:all .25s;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            font-family:'Inter',sans-serif;
            box-shadow:var(--shadow-gold);
            margin-bottom:8px;
        }
        .btn-save:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(201,146,10,0.4); }
        .btn-back {
            width:100%;
            padding:9px;
            border:2px solid var(--gold);
            color:var(--gold);
            background:transparent;
            border-radius:8px;
            font-weight:700;
            font-size:0.82rem;
            cursor:pointer;
            transition:all .25s;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            text-decoration:none;
            font-family:'Inter',sans-serif;
        }
        .btn-back:hover { background:var(--gold); color:white; }

        .default-notice {
            background:rgba(201,146,10,0.06);
            border:1px dashed rgba(201,146,10,0.3);
            border-radius:8px;
            padding:8px 12px;
            font-size:0.72rem;
            color:var(--mid);
            margin-bottom:10px;
            display:flex;
            align-items:flex-start;
            gap:6px;
        }
        .default-notice i { color:var(--gold); margin-top:1px; flex-shrink:0; }

        @media(max-width:768px){
            .form-card-body{padding:12px 14px}
            .img-upload-box{height:120px}
            .pg-header-title{font-size:0.95rem}
        }
    </style>
</head>
<body>

<!-- =============================================
     PAGE CONTENT
============================================= -->
<div class="content">
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="pg-header">
            <div class="row align-items-center g-2">
                <div class="col-md-7">
                    <div class="pg-header-title">
                        <i class="fas fa-<?php echo $edit_mode?'edit':'plus-circle'; ?>"></i>
                        <span><?php echo $edit_mode?'Edit':'Add'; ?></span> Product
                    </div>
                    <div class="pg-header-sub">
                        <?php echo $edit_mode
                            ? 'Update product details — changes will reflect live on the website instantly.'
                            : 'Add a new machine to your product catalogue. It will appear on the website after saving.'; ?>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-2 mt-md-0">
                    <a href="products.php" class="btn btn-outline-secondary btn-sm" style="border-radius:8px;font-weight:600;font-size:0.75rem;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>

        <!-- ALERTS -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-ok"><i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-err"><i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- MAIN FORM -->
        <form method="POST" enctype="multipart/form-data" id="productForm">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="edit_mode" value="1">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($form['image']); ?>">
            <?php endif; ?>

            <div class="row g-4">

                <!-- ════════════════════════════════════════
                     LEFT COLUMN: Main Product Info
                ════════════════════════════════════════ -->
                <div class="col-lg-8">

                    <!-- Basic Details -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <h5><i class="fas fa-info-circle"></i> Product Information</h5>
                        </div>
                        <div class="form-card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Product Name <span class="req">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                           placeholder="e.g. Hi-90 R High Speed Router"
                                           value="<?php echo htmlspecialchars($form['name']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" min="0"
                                           value="<?php echo intval($form['sort_order']); ?>">
                                    <div class="form-hint">Lower number = shown first</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category <span class="req">*</span></label>
                                    <select name="category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach($categories as $cat): $cn = is_array($cat)?$cat['name']:$cat; ?>
                                            <option value="<?php echo htmlspecialchars($cn); ?>"
                                                <?php echo $form['category']===$cn?'selected':''; ?>>
                                                <?php echo htmlspecialchars($cn); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sub-Category</label>
                                    <input type="text" name="sub_category" class="form-control"
                                           placeholder="e.g. CNC, Hydraulic, Panel Processing"
                                           value="<?php echo htmlspecialchars($form['sub_category']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <h5><i class="fas fa-align-left"></i> Description</h5>
                        </div>
                        <div class="form-card-body">
                            <div class="default-notice">
                                <i class="fas fa-info-circle"></i>
                                <span>If description is left blank, a default industry description will be shown. Fill it for best results.</span>
                            </div>
                            <label class="form-label">Full Description <span class="req">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required
                                      placeholder="Describe this machine — its purpose, industry use, key benefits..."><?php echo htmlspecialchars($form['description']); ?></textarea>
                        </div>
                    </div>

                    <!-- Features + Specs -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <h5><i class="fas fa-list-check"></i> Features &amp; Specifications</h5>
                            <small style="color:var(--mid);font-size:0.65rem;">One per line</small>
                        </div>
                        <div class="form-card-body">
                            <div class="default-notice">
                                <i class="fas fa-lightbulb"></i>
                                <span>Optional — if left blank, default quality badges will display.</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Key Features</label>
                                    <textarea name="features" class="form-control" rows="4"
                                              placeholder="High-speed spindle motor&#10;Auto tool change&#10;Low vibration design"><?php echo htmlspecialchars($form['features']); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Technical Specifications</label>
                                    <textarea name="specifications" class="form-control" rows="4"
                                              placeholder="Motor: 3.5 kW / 5 HP&#10;Spindle Speed: 3200 RPM"><?php echo htmlspecialchars($form['specifications']); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /col-lg-8 -->

                <!-- ════════════════════════════════════════
                     RIGHT COLUMN: Image + Status
                ════════════════════════════════════════ -->
                <div class="col-lg-4">

                    <!-- Image Upload -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <h5><i class="fas fa-image"></i> Product Image</h5>
                        </div>
                        <div class="form-card-body">
                            <div class="img-upload-box" id="uploadBox" onclick="document.getElementById('hiddenFileInput').click()">
                                <?php if (!empty($form['image']) && file_exists("../" . $form['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($form['image']); ?>" id="previewImg" alt="Current">
                                <?php else: ?>
                                    <img id="previewImg" style="display:none;">
                                <?php endif; ?>
                                <div class="upload-ph" id="uploadPh" <?php echo (!empty($form['image']) && file_exists("../" . $form['image']))?'style="display:none"':''; ?>>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Click to Upload Image</span>
                                    <small>JPG, PNG, WebP</small>
                                </div>
                            </div>
                            <input type="file" name="product_image" id="hiddenFileInput" accept="image/*">

                            <label class="form-label" style="font-size:0.75rem;margin-top:6px;">Or Paste Image URL</label>
                            <input type="text" name="image_url" id="imageUrlInput" class="form-control"
                                   placeholder="https://example.com/machine.jpg"
                                   value="<?php echo (isset($form['image']) && strpos($form['image'],'http')===0) ? htmlspecialchars($form['image']) : ''; ?>">
                            <div class="form-hint">Uploaded file takes priority over URL.</div>

                            <?php if (!empty($form['image']) && file_exists("../" . $form['image'])): ?>
                                <div class="form-hint mt-2" style="color:var(--gold-dark);">
                                    <i class="fas fa-check-circle" style="color:var(--gold);"></i>
                                    Current: <?php echo htmlspecialchars(basename($form['image'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-card">
                        <div class="form-card-head">
                            <h5><i class="fas fa-toggle-on"></i> Visibility</h5>
                        </div>
                        <div class="form-card-body">
                            <label class="form-label">Product Status</label>
                            <select name="status" class="form-select mb-3">
                                <option value="active"   <?php echo $form['status']==='active'  ?'selected':''; ?>>Active — Visible on Website</option>
                                <option value="draft"    <?php echo $form['status']==='draft'   ?'selected':''; ?>>Draft — Hidden</option>
                                <option value="inactive" <?php echo $form['status']==='inactive'?'selected':''; ?>> Inactive — Hidden</option>
                            </select>

                            <div class="featured-toggle-wrap">
                                <input class="form-check-input" type="checkbox" name="featured" id="featuredToggle"
                                       value="1" <?php echo $form['featured']?'checked':''; ?>>
                                <label for="featuredToggle">
                                    <i class="fas fa-star"></i> Featured Product
                                </label>
                            </div>
                            <div class="form-hint" style="margin-top:0">Featured products appear with a gold badge.</div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <button type="submit" name="save_product" class="btn-save">
                        <i class="fas fa-save"></i>
                        <?php echo $edit_mode ? 'Update Product' : 'Save &amp; Publish'; ?>
                    </button>
                    <a href="products.php" class="btn-back">
                        <i class="fas fa-times"></i> Cancel
                    </a>

                </div><!-- /col-lg-4 -->

            </div><!-- /row -->
        </form>

    </div><!-- /container-fluid -->
</div><!-- /content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Image file preview ───────────────────────────────────────
document.getElementById('hiddenFileInput').addEventListener('change', function(){
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('previewImg');
            img.src = e.target.result;
            img.style.display = 'block';
            document.getElementById('uploadPh').style.display = 'none';
        };
        reader.readAsDataURL(this.files[0]);
    }
});

// ── Image URL preview ────────────────────────────────────────
document.getElementById('imageUrlInput').addEventListener('input', function(){
    const img = document.getElementById('previewImg');
    if (this.value.trim()) {
        img.src = this.value.trim();
        img.style.display = 'block';
        document.getElementById('uploadPh').style.display = 'none';
    } else {
        // If no file uploaded and no URL, show placeholder
        if (!document.getElementById('hiddenFileInput').files.length) {
            img.style.display = 'none';
            document.getElementById('uploadPh').style.display = 'flex';
        }
    }
});

// ── Auto-hide alerts after 5s ────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.alert-ok, .alert-err').forEach(el => {
        el.style.transition = 'opacity .5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 5000);
</script>

</body>
</html>