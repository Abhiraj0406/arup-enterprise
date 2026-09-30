<?php
// admin/announcements.php - Manage Announcement Strips
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db.php";

date_default_timezone_set('Asia/Kolkata');

$page_title = "Announcements";
$page_icon = "bullhorn";

// ================= FETCH ANNOUNCEMENTS =================
$announcements = [];
$result = $conn->query("SELECT * FROM announcement_strips ORDER BY sort_order ASC, id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// ================= ADD ANNOUNCEMENT =================
if (isset($_POST['add_announcement'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $link_url = trim($_POST['link_url']);
    $link_text = trim($_POST['link_text']);
    $bg_color = trim($_POST['bg_color']);
    $text_color = trim($_POST['text_color']);
    $icon = trim($_POST['icon']);
    $sort_order = intval($_POST['sort_order']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO announcement_strips (title, message, link_url, link_text, bg_color, text_color, icon, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssis", $title, $message, $link_url, $link_text, $bg_color, $text_color, $icon, $sort_order, $status);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement added successfully!";
        header("Location: announcements.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// ================= UPDATE ANNOUNCEMENT =================
if (isset($_POST['edit_announcement'])) {
    $id = intval($_POST['edit_id']);
    $title = trim($_POST['edit_title']);
    $message = trim($_POST['edit_message']);
    $link_url = trim($_POST['edit_link_url']);
    $link_text = trim($_POST['edit_link_text']);
    $bg_color = trim($_POST['edit_bg_color']);
    $text_color = trim($_POST['edit_text_color']);
    $icon = trim($_POST['edit_icon']);
    $sort_order = intval($_POST['edit_sort_order']);
    $status = $_POST['edit_status'];
    
    $stmt = $conn->prepare("UPDATE announcement_strips SET title = ?, message = ?, link_url = ?, link_text = ?, bg_color = ?, text_color = ?, icon = ?, sort_order = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssssssssi", $title, $message, $link_url, $link_text, $bg_color, $text_color, $icon, $sort_order, $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement updated successfully!";
        header("Location: announcements.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
}

// ================= TOGGLE STATUS =================
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $stmt = $conn->prepare("UPDATE announcement_strips SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement status updated!";
        header("Location: announcements.php");
        exit();
    }
}

// ================= DELETE ANNOUNCEMENT =================
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM announcement_strips WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Announcement deleted successfully!";
        header("Location: announcements.php");
        exit();
    }
}

// ================= ICONS LIST =================
$icons = [
    'fa-bullhorn', 'fa-fire', 'fa-star', 'fa-gem', 'fa-crown',
    'fa-gift', 'fa-tag', 'fa-percent', 'fa-clock', 'fa-bolt',
    'fa-rocket', 'fa-megaphone', 'fa-bell', 'fa-info-circle',
    'fa-check-circle', 'fa-exclamation-circle', 'fa-warning'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
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
            border: 1px solid rgba(220,38,38,0.12);
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
            box-shadow: 0 6px 20px rgba(220,38,38,0.3);
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
            border: 1px solid rgba(220,38,38,0.15);
        }
        .alert-custom i { color: var(--gold); margin-right: 8px; }

        .card-premium {
            background: var(--white);
            border: 1px solid rgba(220,38,38,0.1);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-premium .card-head {
            padding: 10px 16px;
            background: linear-gradient(135deg, var(--cream), var(--ivory));
            border-bottom: 1px solid rgba(220,38,38,0.08);
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
            border-bottom: 2px solid rgba(220,38,38,0.12);
            background: var(--cream);
        }
        .table tbody td {
            padding: 8px 12px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(220,38,38,0.05);
        }
        .table tbody tr:hover { background: rgba(220,38,38,0.03); }

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
            border-top: 1px solid rgba(220,38,38,0.08);
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
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }

        .color-picker-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .color-picker-wrapper input[type="color"] {
            width: 40px;
            height: 40px;
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            padding: 2px;
            cursor: pointer;
        }

        .icon-picker-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 6px;
            padding: 8px;
            background: var(--cream);
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            max-height: 80px;
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
            background: rgba(220,38,38,0.08);
            color: var(--gold);
        }
        .icon-picker-grid .icon-option.selected {
            border-color: var(--gold);
            background: var(--gold);
            color: white;
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

        .announcement-preview {
            padding: 10px 16px;
            border-radius: 8px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .content {
            padding: 16px 18px 30px;
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
                            <i class="fas fa-bullhorn"></i>
                            <span>Announcement Strips</span>
                        </div>
                        <div class="subtitle">Manage premium announcement/offer strips shown on header</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                            <i class="fas fa-plus me-1"></i> Add Announcement
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
                    <h6><i class="fas fa-list-ul"></i> All Announcements</h6>
                    <span class="badge-count"><?php echo count($announcements); ?> Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th style="width:50px;">Icon</th>
                                    <th>Title / Message</th>
                                    <th style="width:120px;">Link</th>
                                    <th style="width:60px;">Sort</th>
                                    <th style="width:80px;">Status</th>
                                    <th style="width:120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($announcements)): ?>
                                    <?php foreach ($announcements as $ann): ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?php echo $ann['id']; ?></td>
                                            <td>
                                                <span style="font-size:1.1rem;color:var(--gold);">
                                                    <i class="fas <?php echo htmlspecialchars($ann['icon'] ?? 'fa-bullhorn'); ?>"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($ann['title']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($ann['message'], 0, 60)); ?></small>
                                                <div style="display:flex;gap:6px;margin-top:3px;">
                                                    <span style="display:inline-block;width:20px;height:20px;border-radius:4px;border:1px solid #ddd;background:<?php echo $ann['bg_color']; ?>;"></span>
                                                    <span style="display:inline-block;width:20px;height:20px;border-radius:4px;border:1px solid #ddd;background:<?php echo $ann['text_color']; ?>;"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($ann['link_url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($ann['link_url']); ?>" target="_blank" style="font-size:0.7rem;">
                                                        <?php echo htmlspecialchars($ann['link_text'] ?? 'Link'); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size:0.65rem;">No link</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $ann['sort_order']; ?></td>
                                            <td>
                                                <a href="?toggle_status=<?php echo $ann['id']; ?>" 
                                                   class="badge-status <?php echo $ann['status'] == 'active' ? 'badge-active' : 'badge-inactive'; ?>"
                                                   onclick="return confirm('Toggle status for <?php echo htmlspecialchars($ann['title']); ?>?')">
                                                    <?php echo ucfirst($ann['status']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-outline-gold" 
                                                            onclick="editAnnouncement(<?php echo $ann['id']; ?>, '<?php echo htmlspecialchars($ann['title']); ?>', '<?php echo htmlspecialchars($ann['message']); ?>', '<?php echo htmlspecialchars($ann['link_url'] ?? ''); ?>', '<?php echo htmlspecialchars($ann['link_text'] ?? ''); ?>', '<?php echo $ann['bg_color']; ?>', '<?php echo $ann['text_color']; ?>', '<?php echo $ann['icon'] ?? 'fa-bullhorn'; ?>', <?php echo $ann['sort_order']; ?>, '<?php echo $ann['status']; ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?delete_id=<?php echo $ann['id']; ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Delete announcement \'<?php echo htmlspecialchars($ann['title']); ?>\'?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-bullhorn" style="font-size:2rem;display:block;color:var(--light-gray);margin-bottom:8px;"></i>
                                            <div style="font-weight:600;color:var(--mid-gray);font-size:0.9rem;">No announcements found</div>
                                            <div style="font-size:0.75rem;color:var(--mid-gray);">Click "Add Announcement" to create your first strip</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card-premium mt-3">
                <div class="card-head">
                    <h6><i class="fas fa-eye"></i> Live Preview (Frontend)</h6>
                </div>
                <div class="card-body">
                    <?php
                    $preview = $conn->query("SELECT * FROM announcement_strips WHERE status='active' ORDER BY sort_order ASC LIMIT 1");
                    if ($preview && $preview->num_rows > 0) {
                        $p = $preview->fetch_assoc();
                        ?>
                        <div class="announcement-preview" style="background:<?php echo $p['bg_color']; ?>;color:<?php echo $p['text_color']; ?>;">
                            <div>
                                <i class="fas <?php echo $p['icon']; ?>"></i>
                                <strong><?php echo htmlspecialchars($p['title']); ?></strong>
                                <span><?php echo htmlspecialchars($p['message']); ?></span>
                            </div>
                            <?php if (!empty($p['link_url'])): ?>
                                <a href="<?php echo htmlspecialchars($p['link_url']); ?>" style="color:<?php echo $p['text_color']; ?>;text-decoration:underline;font-weight:700;">
                                    <?php echo htmlspecialchars($p['link_text'] ?? 'Learn More'); ?> →
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php
                    } else {
                        echo '<div class="text-muted" style="font-size:0.85rem;"><i class="fas fa-info-circle"></i> No active announcements to preview.</div>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <!-- ===== ADD ANNOUNCEMENT MODAL ===== -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-custom">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Add New Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="e.g., Summer Sale">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Icon</label>
                                <div class="row g-2">
                                    <div class="col-9">
                                        <input type="text" name="icon" id="addIconInput" class="form-control" placeholder="fa-bullhorn" value="fa-bullhorn">
                                    </div>
                                    <div class="col-3">
                                        <div id="addIconPreview" class="icon-preview-box"><i class="fas fa-bullhorn"></i></div>
                                    </div>
                                </div>
                                <div class="icon-picker-grid">
                                    <?php foreach($icons as $ic): ?>
                                    <span class="icon-option <?php echo $ic == 'fa-bullhorn' ? 'selected' : ''; ?>" 
                                          data-icon="<?php echo $ic; ?>" 
                                          onclick="selectIcon('add', '<?php echo $ic; ?>')">
                                        <i class="fas <?php echo $ic; ?>"></i>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="2" required placeholder="Your announcement message here..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Link URL</label>
                                <input type="text" name="link_url" class="form-control" placeholder="e.g., products.php or https://example.com">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Link Text</label>
                                <input type="text" name="link_text" class="form-control" placeholder="e.g., Shop Now">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Background Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="bg_color" id="addBgColor" value="#C9920A">
                                    <input type="text" class="form-control" id="addBgColorText" value="#C9920A" style="width:100px;">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Text Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="text_color" id="addTextColor" value="#FFFFFF">
                                    <input type="text" class="form-control" id="addTextColorText" value="#FFFFFF" style="width:100px;">
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-2 mb-2">
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
                        <button type="submit" name="add_announcement" class="btn btn-gold" style="font-size:0.8rem;padding:6px 16px;">
                            <i class="fas fa-save me-1"></i> Add Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== EDIT ANNOUNCEMENT MODAL ===== -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-content-custom">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i> Edit Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="editId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="edit_title" id="editTitle" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Icon</label>
                                <div class="row g-2">
                                    <div class="col-9">
                                        <input type="text" name="edit_icon" id="editIconInput" class="form-control" placeholder="fa-bullhorn">
                                    </div>
                                    <div class="col-3">
                                        <div id="editIconPreview" class="icon-preview-box"><i class="fas fa-bullhorn"></i></div>
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
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="edit_message" id="editMessage" class="form-control" rows="2" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Link URL</label>
                                <input type="text" name="edit_link_url" id="editLinkUrl" class="form-control" placeholder="e.g., products.php">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Link Text</label>
                                <input type="text" name="edit_link_text" id="editLinkText" class="form-control" placeholder="e.g., Shop Now">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Background Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="edit_bg_color" id="editBgColor" value="#C9920A">
                                    <input type="text" class="form-control" id="editBgColorText" value="#C9920A" style="width:100px;">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Text Color</label>
                                <div class="color-picker-wrapper">
                                    <input type="color" name="edit_text_color" id="editTextColor" value="#FFFFFF">
                                    <input type="text" class="form-control" id="editTextColorText" value="#FFFFFF" style="width:100px;">
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="edit_sort_order" id="editSortOrder" class="form-control" min="0">
                            </div>
                            <div class="col-md-2 mb-2">
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
                        <button type="submit" name="edit_announcement" class="btn btn-gold" style="font-size:0.8rem;padding:6px 16px;">
                            <i class="fas fa-save me-1"></i> Update Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== ICON PICKER =====
        function selectIcon(type, icon) {
            if (type === 'add') {
                document.getElementById('addIconInput').value = icon;
                document.getElementById('addIconPreview').innerHTML = '<i class="fas ' + icon + '"></i>';
                document.querySelectorAll('#addAnnouncementModal .icon-option').forEach(el => {
                    el.classList.toggle('selected', el.dataset.icon === icon);
                });
            } else {
                document.getElementById('editIconInput').value = icon;
                document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + icon + '"></i>';
                document.querySelectorAll('#editAnnouncementModal .icon-option').forEach(el => {
                    el.classList.toggle('selected', el.dataset.icon === icon);
                });
            }
        }

        // ===== COLOR PICKER SYNC =====
        document.getElementById('addBgColor').addEventListener('input', function() {
            document.getElementById('addBgColorText').value = this.value;
        });
        document.getElementById('addBgColorText').addEventListener('input', function() {
            document.getElementById('addBgColor').value = this.value;
        });
        document.getElementById('addTextColor').addEventListener('input', function() {
            document.getElementById('addTextColorText').value = this.value;
        });
        document.getElementById('addTextColorText').addEventListener('input', function() {
            document.getElementById('addTextColor').value = this.value;
        });

        document.getElementById('editBgColor').addEventListener('input', function() {
            document.getElementById('editBgColorText').value = this.value;
        });
        document.getElementById('editBgColorText').addEventListener('input', function() {
            document.getElementById('editBgColor').value = this.value;
        });
        document.getElementById('editTextColor').addEventListener('input', function() {
            document.getElementById('editTextColorText').value = this.value;
        });
        document.getElementById('editTextColorText').addEventListener('input', function() {
            document.getElementById('editTextColor').value = this.value;
        });

        // ===== EDIT ANNOUNCEMENT =====
        function editAnnouncement(id, title, message, link_url, link_text, bg_color, text_color, icon, sort_order, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editMessage').value = message;
            document.getElementById('editLinkUrl').value = link_url || '';
            document.getElementById('editLinkText').value = link_text || '';
            document.getElementById('editBgColor').value = bg_color || '#C9920A';
            document.getElementById('editBgColorText').value = bg_color || '#C9920A';
            document.getElementById('editTextColor').value = text_color || '#FFFFFF';
            document.getElementById('editTextColorText').value = text_color || '#FFFFFF';
            document.getElementById('editIconInput').value = icon || 'fa-bullhorn';
            document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + (icon || 'fa-bullhorn') + '"></i>';
            document.getElementById('editSortOrder').value = sort_order || 0;
            document.getElementById('editStatus').value = status || 'active';
            
            document.querySelectorAll('#editAnnouncementModal .icon-option').forEach(el => {
                el.classList.toggle('selected', el.dataset.icon === (icon || 'fa-bullhorn'));
            });
            
            new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
        }

        // ===== LIVE ICON PREVIEW =====
        document.getElementById('addIconInput').addEventListener('input', function() {
            document.getElementById('addIconPreview').innerHTML = '<i class="fas ' + this.value + '"></i>';
        });
        document.getElementById('editIconInput').addEventListener('input', function() {
            document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + this.value + '"></i>';
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