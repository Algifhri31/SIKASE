<?php
// Include konfigurasi dashboard
include_once 'config/dashboard_config.php';

// Ambil parameter URL
$current_module = isset($_GET['m']) ? $_GET['m'] : 'awal';
$current_submodule = isset($_GET['s']) ? $_GET['s'] : '';

// Generate breadcrumb
$breadcrumb = generateBreadcrumb($current_module, $current_submodule);

// Tentukan menu aktif
$active_menu = getActiveMenu($current_module, $current_submodule);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    
    <!-- CSS Assets -->
    <?php foreach ($dashboard_assets['css'] as $css): ?>
    <link href="<?php echo $css; ?>" rel="stylesheet">
    <?php endforeach; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- Meta Tags -->
    <meta name="description" content="<?php echo APP_DESCRIPTION; ?>">
    <meta name="author" content="<?php echo APP_NAME; ?>">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <i class="fas fa-graduation-cap" style="color: white; font-size: 18px;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-gradient"><?php echo APP_NAME; ?></h5>
                    <small class="text-muted">v<?php echo APP_VERSION; ?></small>
                </div>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <?php foreach ($dashboard_menu as $menu): ?>
            <a href="<?php echo $menu['url']; ?>" class="<?php echo ($active_menu === $menu['id']) ? 'active' : ''; ?>">
                <i class="<?php echo $menu['icon']; ?>"></i><?php echo $menu['title']; ?>
            </a>
            <?php endforeach; ?>
            
            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <a href="logout.php" style="color: #dc3545;" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="mobile-menu-btn me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
                        <?php if (count($breadcrumb) > 1): ?>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="font-size: 12px;">
                                <?php foreach ($breadcrumb as $index => $item): ?>
                                    <?php if ($index === count($breadcrumb) - 1): ?>
                                        <li class="breadcrumb-item active"><?php echo $item['title']; ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo $item['url']; ?>" class="text-decoration-none"><?php echo $item['title']; ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3 d-none d-md-block current-time" style="font-size: 14px;">
                        <?php echo date('d M Y, H:i'); ?>
                    </span>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle text-primary me-2" style="font-size: 24px;"></i>
                        <div class="d-none d-sm-block">
                            <span class="fw-medium"><?php echo htmlspecialchars($_SESSION['namasi'] ?? 'User'); ?></span>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($_SESSION['idsi'] ?? ''); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>