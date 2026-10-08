<?php
// admin/view_contact.php - Contact Messages Admin Panel
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

// TEMPORARY FIX: Alter table to remove strict ENUM constraint on status
$conn->query("ALTER TABLE contact_messages MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'New'");

// ================= UPDATE STATUS =================
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = trim($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?status_updated=1");
        exit;
    }
}

// ================= DELETE =================
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
        exit;
    }
}

// ================= FETCH ALL MESSAGES =================
// Check which date column exists
$columns = $conn->query("SHOW COLUMNS FROM contact_messages");
$hasCreatedAt = false;
$hasSubmittedAt = false;
while ($col = $columns->fetch_assoc()) {
    if ($col['Field'] == 'created_at') $hasCreatedAt = true;
    if ($col['Field'] == 'submitted_at') $hasSubmittedAt = true;
}

// Build query with time conversion
$dateColumn = 'created_at';
if (!$hasCreatedAt && $hasSubmittedAt) {
    $dateColumn = 'submitted_at';
} elseif (!$hasCreatedAt && !$hasSubmittedAt) {
    $dateColumn = 'id';
}

// ✅ FIX: Convert UTC to IST directly in SQL
$sql = "SELECT 
            *,
            DATE_FORMAT(CONVERT_TZ($dateColumn, '+00:00', '+05:30'), '%d %b %Y') as display_date,
            DATE_FORMAT(CONVERT_TZ($dateColumn, '+00:00', '+05:30'), '%h:%i %p') as display_time,
            CONVERT_TZ($dateColumn, '+00:00', '+05:30') as display_datetime
        FROM contact_messages 
        ORDER BY $dateColumn DESC";

$result = $conn->query($sql);
$messages = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
$total_msgs = count($messages);

// Count by status
$status_counts = [
    'New' => 0,
    'Called' => 0,
    'Not Picked' => 0,
    'Replied' => 0,
    'Resolved' => 0
];

foreach($messages as $msg) {
    $s = $msg['status'] ?? 'New';
    // Normalize case dynamically without hardcoding
    $s = ucwords(strtolower($s));

    if(isset($status_counts[$s])) {
        $status_counts[$s]++;
    }
}

