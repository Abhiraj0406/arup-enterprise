<?php
// admin/categories.php - Category Management with Pin to Menu
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

$page_title = "Categories";
$page_icon = "tags";

// ================= ADD SHOW_IN_MENU COLUMN IF NOT EXISTS =================
$checkColumn = $conn->query("SHOW COLUMNS FROM categories LIKE 'show_in_menu'");
if ($checkColumn->num_rows == 0) {
    $conn->query("ALTER TABLE categories ADD COLUMN show_in_menu TINYINT(1) DEFAULT 1");
}

// ================= ADD PIN_TO_MENU COLUMN IF NOT EXISTS =================
$checkPinColumn = $conn->query("SHOW COLUMNS FROM categories LIKE 'pin_to_menu'");
if ($checkPinColumn->num_rows == 0) {
    $conn->query("ALTER TABLE categories ADD COLUMN pin_to_menu TINYINT(1) DEFAULT 0");
}

// ================= FETCH CATEGORIES =================
$categories = [];
$result = $conn->query("
    SELECT c.*, 
           (SELECT COUNT(*) FROM products WHERE category = c.name AND status != 'inactive') as product_count
    FROM categories c 
    ORDER BY c.sort_order ASC, c.name ASC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// ================= ADD CATEGORY =================
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $name)));
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $sort_order = intval($_POST['sort_order']);
    $show_in_menu = isset($_POST['show_in_menu']) ? 1 : 0;
    $pin_to_menu = isset($_POST['pin_to_menu']) ? 1 : 0;
    
    $check = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
    $check->bind_param("s", $slug);
    $check->execute();
    $checkResult = $check->get_result();
    
    if ($checkResult->num_rows > 0) {
        $_SESSION['error'] = "Category '{$name}' already exists!";
    } else {
        // FIXED: 7 placeholders, 7 variables: 4 strings + 3 integers = "ssssiii"
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, description, icon, sort_order, status, show_in_menu, pin_to_menu) VALUES (?, ?, ?, ?, ?, 'active', ?, ?)");
        $stmt->bind_param("ssssiii", $name, $slug, $description, $icon, $sort_order, $show_in_menu, $pin_to_menu);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Category '{$name}' added successfully!";
            header("Location: categories.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
    }
}

// ================= UPDATE CATEGORY =================
if (isset($_POST['edit_category'])) {
    $id = intval($_POST['edit_id']);
    $name = trim($_POST['edit_name']);
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $name)));
    $description = trim($_POST['edit_description']);
    $icon = trim($_POST['edit_icon']);
    $sort_order = intval($_POST['edit_sort_order']);
    $show_in_menu = isset($_POST['edit_show_in_menu']) ? 1 : 0;
    $pin_to_menu = isset($_POST['edit_pin_to_menu']) ? 1 : 0;
    
    // FIXED: 8 placeholders, 8 variables: 4 strings + 4 integers = "ssssiiii"
    $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, sort_order = ?, show_in_menu = ?, pin_to_menu = ? WHERE id = ?");
    $stmt->bind_param("ssssiiii", $name, $slug, $description, $icon, $sort_order, $show_in_menu, $pin_to_menu, $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Category updated successfully!";
        header("Location: categories.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// ================= TOGGLE STATUS =================
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $stmt = $conn->prepare("UPDATE categories SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Category status updated!";
        header("Location: categories.php");
        exit();
    }
}

// ================= TOGGLE MENU VISIBILITY =================
if (isset($_GET['toggle_menu'])) {
    $id = intval($_GET['toggle_menu']);
    $stmt = $conn->prepare("UPDATE categories SET show_in_menu = IF(show_in_menu=1, 0, 1) WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Category menu visibility updated!";
        header("Location: categories.php");
        exit();
    }
}

// ================= TOGGLE PIN TO MENU =================
if (isset($_GET['toggle_pin'])) {
    $id = intval($_GET['toggle_pin']);
    $stmt = $conn->prepare("UPDATE categories SET pin_to_menu = IF(pin_to_menu=1, 0, 1) WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Category pin status updated!";
        header("Location: categories.php");
        exit();
    }
}

// ================= DELETE CATEGORY =================
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Category deleted successfully!";
        header("Location: categories.php");
        exit();
    }
}

