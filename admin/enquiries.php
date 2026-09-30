<?php

date_default_timezone_set('Asia/Kolkata');


// admin/enquiries.php - View All Contact Enquiries
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "includes/db.php";

date_default_timezone_set('Asia/Kolkata');

// Check if $conn is defined
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error. Please check db.php");
}

$page_title = "Enquiries";
$page_icon = "envelope";

// ============================================================
// UPDATE ENQUIRY STATUS - FIXED
// ============================================================
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    $valid_statuses = ['new', 'read', 'replied', 'closed'];
    
    if (in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE contact_enquiries SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Enquiry #" . $id . " status updated to '" . ucfirst($status) . "'!";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }
        $stmt->close();
        header("Location: enquiries.php");
        exit();
    }
}

// ============================================================
// DELETE ENQUIRY
// ============================================================
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contact_enquiries WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Enquiry deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete enquiry.";
    }
    $stmt->close();
    header("Location: enquiries.php");
    exit();
}

// ============================================================
// BULK DELETE
// ============================================================
if (isset($_POST['bulk_delete']) && isset($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM contact_enquiries WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        if ($stmt->execute()) {
            $_SESSION['success'] = count($ids) . " enquiries deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete enquiries.";
        }
        $stmt->close();
        header("Location: enquiries.php");
        exit();
    }
}

// ============================================================
// BULK STATUS UPDATE - FIXED
// ============================================================
if (isset($_POST['bulk_status']) && isset($_POST['selected_ids']) && isset($_POST['bulk_status_value'])) {
    $ids = $_POST['selected_ids'];
    $status = $_POST['bulk_status_value'];
    $valid_statuses = ['new', 'read', 'replied', 'closed'];
    
    if (!empty($ids) && in_array($status, $valid_statuses)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("UPDATE contact_enquiries SET status = ? WHERE id IN ($placeholders)");
        $stmt->bind_param($types . "s", ...array_merge($ids, [$status]));
        if ($stmt->execute()) {
            $_SESSION['success'] = count($ids) . " enquiries updated to '" . ucfirst($status) . "'!";
        } else {
            $_SESSION['error'] = "Failed to update enquiries.";
        }
        $stmt->close();
        header("Location: enquiries.php");
        exit();
    }
}

// ============================================================
// FETCH ENQUIRIES WITH FILTERS
// ============================================================
$status_filter = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$sql = "SELECT * FROM contact_enquiries WHERE 1=1";
$params = [];
$types = "";

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param]);
    $types .= "sssss";
}