$page_title = "Contact Messages Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | Arup Enterprise Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ✅ Admin Panel CSS */
        :root {
            --gold: #DC2626;
            --gold-dark: #B91C1C;
            --gold-light: #EF4444;
            --cream: #FFF5F5;
            --ivory: #FEE2E2;
            --mustard: #F87171;
            --charcoal: #111827;
            --mid-gray: #4B5563;
            --light-gray: #FECACA;
            --white: #ffffff;
            --shadow-gold: 0 4px 20px rgba(220,38,38,0.2);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            font-size: 14px;
        }

        /* ✅ Admin Header */
        .admin-header {
            background: var(--white);
            border-radius: 16px;
            padding: 16px 24px;
            margin-bottom: 24px;
            border: 1px solid rgba(220,38,38,0.12);
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
        .admin-header .title span { 
            color: var(--gold); 
        }
        .admin-header .subtitle {
            font-size: 0.75rem;
            color: var(--mid-gray);
            margin: 0;
        }

        /* ✅ Stats Bar */
        .stats-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
        }
        .stat-item {
            text-align: center;
            background: var(--cream);
            padding: 6px 16px;
            border-radius: 24px;
            border: 1px solid rgba(220,38,38,0.15);
            transition: all 0.3s;
        }
        .stat-item:hover {
            border-color: var(--gold);
            box-shadow: var(--shadow-gold);
        }
        .stat-item .num {
            font-weight: 900;
            font-size: 1rem;
            color: var(--gold-dark);
        }
        .stat-item .lbl {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--mid-gray);
            font-weight: 600;
            margin-left: 6px;
            letter-spacing: 0.5px;
        }
        .stat-item .lbl i { 
            font-size: 0.6rem;
            color: var(--gold);
            margin-right: 3px;
        }

        /* ✅ Alerts */
        .alert-custom {
            background: #f0fdf4;
            border-left: 4px solid var(--gold);
            border-radius: 12px;
            padding: 12px 18px;
            color: #166534;
            font-weight: 500;
            font-size: 0.85rem;
            border: 1px solid rgba(220,38,38,0.15);
        }
        .alert-custom i { 
            color: var(--gold); 
            margin-right: 10px; 
        }

        /* ✅ Main Card */
        .card-premium {
            background: var(--white);
            border: 1px solid rgba(220,38,38,0.1);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .card-premium .card-head {
            padding: 14px 22px;
            background: linear-gradient(135deg, var(--cream), var(--ivory));
            border-bottom: 2px solid rgba(220,38,38,0.08);
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
        .card-premium .card-head h6 i { 
            color: var(--gold); 
            margin-right: 10px;
        }
        .card-premium .card-head .badge-count {
            background: var(--gold);
            color: white;
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .card-premium .card-body {
            padding: 18px 22px;
        }

        /* ✅ Table */
        .table {
            color: var(--charcoal);
            margin: 0;
            font-size: 0.82rem;
        }
        .table thead th {
            padding: 12px 14px;
            font-weight: 700;
            color: var(--gold-dark);
            text-transform: uppercase;
            font-size: 0.6rem;
            letter-spacing: 0.8px;
            border-bottom: 2px solid rgba(220,38,38,0.15);
            background: var(--cream);
        }
        .table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(220,38,38,0.06);
            font-size: 0.8rem;
        }
        .table tbody tr:hover { 
            background: rgba(220,38,38,0.04); 
        }
        .table tbody tr:last-child td { 
            border-bottom: none; 
        }

        /* ✅ Message Box */
        .message-box {
            max-width: 320px;
            font-size: 0.8rem;
            color: var(--mid-gray);
            line-height: 1.6;
        }
        .read-more {
            color: var(--gold-dark);
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 700;
            transition: color 0.3s;
            border: none;
            background: transparent;
            padding: 0;
        }
        .read-more:hover { 
            color: var(--gold); 
        }

        /* ✅ Status Select */
        .status-select {
            border: none;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.7rem;
            font-weight: 700;
            cursor: pointer;
            width: 115px;
            transition: all 0.3s;
            background: var(--cream);
            font-family: 'Inter', sans-serif;
        }
        .status-select:focus { 
            outline: 2px solid var(--gold); 
        }
        .st-New { background: #eff6ff; color: #1d4ed8; }
        .st-Called { background: #f0fdf4; color: #15803d; }
        .st-NotPicked { background: #fef3c7; color: #b45309; }
        .st-Replied { background: #f3e8ff; color: #7c3aed; }
        .st-Resolved { background: #dcfce7; color: #166534; }

        /* ✅ Buttons */
        .btn-delete {
            background: #fef2f2;
            color: #b91c1c;
            border: none;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-delete:hover {
            background: #fecaca;
            color: #991b1b;
            transform: scale(1.05);
        }

        .btn-view {
            background: rgba(220,38,38,0.1);
            color: var(--gold-dark);
            border: none;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-view:hover {
            background: var(--gold);
            color: white;
            transform: scale(1.05);
        }

        .btn-delete i, .btn-view i {
            font-size: 0.65rem;
        }

        /* ✅ Modals */
        .modal-content-custom {
            border: 2px solid var(--gold);
            border-radius: 16px;
        }
        .modal-content-custom .modal-header {
            background: linear-gradient(135deg, var(--charcoal), #2a2a2a);
            border-bottom: 2px solid var(--gold);
            padding: 14px 22px;
        }
        .modal-content-custom .modal-header h5 {
            color: var(--white);
            font-size: 1rem;
        }
        .modal-content-custom .modal-header h5 i { 
            color: var(--gold); 
            margin-right: 10px;
        }
        .modal-content-custom .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .modal-content-custom .modal-body {
            color: var(--charcoal);
            padding: 28px;
        }
        .modal-main-icon {
            color: var(--gold);
            font-size: 2.8rem;
            margin-bottom: 12px;
            display: inline-block;
        }
        .modal-content-custom .modal-body h4 { 
            color: var(--gold-dark); 
            font-size: 1.2rem;
            font-weight: 800;
        }

        .btn-modal-danger {
            background: #b91c1c;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: 700;
            font-size: 0.8rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-modal-danger:hover {
            background: #991b1b;
            transform: translateY(-2px);
            color: white;
        }
        .btn-modal-cancel {
            background: var(--light-gray);
            color: var(--charcoal);
            border: none;
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-modal-cancel:hover { 
            background: #d5cdbc; 
            color: var(--charcoal);
        }

        /* ✅ Detail Modal */
        .detail-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid rgba(220,38,38,0.08);
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-item .label {
            font-weight: 700;
            color: var(--gold-dark);
            width: 100px;
            flex-shrink: 0;
            font-size: 0.8rem;
        }
        .detail-item .label i {
            color: var(--gold);
            margin-right: 6px;
            width: 16px;
        }
        .detail-item .value {
            color: var(--charcoal);
            font-size: 0.85rem;
        }
        .detail-item .value .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .badge-new { background: #eff6ff; color: #1d4ed8; }
        .badge-called { background: #f0fdf4; color: #15803d; }
        .badge-notpicked { background: #fef3c7; color: #b45309; }
        .badge-replied { background: #f3e8ff; color: #7c3aed; }
        .badge-resolved { background: #dcfce7; color: #166534; }

        /* ✅ Content Padding */
        .content {
            padding: 20px 20px 40px;
        }

        /* ✅ Responsive */
        @media (max-width: 992px) {
            .admin-header { padding: 14px 18px; }
            .admin-header .title { font-size: 1rem; }
            .card-premium .card-body { padding: 14px 16px; }
            .table thead th, .table tbody td { 
                padding: 8px 10px; 
                font-size: 0.7rem; 
            }
            .message-box { max-width: 150px; }
            .status-select { width: 90px; font-size: 0.6rem; padding: 4px 8px; }
            .stats-bar { gap: 6px; justify-content: flex-start; }
            .stat-item { padding: 4px 10px; }
            .stat-item .num { font-size: 0.8rem; }
            .stat-item .lbl { font-size: 0.5rem; }
        }

        @media (max-width: 768px) {
            .admin-header .title { font-size: 0.9rem; }
            .admin-header .subtitle { font-size: 0.6rem; }
            .card-premium .card-head { padding: 10px 14px; }
            .card-premium .card-head h6 { font-size: 0.7rem; }
            .card-premium .card-head .badge-count { font-size: 0.55rem; padding: 2px 10px; }
            .table { font-size: 0.65rem; }
            .table thead th { font-size: 0.5rem; padding: 6px 8px; }
            .table tbody td { padding: 6px 8px; font-size: 0.6rem; }
            .message-box { max-width: 100px; font-size: 0.6rem; }
            .btn-delete, .btn-view { font-size: 0.55rem; padding: 2px 8px; }
            .btn-delete i, .btn-view i { font-size: 0.5rem; }
            .status-select { width: 75px; font-size: 0.5rem; padding: 3px 6px; }
            .detail-item { flex-direction: column; gap: 4px; }
            .detail-item .label { width: 100%; }
        }

        @media (max-width: 480px) {
            .content { padding: 10px; }
            .admin-header { padding: 10px 14px; }
            .card-premium .card-body { padding: 8px 10px; }
            .table thead th, .table tbody td { 
                padding: 4px 6px; 
                font-size: 0.55rem; 
            }
            .status-select { width: 60px; font-size: 0.45rem; padding: 2px 4px; }
            .stats-bar { gap: 4px; }
            .stat-item { padding: 2px 6px; }
            .stat-item .num { font-size: 0.65rem; }
            .stat-item .lbl { font-size: 0.4rem; }
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
                            <span>Contact</span> Messages
                        </div>
                        <div class="subtitle">Manage all customer enquiries and support requests</div>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-bar">
                            <span class="stat-item">
                                <span class="num"><?php echo $total_msgs; ?></span>
                                <span class="lbl"><i class="fas fa-inbox"></i> Total</span>
                            </span>
                            <span class="stat-item">
                                <span class="num"><?php echo $status_counts['New']; ?></span>
                                <span class="lbl"><i class="fas fa-clock"></i> New</span>
                            </span>
                            <span class="stat-item">
                                <span class="num"><?php echo $status_counts['Called']; ?></span>
                                <span class="lbl"><i class="fas fa-phone"></i> Called</span>
                            </span>
                            <span class="stat-item">
                                <span class="num"><?php echo $status_counts['Resolved']; ?></span>
                                <span class="lbl"><i class="fas fa-check-circle"></i> Done</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-custom mb-3">
                    <i class="fas fa-check-circle"></i> Message deleted successfully!
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['status_updated'])): ?>
                <div class="alert alert-custom mb-3">
                    <i class="fas fa-check-circle"></i> Status updated successfully!
                </div>
            <?php endif; ?>

            <!-- ===== MAIN CARD ===== -->
            <div class="card-premium">
                <div class="card-head">
                    <h6><i class="fas fa-list-ul"></i> All Messages</h6>
                    <span class="badge-count"><?php echo $total_msgs; ?> Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:60px;">ID</th>
                                    <th style="min-width:180px;">Sender Details</th>
                                    <th style="min-width:100px;">Subject</th>
                                    <th style="min-width:200px;">Message</th>
                                    <th style="min-width:120px;">Date & Time <span style="font-weight:400;font-size:0.5rem;">(IST)</span></th>
                                    <th style="min-width:120px;">Status</th>
                                    <th style="min-width:90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($messages)): ?>
                                    <?php foreach ($messages as $row): 
                                        $fullMessage = htmlspecialchars($row['message']);
                                        $shortMessage = substr($fullMessage, 0, 120);
                                        $isLong = strlen($fullMessage) > 120;
                                        $status = $row['status'] ?? 'New';
                                        // Normalize case dynamically without hardcoding
                                        $status = ucwords(strtolower($status));

                                        $statusClass = str_replace(' ', '', $status);
                                        
                                        $displayDate = $row['display_date'] ?? date('d M Y');
                                        $displayTime = $row['display_time'] ?? date('h:i A');
                                    ?>
                                        <tr>
                                            <td class="fw-700" style="color:var(--gold-dark);">#<?php echo $row['id']; ?></td>
                                            <td>
                                                <div class="fw-700" style="font-size:0.82rem;"><?php echo htmlspecialchars($row['name']); ?></div>
                                                <div class="text-muted" style="font-size:0.65rem;">
                                                    <i class="fas fa-envelope" style="color:var(--gold);"></i> <?php echo htmlspecialchars($row['email']); ?>
                                                </div>
                                                <div class="text-muted" style="font-size:0.65rem;">
                                                    <i class="fas fa-phone" style="color:var(--gold);"></i> <?php echo htmlspecialchars($row['phone']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" style="background:rgba(220,38,38,0.12); color:var(--gold-dark); padding:4px 12px; font-size:0.7rem; font-weight:600;">
                                                    <?php echo htmlspecialchars($row['subject'] ?? 'General Inquiry'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="message-box">
                                                    <span class="short-text"><?php echo nl2br($shortMessage); ?><?php echo $isLong ? '...' : ''; ?></span>
                                                    <?php if ($isLong): ?>
                                                        <span class="full-text d-none"><?php echo nl2br($fullMessage); ?></span>
                                                        <br><button class="read-more" onclick="toggleText(this)">Read More</button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="font-size:0.7rem; color:var(--mid-gray);">
                                                <i class="far fa-calendar-alt" style="color:var(--gold);"></i> <?php echo $displayDate; ?>
                                                <br><i class="far fa-clock" style="color:var(--gold);"></i> <?php echo $displayTime; ?>
                                            </td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <select name="status" class="status-select st-<?php echo $statusClass; ?>" onchange="this.form.submit()">
                                                        <option value="New" <?php echo $status=='New'?'selected':''; ?>>New</option>
                                                        <option value="Called" <?php echo $status=='Called'?'selected':''; ?>>Called</option>
                                                        <option value="Not Picked" <?php echo $status=='Not Picked'?'selected':''; ?>>Not Picked</option>
                                                        <option value="Replied" <?php echo $status=='Replied'?'selected':''; ?>>Replied</option>
                                                        <option value="Resolved" <?php echo $status=='Resolved'?'selected':''; ?>>Resolved</option>
                                                    </select>
                                                    <input type="hidden" name="update_status" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <button class="btn-view" data-bs-toggle="modal" data-bs-target="#detailModal" 
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                                            data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                            data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                                            data-subject="<?php echo htmlspecialchars($row['subject'] ?? 'General'); ?>"
                                                            data-message="<?php echo htmlspecialchars($row['message']); ?>"
                                                            data-date="<?php echo $displayDate . ' ' . $displayTime; ?>"
                                                            data-status="<?php echo $status; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                            data-id="<?php echo $row['id']; ?>" 
                                                            data-name="<?php echo htmlspecialchars($row['name']); ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-inbox" style="font-size:2.5rem; display:block; color:var(--light-gray); margin-bottom:12px;"></i>
                                            <div style="font-weight:600; color:var(--mid-gray);">No messages found</div>
                                            <div style="font-size:0.8rem; color:var(--mid-gray);">All contact form submissions will appear here</div>
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

    <!-- ===== DELETE MODAL ===== -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5><i class="fas fa-trash-alt"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle modal-main-icon"></i>
                    <h4>Delete Message?</h4>
                    <p>Are you sure you want to delete the message from <strong id="deleteName"></strong>?</p>
                    <p class="text-muted" style="font-size:0.75rem;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" class="btn-modal-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash-alt"></i> Yes, Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DETAIL VIEW MODAL ===== -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-custom">
                <div class="modal-header">
                    <h5><i class="fas fa-envelope-open-text"></i> Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-item">
                        <span class="label"><i class="fas fa-user"></i> Name</span>
                        <span class="value" id="detailName">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="value" id="detailEmail">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><i class="fas fa-phone"></i> Phone</span>
                        <span class="value" id="detailPhone">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><i class="fas fa-tag"></i> Subject</span>
                        <span class="value" id="detailSubject">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><i class="fas fa-calendar-alt"></i> Date & Time</span>
                        <span class="value" id="detailDate">-</span>
                    </div>
                    <div class="detail-item">
                        <span class="label"><i class="fas fa-circle"></i> Status</span>
                        <span class="value" id="detailStatus">-</span>
                    </div>
                    <div class="detail-item" style="border-bottom:none;">
                        <span class="label"><i class="fas fa-comment"></i> Message</span>
                        <span class="value" id="detailMessage" style="white-space:pre-wrap;">-</span>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== Toggle Read More =====
        function toggleText(el) {
            let box = el.closest('.message-box');
            let shortText = box.querySelector('.short-text');
            let fullText = box.querySelector('.full-text');

            if (fullText.classList.contains('d-none')) {
                fullText.classList.remove('d-none');
                shortText.classList.add('d-none');
                el.innerText = 'Show Less';
            } else {
                fullText.classList.add('d-none');
                shortText.classList.remove('d-none');
                el.innerText = 'Read More';
            }
        }

        // ===== Delete Modal Handler =====
        const deleteModal = document.getElementById('deleteModal');
        const deleteName = document.getElementById('deleteName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                deleteName.textContent = name;
                confirmDeleteBtn.href = '?delete_id=' + encodeURIComponent(id);
            });
        }

        // ===== Detail Modal Handler =====
        const detailModal = document.getElementById('detailModal');
        
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                document.getElementById('detailName').textContent = button.getAttribute('data-name');
                document.getElementById('detailEmail').textContent = button.getAttribute('data-email');
                document.getElementById('detailPhone').textContent = button.getAttribute('data-phone');
                document.getElementById('detailSubject').textContent = button.getAttribute('data-subject');
                document.getElementById('detailDate').textContent = button.getAttribute('data-date');
                
                const status = button.getAttribute('data-status');
                const statusMap = {
                    'New': 'badge-new',
                    'Called': 'badge-called',
                    'Not Picked': 'badge-notpicked',
                    'Replied': 'badge-replied',
                    'Resolved': 'badge-resolved'
                };
                document.getElementById('detailStatus').innerHTML = `<span class="badge-status ${statusMap[status] || 'badge-new'}">${status}</span>`;
                
                document.getElementById('detailMessage').textContent = button.getAttribute('data-message');
            });
        }

        // ===== Auto-hide alerts =====
        setTimeout(() => {
            document.querySelectorAll('.alert-custom').forEach(el => {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 3000);
    </script>
</body>
</html>