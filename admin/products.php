<?php
// admin/products.php - Product Management
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

$page_title = "Products";
$page_icon = "boxes";

// ================= FETCH PRODUCTS =================
$products = [];
$result = $conn->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM products WHERE category = p.category) as total_in_category
    FROM products p 
    ORDER BY p.category, p.sort_order ASC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// ================= FETCH CATEGORIES =================
$categories = [];
$catResult = $conn->query("SELECT * FROM categories WHERE status='active' ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// ================= DELETE PRODUCT =================
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $imgStmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $imgStmt->bind_param("i", $id);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();
    if ($imgRow = $imgResult->fetch_assoc()) {
        if (!empty($imgRow['image']) && file_exists("../" . $imgRow['image'])) {
            unlink("../" . $imgRow['image']);
        }
    }
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product deleted successfully!";
        header("Location: products.php");
        exit();
    }
}

// ================= TOGGLE FEATURED =================
if (isset($_GET['toggle_featured'])) {
    $id = intval($_GET['toggle_featured']);
    $stmt = $conn->prepare("UPDATE products SET featured = IF(featured=1, 0, 1) WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product featured status updated!";
        header("Location: products.php");
        exit();
    }
}

// ================= TOGGLE STATUS =================
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $stmt = $conn->prepare("UPDATE products SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Product status updated!";
        header("Location: products.php");
        exit();
    }
}