if (!empty($date_from)) {
    $sql .= " AND DATE(created_at) >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND DATE(created_at) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$enquiries = [];
if (isset($conn) && $conn) {
    try {
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $enquiries[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) {
        $enquiries = [];
    }
}

// Get status counts
$status_counts = ['new' => 0, 'read' => 0, 'replied' => 0, 'closed' => 0, 'total' => 0];
if (isset($conn) && $conn) {
    $result = $conn->query("SELECT status, COUNT(*) as count FROM contact_enquiries GROUP BY status");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (isset($status_counts[$row['status']])) {
                $status_counts[$row['status']] = $row['count'];
            }
        }
    }
    $result = $conn->query("SELECT COUNT(*) as total FROM contact_enquiries");
    if ($result) {
        $row = $result->fetch_assoc();
        $status_counts['total'] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiries | Arup Enterprise Admin</title>
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

        /* ===== ADMIN HEADER ===== */
        .admin-header {
            background: var(--white);
            border-radius: 12px;
            padding: 16px 20px;
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
            font-size: 1.2rem;
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
            font-size: 0.75rem;
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
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-outline-gold:hover {
            background: var(--gold);
            color: white;
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            background: transparent;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }

        /* ===== STATUS BADGES ===== */
        .badge-status {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: inline-block;
        }
        .badge-new { background: #dbeafe; color: #1d4ed8; }
        .badge-read { background: #fef3c7; color: #b45309; }
        .badge-replied { background: #dcfce7; color: #166534; }
        .badge-closed { background: #f3f4f6; color: #6b7280; }

        /* ===== ALERT ===== */
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
        .alert-custom.alert-danger {
            background: #fef2f2;
            border-left-color: #b91c1c;
            color: #b91c1c;
        }

        /* ===== CARD ===== */
        .card-premium {
            background: var(--white);
            border: 1px solid rgba(201,146,10,0.1);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-premium .card-head {
            padding: 12px 16px;
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
            font-size: 0.85rem;
            color: var(--charcoal);
        }
        .card-premium .card-head h6 i { color: var(--gold); margin-right: 6px; }
        .card-premium .card-head .badge-count {
            background: var(--gold);
            color: white;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .card-premium .card-body {
            padding: 12px 16px;
        }

        /* ===== TABLE ===== */
        .table {
            color: var(--charcoal);
            margin: 0;
            font-size: 0.78rem;
        }
        .table thead th {
            padding: 8px 10px;
            font-weight: 700;
            color: var(--gold-dark);
            text-transform: uppercase;
            font-size: 0.55rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(201,146,10,0.12);
            background: var(--cream);
            white-space: nowrap;
        }
        .table tbody td {
            padding: 8px 10px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(201,146,10,0.05);
            font-size: 0.78rem;
        }
        .table tbody tr:hover { background: rgba(201,146,10,0.03); }
        .table tbody tr:last-child td { border-bottom: none; }

        .table .message-preview {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            color: var(--mid-gray);
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            padding: 8px 0;
        }
        .filter-bar .form-control,
        .filter-bar .form-select {
            font-size: 0.78rem;
            padding: 5px 10px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            min-width: 120px;
        }
        .filter-bar .form-control:focus,
        .filter-bar .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,146,10,0.08);
        }

        .content {
            padding: 16px 18px 30px;
        }

        /* ===== STATS ROW ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }
        .stat-box {
            background: var(--white);
            border-radius: 10px;
            padding: 12px 16px;
            border: 1px solid rgba(201,146,10,0.1);
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
        }
        .stat-box:hover {
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .stat-box .number {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--charcoal);
            display: block;
            line-height: 1.2;
        }
        .stat-box .label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--mid-gray);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-box .label i { color: var(--gold); margin-right: 4px; }
        .stat-box.new .number { color: #1d4ed8; }
        .stat-box.read .number { color: #b45309; }
        .stat-box.replied .number { color: #166534; }
        .stat-box.closed .number { color: #6b7280; }
        .stat-box.total .number { color: var(--gold); }

        @media (max-width: 768px) {
            .stats-row { grid-template-columns: repeat(3, 1fr); }
            .admin-header .title { font-size: 1rem; }
            .card-premium .card-body { padding: 8px 10px; }
            .table thead th, .table tbody td { padding: 4px 6px; font-size: 0.65rem; }
            .content { padding: 10px 12px 16px; }
            .filter-bar .form-control,
            .filter-bar .form-select { min-width: 80px; font-size: 0.7rem; }
        }
        @media (max-width: 480px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
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
                            <i class="fas fa-envelope"></i>
                            <span>Enquiries</span>
                        </div>
                        <div class="subtitle">Manage all contact form submissions and quote requests</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="enquiries.php" class="btn btn-gold">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
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
                <div class="alert alert-custom alert-danger mb-3">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- ===== STATS ROW ===== -->
            <div class="stats-row">
                <a href="enquiries.php" class="stat-box total">
                    <span class="number"><?php echo $status_counts['total']; ?></span>
                    <span class="label"><i class="fas fa-envelope"></i> Total</span>
                </a>
                <a href="enquiries.php?filter_status=new" class="stat-box new">
                    <span class="number"><?php echo $status_counts['new']; ?></span>
                    <span class="label"><i class="fas fa-circle" style="color:#1d4ed8;"></i> New</span>
                </a>
                <a href="enquiries.php?filter_status=read" class="stat-box read">
                    <span class="number"><?php echo $status_counts['read']; ?></span>
                    <span class="label"><i class="fas fa-circle" style="color:#b45309;"></i> Read</span>
                </a>
                <a href="enquiries.php?filter_status=replied" class="stat-box replied">
                    <span class="number"><?php echo $status_counts['replied']; ?></span>
                    <span class="label"><i class="fas fa-circle" style="color:#166534;"></i> Replied</span>
                </a>
                <a href="enquiries.php?filter_status=closed" class="stat-box closed">
                    <span class="number"><?php echo $status_counts['closed']; ?></span>
                    <span class="label"><i class="fas fa-circle" style="color:#6b7280;"></i> Closed</span>
                </a>
            </div>

            <!-- ===== FILTER BAR ===== -->
            <div class="card-premium mb-3">
                <div class="card-body">
                    <form method="GET" action="enquiries.php" class="filter-bar">
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" style="min-width:180px;">
                        
                        <select name="filter_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="new" <?php echo $status_filter == 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="read" <?php echo $status_filter == 'read' ? 'selected' : ''; ?>>Read</option>
                            <option value="replied" <?php echo $status_filter == 'replied' ? 'selected' : ''; ?>>Replied</option>
                            <option value="closed" <?php echo $status_filter == 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>

                        <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" style="min-width:130px;">
                        <span style="color:var(--mid-gray);font-size:0.7rem;">to</span>
                        <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" style="min-width:130px;">

                        <button type="submit" class="btn btn-gold">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="enquiries.php" class="btn btn-outline-gold">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </form>
                </div>
            </div>

            <!-- ===== MAIN TABLE ===== -->
            <div class="card-premium">
                <div class="card-head">
                    <h6><i class="fas fa-list-ul"></i> All Enquiries</h6>
                    <span class="badge-count"><i class="fas fa-database"></i> <?php echo count($enquiries); ?> Records</span>
                </div>
                <div class="card-body">

                    <?php if (empty($enquiries)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox" style="font-size:3rem;color:var(--light-gray);display:block;margin-bottom:12px;"></i>
                            <h5 style="font-weight:700;color:var(--mid-gray);">No Enquiries Found</h5>
                            <p style="color:var(--mid-gray);font-size:0.85rem;">Contact form submissions will appear here</p>
                        </div>
                    <?php else: ?>

                    <form method="POST" action="enquiries.php" id="bulkForm">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width:30px;">
                                            <input type="checkbox" id="selectAll" class="form-check-input" style="cursor:pointer;">
                                        </th>
                                        <th style="width:50px;"><i class="fas fa-hashtag"></i></th>
                                        <th><i class="fas fa-user"></i> Name</th>
                                        <th><i class="fas fa-address-card"></i> Contact</th>
                                        <th><i class="fas fa-comment"></i> Message</th>
                                        <th><i class="fas fa-tag"></i> Product</th>
                                        <th style="width:90px;"><i class="fas fa-flag"></i> Status</th>
                                        <th style="width:130px;"><i class="fas fa-calendar"></i> Date</th>
                                        <th style="width:160px;"><i class="fas fa-cog"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enquiries as $e): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="<?php echo $e['id']; ?>" class="form-check-input row-checkbox" style="cursor:pointer;">
                                        </td>
                                        <td class="fw-bold text-muted"><?php echo $e['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?></strong>
                                        </td>
                                        <td>
                                            <div><i class="fas fa-phone" style="color:var(--gold);font-size:0.65rem;width:14px;"></i> <?php echo htmlspecialchars($e['phone']); ?></div>
                                            <div><i class="fas fa-envelope" style="color:var(--gold);font-size:0.65rem;width:14px;"></i> <?php echo htmlspecialchars($e['email']); ?></div>
                                        </td>
                                        <td>
                                            <span class="message-preview" title="<?php echo htmlspecialchars($e['message']); ?>">
                                                <?php echo htmlspecialchars(substr($e['message'] ?? '', 0, 60)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($e['product_name'])): ?>
                                                <span class="badge" style="background:rgba(201,146,10,0.15);color:var(--gold-dark);font-weight:600;font-size:0.65rem;">
                                                    <i class="fas fa-cube"></i> <?php echo htmlspecialchars($e['product_name']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color:#999;font-size:0.7rem;"><i class="fas fa-minus"></i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-<?php echo $e['status']; ?>">
                                                <?php if ($e['status'] == 'new'): ?><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;color:#1d4ed8;"></i><?php endif; ?>
                                                <?php if ($e['status'] == 'read'): ?><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;color:#b45309;"></i><?php endif; ?>
                                                <?php if ($e['status'] == 'replied'): ?><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;color:#166534;"></i><?php endif; ?>
                                                <?php if ($e['status'] == 'closed'): ?><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;color:#6b7280;"></i><?php endif; ?>
                                                <?php echo ucfirst($e['status']); ?>
                                            </span>
                                        </td>
                                        <td style="font-size:0.7rem;">
                                            <i class="far fa-calendar-alt" style="color:var(--gold);width:14px;"></i> <?php echo date('d M Y', strtotime($e['created_at'])); ?>
                                            <br><i class="far fa-clock" style="color:var(--gold);width:14px;"></i> <?php echo date('h:i A', strtotime($e['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <!-- Status Dropdown -->
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-gold dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Change Status">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="?status=new&id=<?php echo $e['id']; ?>"><i class="fas fa-circle" style="color:#1d4ed8;"></i> New</a></li>
                                                        <li><a class="dropdown-item" href="?status=read&id=<?php echo $e['id']; ?>"><i class="fas fa-circle" style="color:#b45309;"></i> Read</a></li>
                                                        <li><a class="dropdown-item" href="?status=replied&id=<?php echo $e['id']; ?>"><i class="fas fa-circle" style="color:#166534;"></i> Replied</a></li>
                                                        <li><a class="dropdown-item" href="?status=closed&id=<?php echo $e['id']; ?>"><i class="fas fa-circle" style="color:#6b7280;"></i> Closed</a></li>
                                                    </ul>
                                                </div>

                                                <!-- WhatsApp -->
                                                <a href="https://wa.me/91<?php echo preg_replace('/[^0-9]/', '', $e['phone']); ?>?text=Hi%20<?php echo urlencode($e['first_name']); ?>%2C%20Thank%20you%20for%20your%20enquiry%20about%20<?php echo urlencode($e['product_name'] ?? 'our products'); ?>.%20We%27ll%20get%20back%20to%20you%20shortly." target="_blank" class="btn btn-sm" style="background:#25D366;color:white;border:none;padding:4px 8px;border-radius:6px;font-size:0.65rem;" title="Chat on WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>

                                                <!-- Delete -->
                                                <a href="?delete_id=<?php echo $e['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this enquiry?')" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="mt-3 d-flex gap-2 align-items-center flex-wrap">
                            <span style="font-size:0.75rem;font-weight:600;color:var(--mid-gray);"><i class="fas fa-tasks"></i> Bulk Actions:</span>
                            <select name="bulk_status_value" class="form-select" style="width:auto;font-size:0.75rem;padding:4px 10px;border:2px solid var(--light-gray);border-radius:6px;">
                                <option value="new"><i class="fas fa-circle" style="color:#1d4ed8;"></i> New</option>
                                <option value="read"><i class="fas fa-circle" style="color:#b45309;"></i> Read</option>
                                <option value="replied"><i class="fas fa-circle" style="color:#166534;"></i> Replied</option>
                                <option value="closed"><i class="fas fa-circle" style="color:#6b7280;"></i> Closed</option>
                            </select>
                            <button type="submit" name="bulk_status" class="btn btn-sm btn-gold" style="font-size:0.7rem;padding:4px 12px;">
                                <i class="fas fa-check"></i> Update
                            </button>
                            <button type="submit" name="bulk_delete" class="btn btn-sm btn-outline-danger" style="font-size:0.7rem;padding:4px 12px;" onclick="return confirm('Delete selected enquiries?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                            <span id="selectedCount" style="font-size:0.7rem;color:var(--mid-gray);"><i class="fas fa-check-circle"></i> 0 selected</span>
                        </div>
                    </form>

                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== SELECT ALL =====
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedCount();
        });

        // ===== UPDATE SELECTED COUNT =====
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            document.getElementById('selectedCount').innerHTML = '<i class="fas fa-check-circle"></i> ' + checked + ' selected';
        }

        // ===== AUTO-HIDE ALERTS =====
        setTimeout(() => {
            document.querySelectorAll('.alert-custom').forEach(el => {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>