// ================= ICONS LIST =================
$icons = [
    'fa-tag', 'fa-tree', 'fa-industry', 'fa-cogs', 'fa-microchip', 
    'fa-wrench', 'fa-tools', 'fa-cog', 'fa-gear', 'fa-saw',
    'fa-hammer', 'fa-bolt', 'fa-fire', 'fa-shield-alt', 'fa-star',
    'fa-award', 'fa-gem', 'fa-crown', 'fa-layer-group', 'fa-boxes',
    'fa-cubes', 'fa-chart-line', 'fa-robot', 'fa-flask'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | DipBan Technical Services Admin</title>
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
            background: linear-gradient(90deg, var(--gold), var(--mustard), var(--gold-light));
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
            font-size: 1rem;
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
            letter-spacing: 0.5px;
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
            font-size: 0.78rem;
        }
        .table tbody tr:hover { background: rgba(201,146,10,0.03); }
        .table tbody tr:last-child td { border-bottom: none; }

        .badge-status {
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-decoration: none;
            display: inline-block;
        }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fef2f2; color: #991b1b; }
        .badge-show { background: #dbeafe; color: #1d4ed8; }
        .badge-hidden { background: #fef3c7; color: #b45309; }
        .badge-pinned { background: #fef3c7; color: #b45309; }
        .badge-unpinned { background: #e0e7ff; color: #4338ca; }

        .icon-picker-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 6px;
            padding: 8px;
            background: var(--cream);
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            max-height: 100px;
            overflow-y: auto;
        }
        .icon-picker-grid .icon-option {
            width: 32px;
            height: 32px;
            border: 2px solid var(--light-gray);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--mid-gray);
            background: white;
            flex-shrink: 0;
        }
        .icon-picker-grid .icon-option:hover {
            border-color: var(--gold);
            background: rgba(201,146,10,0.08);
            color: var(--gold);
            transform: scale(1.05);
        }
        .icon-picker-grid .icon-option.selected {
            border-color: var(--gold);
            background: var(--gold);
            color: white;
            box-shadow: 0 0 0 3px rgba(201,146,10,0.15);
        }

        .icon-preview-box {
            width: 40px;
            height: 40px;
            background: var(--cream);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--gold);
            border: 2px solid var(--light-gray);
        }

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

        .form-switch .form-check-input {
            width: 2rem;
            height: 1.1rem;
            cursor: pointer;
        }
        .form-switch .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        .content {
            padding: 16px 18px 30px;
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

        .pin-badge {
            background: var(--gold);
            color: white;
            font-size: 0.5rem;
            padding: 1px 6px;
            border-radius: 10px;
            margin-left: 4px;
            font-weight: 700;
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
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </div>
                        <div class="subtitle">Manage categories and pin them to main menu</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-1"></i> Add Category
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
                    <h6><i class="fas fa-list-ul"></i> All Categories</h6>
                    <span class="badge-count"><?php echo count($categories); ?> Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:35px;">#</th>
                                    <th style="width:40px;">Icon</th>
                                    <th>Category Name</th>
                                    <th style="width:80px;">Slug</th>
                                    <th style="width:60px;">Products</th>
                                    <th style="width:60px;">Sort</th>
                                    <th style="width:80px;">Status</th>
                                    <th style="width:100px;">Show in Menu</th>
                                    <th style="width:100px;">Pin to Main Menu</th>
                                    <th style="width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?php echo $cat['id']; ?></td>
                                            <td>
                                                <span style="font-size:1.1rem;color:var(--gold);">
                                                    <i class="fas <?php echo htmlspecialchars($cat['icon'] ?? 'fa-tag'); ?>"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                                                <?php if (!empty($cat['description'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($cat['description'], 0, 35)); ?></small>
                                                <?php endif; ?>
                                                <?php if (($cat['pin_to_menu'] ?? 0) == 1): ?>
                                                    <span class="pin-badge"><i class="fas fa-thumbtack"></i> PINNED</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><code style="font-size:0.65rem;background:var(--cream);padding:1px 6px;border-radius:4px;"><?php echo $cat['slug']; ?></code></td>
                                            <td>
                                                <span class="badge" style="background:rgba(201,146,10,0.12);color:var(--gold-dark);font-weight:600;font-size:0.7rem;">
                                                    <?php echo $cat['product_count'] ?? 0; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $cat['sort_order']; ?></td>
                                            <td>
                                                <a href="?toggle_status=<?php echo $cat['id']; ?>" 
                                                   class="badge-status <?php echo $cat['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>"
                                                   onclick="return confirm('Toggle status for <?php echo htmlspecialchars($cat['name']); ?>?')">
                                                    <?php echo ucfirst($cat['status']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="?toggle_menu=<?php echo $cat['id']; ?>" 
                                                   class="badge-status <?php echo ($cat['show_in_menu'] ?? 1) ? 'badge-show' : 'badge-hidden'; ?>"
                                                   onclick="return confirm('Toggle menu visibility for <?php echo htmlspecialchars($cat['name']); ?>?')">
                                                    <i class="fas <?php echo ($cat['show_in_menu'] ?? 1) ? 'fa-eye' : 'fa-eye-slash'; ?> me-1"></i>
                                                    <?php echo ($cat['show_in_menu'] ?? 1) ? 'Show' : 'Hidden'; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="?toggle_pin=<?php echo $cat['id']; ?>" 
                                                   class="badge-status <?php echo ($cat['pin_to_menu'] ?? 0) ? 'badge-pinned' : 'badge-unpinned'; ?>"
                                                   onclick="return confirm('Toggle pin to main menu for <?php echo htmlspecialchars($cat['name']); ?>?')">
                                                    <i class="fas <?php echo ($cat['pin_to_menu'] ?? 0) ? 'fa-thumbtack' : 'fa-thumbtack'; ?> me-1"></i>
                                                    <?php echo ($cat['pin_to_menu'] ?? 0) ? 'Pinned' : 'Unpinned'; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-outline-gold" 
                                                            onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars($cat['name']); ?>', '<?php echo htmlspecialchars($cat['description'] ?? ''); ?>', '<?php echo $cat['icon'] ?? 'fa-tag'; ?>', <?php echo $cat['sort_order']; ?>, <?php echo $cat['show_in_menu'] ?? 1; ?>, <?php echo $cat['pin_to_menu'] ?? 0; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?delete_id=<?php echo $cat['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Delete category \'<?php echo htmlspecialchars($cat['name']); ?>\'?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="fas fa-tags" style="font-size:2rem;display:block;color:var(--light-gray);margin-bottom:8px;"></i>
                                            <div style="font-weight:600;color:var(--mid-gray);font-size:0.9rem;">No categories found</div>
                                            <div style="font-size:0.75rem;color:var(--mid-gray);">Click "Add Category" to create your first category</div>
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

    <!-- ===== ADD CATEGORY MODAL ===== -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g., Wood Working">
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Icon <small class="text-muted">(FontAwesome)</small></label>
                            <div class="row g-2">
                                <div class="col-9">
                                    <input type="text" name="icon" id="addIconInput" class="form-control" placeholder="fa-tree" value="fa-tag">
                                </div>
                                <div class="col-3">
                                    <div id="addIconPreview" class="icon-preview-box"><i class="fas fa-tag"></i></div>
                                </div>
                            </div>
                            <div class="icon-picker-grid">
                                <?php foreach($icons as $ic): ?>
                                <span class="icon-option <?php echo $ic == 'fa-tag' ? 'selected' : ''; ?>" 
                                      data-icon="<?php echo $ic; ?>" 
                                      onclick="selectIcon('add', '<?php echo $ic; ?>')">
                                    <i class="fas <?php echo $ic; ?>"></i>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this category"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="show_in_menu" id="addShowInMenu" checked value="1">
                                    <label class="form-check-label fw-bold" for="addShowInMenu" style="font-size:0.75rem;">
                                        <i class="fas fa-eye me-1" style="color:var(--gold);"></i> Show in Dropdown
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="pin_to_menu" id="addPinToMenu" value="1">
                                    <label class="form-check-label fw-bold" for="addPinToMenu" style="font-size:0.75rem;">
                                        <i class="fas fa-thumbtack me-1" style="color:var(--gold);"></i> Pin to Main Menu
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size:0.8rem;padding:6px 16px;">Cancel</button>
                        <button type="submit" name="add_category" class="btn btn-gold" style="font-size:0.8rem;padding:6px 16px;">
                            <i class="fas fa-save me-1"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== EDIT CATEGORY MODAL ===== -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="editId">
                        
                        <div class="mb-2">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="edit_name" id="editName" class="form-control" required>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Icon</label>
                            <div class="row g-2">
                                <div class="col-9">
                                    <input type="text" name="edit_icon" id="editIcon" class="form-control" placeholder="fa-tree">
                                </div>
                                <div class="col-3">
                                    <div id="editIconPreview" class="icon-preview-box"><i class="fas fa-tag"></i></div>
                                </div>
                            </div>
                            <div class="icon-picker-grid">
                                <?php foreach($icons as $ic): ?>
                                <span class="icon-option" data-icon="<?php echo $ic; ?>" onclick="selectIcon('edit', '<?php echo $ic; ?>')">
                                    <i class="fas <?php echo $ic; ?>"></i>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Description</label>
                            <textarea name="edit_description" id="editDescription" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="edit_sort_order" id="editSortOrder" class="form-control" min="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="edit_show_in_menu" id="editShowInMenu" value="1" checked>
                                    <label class="form-check-label fw-bold" for="editShowInMenu" style="font-size:0.75rem;">
                                        <i class="fas fa-eye me-1" style="color:var(--gold);"></i> Show in Dropdown
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" name="edit_pin_to_menu" id="editPinToMenu" value="1">
                                    <label class="form-check-label fw-bold" for="editPinToMenu" style="font-size:0.75rem;">
                                        <i class="fas fa-thumbtack me-1" style="color:var(--gold);"></i> Pin to Main Menu
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size:0.8rem;padding:6px 16px;">Cancel</button>
                        <button type="submit" name="edit_category" class="btn btn-gold" style="font-size:0.8rem;padding:6px 16px;">
                            <i class="fas fa-save me-1"></i> Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectIcon(type, icon) {
            if (type === 'add') {
                document.getElementById('addIconInput').value = icon;
                document.getElementById('addIconPreview').innerHTML = '<i class="fas ' + icon + '"></i>';
                document.querySelectorAll('#addCategoryModal .icon-option').forEach(el => {
                    el.classList.toggle('selected', el.dataset.icon === icon);
                });
            } else {
                document.getElementById('editIcon').value = icon;
                document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + icon + '"></i>';
                document.querySelectorAll('#editCategoryModal .icon-option').forEach(el => {
                    el.classList.toggle('selected', el.dataset.icon === icon);
                });
            }
        }

        document.getElementById('addIconInput').addEventListener('input', function() {
            document.getElementById('addIconPreview').innerHTML = '<i class="fas ' + this.value + '"></i>';
        });

        document.getElementById('editIcon')?.addEventListener('input', function() {
            document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + this.value + '"></i>';
        });

        function editCategory(id, name, description, icon, sort_order, show_in_menu, pin_to_menu) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editIcon').value = icon || 'fa-tag';
            document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + (icon || 'fa-tag') + '"></i>';
            document.getElementById('editDescription').value = description || '';
            document.getElementById('editSortOrder').value = sort_order || 0;
            document.getElementById('editShowInMenu').checked = show_in_menu == 1;
            document.getElementById('editPinToMenu').checked = pin_to_menu == 1;
            
            document.querySelectorAll('#editCategoryModal .icon-option').forEach(el => {
                el.classList.toggle('selected', el.dataset.icon === (icon || 'fa-tag'));
            });
            
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }

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