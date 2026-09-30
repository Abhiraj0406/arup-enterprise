<?php
// admin/associates.php - Manage Associates/Partners (Only Logos)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db.php";

date_default_timezone_set('Asia/Kolkata');

$page_title = "Associates";
$page_icon = "handshake";

// ================= FETCH ASSOCIATES =================
$associates = [];
$result = $conn->query("SELECT * FROM associates ORDER BY sort_order ASC, id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $associates[] = $row;
    }
}

// ================= ADD ASSOCIATE =================
if (isset($_POST['add_associate'])) {
    $logo = trim($_POST['logo']);
    $sort_order = intval($_POST['sort_order']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO associates (logo, sort_order, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $logo, $sort_order, $status);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Associate logo added successfully!";
        header("Location: associates.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// ================= UPDATE ASSOCIATE =================
if (isset($_POST['edit_associate'])) {
    $id = intval($_POST['edit_id']);
    $logo = trim($_POST['edit_logo']);
    $sort_order = intval($_POST['edit_sort_order']);
    $status = $_POST['edit_status'];
    
    $stmt = $conn->prepare("UPDATE associates SET logo = ?, sort_order = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sisi", $logo, $sort_order, $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Associate logo updated successfully!";
        header("Location: associates.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// ================= TOGGLE STATUS =================
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $stmt = $conn->prepare("UPDATE associates SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Associate status updated!";
        header("Location: associates.php");
        exit();
    }
}

// ================= DELETE ASSOCIATE =================
if (isset($_GET['delete_id']) || isset($_POST['delete_id'])) {
    $id = intval($_GET['delete_id'] ?? $_POST['delete_id']);
    
    if ($id > 0) {
        // Get logo path to delete file
        $stmt = $conn->prepare("SELECT logo FROM associates WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $logo_path = trim($row['logo'] ?? '');
            if (!empty($logo_path) && stripos($logo_path, 'http') !== 0) {
                $clean_path = ltrim(str_replace('../', '', $logo_path), '/');
                if (file_exists("../" . $clean_path)) {
                    @unlink("../" . $clean_path);
                }
            }
        }
        
        $stmt = $conn->prepare("DELETE FROM associates WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Partner logo deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
    }
    header("Location: associates.php");
    exit();
}

// ================= UPLOAD LOGO (100KB LIMIT) =================
if (isset($_POST['upload_logo'])) {
    $target_dir = "../assets/images/associates/";
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES["logo_file"]["name"]);
    $target_file = $target_dir . $file_name;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_size = $_FILES["logo_file"]["size"];
    
    // Check if image file is valid
    $check = getimagesize($_FILES["logo_file"]["tmp_name"]);
    if ($check !== false) {
        // Check file size (100KB = 102400 bytes)
        if ($file_size > 102400) {
            echo json_encode(['success' => false, 'error' => 'File size must be less than 100KB. Current size: ' . round($file_size/1024, 2) . 'KB']);
            exit();
        }
        
        // Allow certain file formats
        if (in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            if (move_uploaded_file($_FILES["logo_file"]["tmp_name"], $target_file)) {
                echo json_encode(['success' => true, 'path' => 'assets/images/associates/' . $file_name]);
                exit();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error uploading file']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Only JPG, JPEG, PNG, GIF, WEBP & SVG files are allowed']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'File is not a valid image']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Associates | Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #C9920A;
            --gold-dark: #a87a08;
            --cream: #fef7ed;
            --ivory: #faf3e8;
            --charcoal: #1e1e1e;
            --mid-gray: #4a3f37;
            --light-gray: #e8ddd0;
            --white: #ffffff;
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
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 18px;
            border: 1px solid rgba(201,146,10,0.12);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), #d4a373, var(--gold));
        }

        .admin-header .title {
            font-size: 1.1rem;
            font-weight: 900;
            color: var(--charcoal);
            margin: 0;
        }
        .admin-header .title i {
            color: var(--gold);
            margin-right: 8px;
        }
        .admin-header .title span { color: var(--gold); }
        .admin-header .subtitle {
            font-size: 0.7rem;
            color: var(--mid-gray);
            margin: 0;
        }

        .btn-gold {
            background: var(--gold);
            color: white;
            border: none;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
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
            padding: 4px 10px;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: white;
        }

        .alert-custom {
            background: #f0fdf4;
            border-left: 4px solid var(--gold);
            border-radius: 10px;
            padding: 10px 16px;
            color: #166534;
            font-weight: 500;
            font-size: 0.8rem;
            border: 1px solid rgba(201,146,10,0.15);
        }
        .alert-custom i { color: var(--gold); margin-right: 8px; }

        .card-premium {
            background: var(--white);
            border: 1px solid rgba(201,146,10,0.1);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-premium .card-head {
            padding: 10px 16px;
            background: linear-gradient(135deg, var(--cream), var(--ivory));
            border-bottom: 1px solid rgba(201,146,10,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .card-premium .card-head h6 {
            font-weight: 700;
            margin: 0;
            font-size: 0.8rem;
            color: var(--charcoal);
        }
        .card-premium .card-head h6 i { color: var(--gold); margin-right: 6px; }
        .card-premium .card-head .badge-count {
            background: var(--gold);
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 700;
        }

        .card-premium .card-body {
            padding: 12px 16px;
        }

        .table {
            color: var(--charcoal);
            margin: 0;
            font-size: 0.78rem;
        }
        .table thead th {
            padding: 8px 12px;
            font-weight: 700;
            color: var(--gold-dark);
            text-transform: uppercase;
            font-size: 0.55rem;
            letter-spacing: 0.6px;
            border-bottom: 2px solid rgba(201,146,10,0.12);
            background: var(--cream);
        }
        .table tbody td {
            padding: 8px 12px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(201,146,10,0.05);
        }
        .table tbody tr:hover { background: rgba(201,146,10,0.03); }

        .badge-status {
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
        }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fef2f2; color: #991b1b; }

        .modal-content-custom {
            border: 2px solid var(--gold);
            border-radius: 14px;
        }
        .modal-content-custom .modal-header {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 12px 18px;
        }
        .modal-content-custom .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .modal-content-custom .modal-body {
            padding: 18px 20px;
        }
        .modal-content-custom .modal-footer {
            padding: 12px 18px;
            border-top: 1px solid rgba(201,146,10,0.08);
        }

        .form-label {
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .form-control, .form-select {
            font-size: 0.85rem;
            padding: 6px 12px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,146,10,0.08);
        }

        .logo-preview {
            width: 80px;
            height: 60px;
            border: 2px dashed var(--light-gray);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: var(--cream);
            padding: 4px;
        }
        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .logo-preview .placeholder {
            color: var(--mid-gray);
            font-size: 0.6rem;
            text-align: center;
        }

        .content {
            padding: 16px 18px 30px;
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .upload-btn-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-size-warning {
            font-size: 0.7rem;
            color: #e74c3c;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .admin-header { padding: 10px 14px; }
            .admin-header .title { font-size: 0.9rem; }
            .card-premium .card-body { padding: 10px 12px; }
            .table thead th, .table tbody td { padding: 6px 8px; font-size: 0.65rem; }
            .content { padding: 12px 14px 20px; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <div class="content">
        <div class="container-fluid">
            
            <div class="admin-header">
                <div class="row align-items-center g-2">
                    <div class="col-md-6">
                        <div class="title">
                            <i class="fas fa-handshake"></i>
                            <span>Associates / Partners</span>
                        </div>
                        <div class="subtitle">Manage partner logos (Max 100KB per image)</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addAssociateModal">
                            <i class="fas fa-plus me-1"></i> Add Logo
                        </button>
                    </div>
                </div>
            </div>

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

            <div class="card-premium">
                <div class="card-head">
                    <h6><i class="fas fa-list-ul"></i> All Partner Logos</h6>
                    <span class="badge-count"><?php echo count($associates); ?> Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th style="width:120px;">Logo</th>
                                    <th style="width:60px;">Sort</th>
                                    <th style="width:80px;">Status</th>
                                    <th style="width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($associates)): ?>
                                    <?php foreach ($associates as $assoc): ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?php echo $assoc['id']; ?></td>
                                            <td>
                                                <div class="logo-preview">
                                                    <?php
                                                    $raw_logo = trim($assoc['logo'] ?? '');
                                                    $logo_src = '';
                                                    if (!empty($raw_logo)) {
                                                        if (stripos($raw_logo, 'http://') === 0 || stripos($raw_logo, 'https://') === 0 || strpos($raw_logo, '../') === 0) {
                                                            $logo_src = $raw_logo;
                                                        } elseif (strpos($raw_logo, '/') === 0) {
                                                            $logo_src = '..' . $raw_logo;
                                                        } else {
                                                            $logo_src = '../' . $raw_logo;
                                                        }
                                                    }
                                                    ?>
                                                    <?php if (!empty($logo_src)): ?>
                                                        <img src="<?php echo htmlspecialchars($logo_src); ?>" alt="Partner Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                        <span class="placeholder" style="display:none;"><i class="fas fa-image"></i><br>No Logo</span>
                                                    <?php else: ?>
                                                        <span class="placeholder"><i class="fas fa-image"></i><br>No Logo</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?php echo $assoc['sort_order']; ?></td>
                                            <td>
                                                <a href="?toggle_status=<?php echo $assoc['id']; ?>" 
                                                   class="badge-status <?php echo $assoc['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>"
                                                   onclick="return confirm('Toggle status?')">
                                                    <?php echo ucfirst($assoc['status']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-outline-gold" 
                                                            onclick="editAssociate(<?php echo $assoc['id']; ?>, '<?php echo htmlspecialchars($assoc['logo'] ?? ''); ?>', <?php echo $assoc['sort_order']; ?>, '<?php echo $assoc['status']; ?>')"
                                                            title="Edit Logo">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="associates.php?delete_id=<?php echo (int)$assoc['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirmDelete(event, <?php echo (int)$assoc['id']; ?>)"
                                                       title="Delete Logo">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-handshake" style="font-size:2rem;display:block;color:var(--light-gray);margin-bottom:8px;"></i>
                                            <div style="font-weight:600;color:var(--mid-gray);font-size:0.9rem;">No partner logos found</div>
                                            <div style="font-size:0.75rem;color:var(--mid-gray);">Click "Add Logo" to upload your first partner logo</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ===== ADD ASSOCIATE MODAL ===== -->
    <div class="modal fade" id="addAssociateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <form method="POST" id="addAssociateForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add Partner Logo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Logo <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" name="logo" id="addLogoInput" class="form-control" placeholder="assets/images/associates/logo.png" required>
                                </div>
                                <div class="col-4">
                                    <div id="addLogoPreview" class="logo-preview">
                                        <span class="placeholder"><i class="fas fa-image"></i><br>Preview</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="upload-btn-wrapper">
                                    <button type="button" class="btn btn-sm btn-outline-gold">
                                        <i class="fas fa-upload"></i> Upload Logo
                                    </button>
                                    <input type="file" id="addLogoUpload" accept="image/*" onchange="uploadLogo('add')">
                                </div>
                                <small class="text-muted d-block mt-1">Upload JPG, PNG, GIF, WEBP, SVG (Max 100KB)</small>
                                <small class="file-size-warning"><i class="fas fa-info-circle"></i> File size must be less than 100KB</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size:0.8rem;padding:6px 16px;">Cancel</button>
                        <button type="submit" name="add_associate" class="btn btn-gold" style="font-size:0.8rem;padding:6px 16px;">
                            <i class="fas fa-save me-1"></i> Add Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== EDIT ASSOCIATE MODAL ===== -->
    <div class="modal fade" id="editAssociateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <form method="POST" id="editAssociateForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Partner Logo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="editId">
                        
                        <div class="mb-2">
                            <label class="form-label">Logo <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" name="edit_logo" id="editLogoInput" class="form-control" placeholder="assets/images/associates/logo.png" required>
                                </div>
                                <div class="col-4">
                                    <div id="editLogoPreview" class="logo-preview">
                                        <span class="placeholder"><i class="fas fa-image"></i><br>Preview</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="upload-btn-wrapper">
                                    <button type="button" class="btn btn-sm btn-outline-gold">
                                        <i class="fas fa-upload"></i> Upload Logo
                                    </button>
                                    <input type="file" id="editLogoUpload" accept="image/*" onchange="uploadLogo('edit')">
                                </div>
                                <small class="text-muted d-block mt-1">Upload JPG, PNG, GIF, WEBP, SVG (Max 100KB)</small>
                                <small class="file-size-warning"><i class="fas fa-info-circle"></i> File size must be less than 100KB</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="edit_sort_order" id="editSortOrder" class="form-control" min="0">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Status</label>
                                <select name="edit_status" id="editStatus" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size:0.8rem;padding:6px 16px;">Cancel</button>
                        <button type="submit" name="edit_associate" class="btn btn-gold" style="font-size:0.8rem;padding:6px 16px;">
                            <i class="fas fa-save me-1"></i> Update Logo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== DELETE CONFIRMATION MODAL ===== -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-custom text-center p-3">
                <div class="modal-body">
                    <div style="font-size:2.4rem; color:#dc3545; margin-bottom:12px;">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h5 style="font-weight:700; color:var(--charcoal); margin-bottom:8px;">Delete Partner Logo?</h5>
                    <p style="font-size:0.85rem; color:var(--mid-gray); margin-bottom:20px;">
                        Are you sure you want to permanently delete this logo? This action cannot be undone.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <a href="#" id="confirmDeleteBtn" class="btn btn-sm btn-danger px-3">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== CONFIRM DELETE FUNCTION =====
        function confirmDelete(e, id) {
            e.preventDefault();
            document.getElementById('confirmDeleteBtn').href = 'associates.php?delete_id=' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
            return false;
        }
        // ===== UPLOAD LOGO FUNCTION =====
        function uploadLogo(type) {
            var input = document.getElementById(type + 'LogoUpload');
            var file = input.files[0];
            
            if (!file) {
                alert('Please select a file first!');
                return;
            }
            
            // Check file size (100KB = 102400 bytes)
            if (file.size > 102400) {
                alert('File size must be less than 100KB. Current size: ' + (file.size / 1024).toFixed(2) + 'KB');
                input.value = '';
                return;
            }
            
            var formData = new FormData();
            formData.append('logo_file', file);
            formData.append('upload_logo', 1);
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'associates.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            if (type === 'add') {
                                document.getElementById('addLogoInput').value = response.path;
                                document.getElementById('addLogoPreview').innerHTML = '<img src="../' + response.path + '" alt="Logo">';
                            } else {
                                document.getElementById('editLogoInput').value = response.path;
                                document.getElementById('editLogoPreview').innerHTML = '<img src="../' + response.path + '" alt="Logo">';
                            }
                            alert('Logo uploaded successfully!');
                        } else {
                            alert('Error: ' + response.error);
                        }
                    } catch (e) {
                        alert('Error uploading file. Please try again.');
                    }
                } else {
                    alert('Server error. Please try again.');
                }
            };
            xhr.send(formData);
        }

        // ===== LIVE PREVIEW FOR ADD =====
        document.getElementById('addLogoInput').addEventListener('input', function() {
            var val = this.value;
            if (val) {
                document.getElementById('addLogoPreview').innerHTML = '<img src="../' + val + '" alt="Logo" onerror="this.parentElement.innerHTML=\'<span class=\\\'placeholder\\\'><i class=\\\'fas fa-image\\\'></i><br>Invalid</span>\'">';
            } else {
                document.getElementById('addLogoPreview').innerHTML = '<span class="placeholder"><i class="fas fa-image"></i><br>Preview</span>';
            }
        });

        // ===== LIVE PREVIEW FOR EDIT =====
        document.getElementById('editLogoInput').addEventListener('input', function() {
            var val = this.value;
            if (val) {
                document.getElementById('editLogoPreview').innerHTML = '<img src="../' + val + '" alt="Logo" onerror="this.parentElement.innerHTML=\'<span class=\\\'placeholder\\\'><i class=\\\'fas fa-image\\\'></i><br>Invalid</span>\'">';
            } else {
                document.getElementById('editLogoPreview').innerHTML = '<span class="placeholder"><i class="fas fa-image"></i><br>Preview</span>';
            }
        });

        // ===== EDIT ASSOCIATE =====
        function editAssociate(id, logo, sort_order, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editLogoInput').value = logo || '';
            document.getElementById('editSortOrder').value = sort_order || 0;
            document.getElementById('editStatus').value = status || 'active';
            
            if (logo) {
                document.getElementById('editLogoPreview').innerHTML = '<img src="../' + logo + '" alt="Logo">';
            } else {
                document.getElementById('editLogoPreview').innerHTML = '<span class="placeholder"><i class="fas fa-image"></i><br>Preview</span>';
            }
            
            new bootstrap.Modal(document.getElementById('editAssociateModal')).show();
        }

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