// ✅ Include admin navbar
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | DipBan Admin</title>
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

        .content {
            padding: 16px 20px 30px;
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
        }
        .admin-header .title span { color: var(--gold); }
        .admin-header .subtitle {
            font-size: 0.7rem;
            color: var(--mid-gray);
            margin: 0;
        }

        .alert-custom {
            background: #f0fdf4;
            border-left: 4px solid var(--gold);
            border-radius: 10px;
            padding: 10px 16px;
            color: #166534;
            font-weight: 500;
            font-size: 0.82rem;
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
            gap: 10px;
        }
        .card-premium .card-head h6 {
            font-weight: 700;
            margin: 0;
            font-size: 0.78rem;
            color: var(--charcoal);
        }
        .card-premium .card-head h6 i { color: var(--gold); margin-right: 6px; }
        .card-premium .card-head .head-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .card-premium .card-head .badge-count {
            background: var(--gold);
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* ================================================
           PREMIUM LIVE SEARCH BAR
        ================================================ */
        .product-search-wrap {
            position: relative;
            max-width: 300px;
            width: 100%;
        }
        .product-search-wrap i.search-ico {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold-dark);
            font-size: 0.75rem;
            pointer-events: none;
            transition: color 0.2s;
        }
        #productSearchInput {
            width: 100%;
            padding: 8px 32px 8px 32px;
            border: 1.5px solid rgba(201,146,10,0.22);
            border-radius: 20px;
            background: var(--cream);
            font-family: 'Inter', sans-serif;
            font-size: 0.78rem;
            color: var(--charcoal);
            outline: none;
            transition: all 0.25s ease;
        }
        #productSearchInput::placeholder { color: #b8a98f; }
        #productSearchInput:focus {
            border-color: var(--gold);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(201,146,10,0.12);
        }
        .product-search-wrap:has(#productSearchInput:focus) i.search-ico { color: var(--gold); }
        .search-clear-btn {
            position: absolute;
            right: 7px;
            top: 50%;
            transform: translateY(-50%);
            width: 19px;
            height: 19px;
            border: none;
            background: rgba(201,146,10,0.14);
            color: var(--gold-dark);
            border-radius: 50%;
            font-size: 0.62rem;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }
        .search-clear-btn:hover { background: var(--gold); color: #fff; }
        #productSearchInput:not(:placeholder-shown) ~ .search-clear-btn { display: flex; }

        .search-results-hint {
            font-size: 0.68rem;
            color: var(--mid-gray);
            white-space: nowrap;
        }
        .search-results-hint strong { color: var(--gold-dark); }

        .search-no-results {
            display: none;
            text-align: center;
            padding: 36px 10px;
        }
        .search-no-results i { font-size: 1.8rem; color: var(--light-gray); display: block; margin-bottom: 8px; }
        .search-no-results .snr-text { font-weight: 600; color: var(--mid-gray); font-size: 0.85rem; }
        .search-no-results .snr-sub { font-size: 0.72rem; color: var(--mid-gray); margin-top: 2px; }

        /* smooth filtering — fade rows in/out instead of an abrupt jump */
        .table tbody tr {
            transition: opacity 0.16s ease;
        }
        .table tbody tr.row-hidden {
            display: none;
        }
        mark.search-highlight {
            background: rgba(201,146,10,0.3);
            color: inherit;
            border-radius: 3px;
            padding: 0 2px;
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

        .btn-gold {
            background: var(--gold);
            color: white;
            border: none;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-gold:hover {
            background: var(--gold-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(201,146,10,0.3);
        }

        .btn-outline-gold {
            border: 2px solid var(--gold);
            color: var(--gold);
            background: transparent;
            padding: 3px 10px;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: white;
        }

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
        .badge-draft { background: #fef3c7; color: #b45309; }

        .image-preview-small {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid rgba(201,146,10,0.1);
        }
        .image-placeholder {
            width: 50px;
            height: 50px;
            background: var(--ivory);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 1.2rem;
            border: 2px solid rgba(201,146,10,0.1);
        }

        @media (max-width: 768px) {
            .admin-header { padding: 10px 14px; }
            .admin-header .title { font-size: 0.9rem; }
            .card-premium .card-body { padding: 10px 12px; }
            .table thead th, .table tbody td { padding: 6px 8px; font-size: 0.65rem; }
            .image-preview-small { width: 36px; height: 36px; }
            .image-placeholder { width: 36px; height: 36px; font-size: 0.9rem; }
            .content { padding: 12px 14px 20px; }
            .product-search-wrap { max-width: 100%; }
            .card-premium .card-head .head-right { width: 100%; justify-content: space-between; }
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
                            <i class="fas fa-boxes"></i>
                            <span>Products</span>
                        </div>
                        <div class="subtitle">Manage your product inventory</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="add_product.php" class="btn-gold">
                            <i class="fas fa-plus"></i> Add Product
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
                    <h6><i class="fas fa-list-ul"></i> All Products</h6>
                    <div class="head-right">
                        <!-- ✅ PREMIUM LIVE SEARCH -->
                        <div class="product-search-wrap">
                            <i class="fas fa-search search-ico"></i>
                            <input type="text" id="productSearchInput" placeholder="Search products by name..." autocomplete="off">
                            <button type="button" class="search-clear-btn" id="searchClearBtn" title="Clear search">&times;</button>
                        </div>
                        <span class="search-results-hint" id="searchResultsHint"></span>
                        <span class="badge-count" id="productCountBadge"><?php echo count($products); ?> Records</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th style="width:35px;">#</th>
                                    <th style="width:60px;">Image</th>
                                    <th>Product Name</th>
                                    <th style="width:130px;">Category</th>
                                    <th style="width:75px;">Status</th>
                                    <th style="width:65px;">Featured</th>
                                    <th style="width:60px;">Sort</th>
                                    <th style="width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $p): ?>
                                        <tr data-product-name="<?php echo htmlspecialchars(mb_strtolower($p['name'])); ?>">
                                            <td class="fw-bold text-muted"><?php echo $p['id']; ?></td>
                                            <td>
                                                <?php if (!empty($p['image']) && file_exists("../" . $p['image'])): ?>
                                                    <img src="../<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="image-preview-small">
                                                <?php else: ?>
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-cog"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong class="js-product-name"><?php echo htmlspecialchars($p['name']); ?></strong>
                                                <?php if (!empty($p['sub_category'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($p['sub_category']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge" style="background:rgba(201,146,10,0.12);color:var(--gold-dark);font-weight:500;font-size:0.7rem;">
                                                    <?php echo htmlspecialchars($p['category']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?toggle_status=<?php echo $p['id']; ?>" 
                                                   class="badge-status <?php echo $p['status'] == 'active' ? 'badge-active' : ($p['status'] == 'draft' ? 'badge-draft' : 'badge-inactive'); ?>"
                                                   onclick="return confirm('Toggle status for <?php echo htmlspecialchars($p['name']); ?>?')">
                                                    <?php echo ucfirst($p['status']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="?toggle_featured=<?php echo $p['id']; ?>" 
                                                   class="btn btn-sm <?php echo $p['featured'] ? 'btn-warning' : 'btn-outline-secondary'; ?>" 
                                                   style="padding:2px 8px;border-radius:20px;font-size:0.6rem;"
                                                   onclick="return confirm('Toggle featured status for <?php echo htmlspecialchars($p['name']); ?>?')">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                            </td>
                                            <td><?php echo $p['sort_order']; ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="add_product.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-gold">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete_id=<?php echo $p['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       style="font-size:0.7rem;padding:2px 8px;"
                                                       onclick="return confirm('Delete product \'<?php echo htmlspecialchars($p['name']); ?>\'?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-boxes" style="font-size:2rem;display:block;color:var(--light-gray);margin-bottom:6px;"></i>
                                            <div style="font-weight:600;color:var(--mid-gray);font-size:0.9rem;">No products found</div>
                                            <div style="font-size:0.75rem;color:var(--mid-gray);">Click "Add Product" to create your first product</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- ✅ Shown only when a search has zero matches -->
                        <div class="search-no-results" id="searchNoResults">
                            <i class="fas fa-search"></i>
                            <div class="snr-text">No products match your search</div>
                            <div class="snr-sub">Try a different name or <a href="#" id="snrClearLink" style="color:var(--gold-dark);font-weight:700;">clear the search</a></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== AUTO-HIDE ALERTS =====
        setTimeout(() => {
            document.querySelectorAll('.alert-custom').forEach(el => {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);

        // ============================================================
        // ✅ PREMIUM LIVE PRODUCT SEARCH
        // Filters table rows by product name as you type — smooth,
        // no page reload, with highlight + "no results" state.
        // ============================================================
        (function () {
            const input      = document.getElementById('productSearchInput');
            const clearBtn    = document.getElementById('searchClearBtn');
            const table       = document.getElementById('productsTable');
            const hint        = document.getElementById('searchResultsHint');
            const countBadge  = document.getElementById('productCountBadge');
            const noResults   = document.getElementById('searchNoResults');
            const snrClear    = document.getElementById('snrClearLink');
            if (!input || !table) return;

            const rows = Array.from(table.querySelectorAll('tbody tr[data-product-name]'));
            const totalCount = rows.length;
            let debounceTimer = null;

            function highlight(el, term) {
                const original = el.dataset.originalText || el.textContent;
                el.dataset.originalText = original;
                if (!term) {
                    el.innerHTML = original;
                    return;
                }
                const idx = original.toLowerCase().indexOf(term.toLowerCase());
                if (idx === -1) {
                    el.innerHTML = original;
                    return;
                }
                const before = original.slice(0, idx);
                const match  = original.slice(idx, idx + term.length);
                const after  = original.slice(idx + term.length);
                el.innerHTML = before + '<mark class="search-highlight">' + match + '</mark>' + after;
            }

            function runFilter() {
                const term = input.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.dataset.productName || '';
                    const matches = !term || name.includes(term);
                    row.classList.toggle('row-hidden', !matches);
                    if (matches) visibleCount++;

                    const nameEl = row.querySelector('.js-product-name');
                    if (nameEl) highlight(nameEl, term);
                });

                if (table.querySelector('tbody tr[colspan]')) {
                    // empty-state row from PHP (no products at all) — leave as-is
                }

                noResults.style.display = (term && visibleCount === 0 && totalCount > 0) ? 'block' : 'none';
                table.style.display = (term && visibleCount === 0 && totalCount > 0) ? 'none' : '';

                if (term) {
                    hint.innerHTML = 'Showing <strong>' + visibleCount + '</strong> of ' + totalCount;
                    countBadge.textContent = visibleCount + ' Matched';
                } else {
                    hint.innerHTML = '';
                    countBadge.textContent = totalCount + ' Records';
                }
            }

            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runFilter, 120); // small debounce keeps it smooth while typing fast
            });

            clearBtn.addEventListener('click', function () {
                input.value = '';
                input.focus();
                runFilter();
            });

            snrClear.addEventListener('click', function (e) {
                e.preventDefault();
                input.value = '';
                input.focus();
                runFilter();
            });

            // Press "/" anywhere on the page to jump straight into search (handy power-user shortcut)
            document.addEventListener('keydown', function (e) {
                if (e.key === '/' && document.activeElement !== input) {
                    e.preventDefault();
                    input.focus();
                }
                if (e.key === 'Escape' && document.activeElement === input) {
                    input.value = '';
                    runFilter();
                    input.blur();
                }
            });
        })();
    </script>
</body>
</html>