<?php
/**
 * Dashboard Optimization Script
 * Script untuk mengoptimalkan dan membersihkan dashboard
 */

echo "<!DOCTYPE html>";
echo "<html><head><title>Dashboard Optimization</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>";
echo "</head><body class='bg-light p-4'>";

echo "<div class='container'>";
echo "<h1 class='mb-4'><i class='fas fa-tools text-primary'></i> Dashboard Optimization</h1>";

// 1. Create necessary directories
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5><i class='fas fa-folder-plus'></i> Creating Directories</h5></div>";
echo "<div class='card-body'>";

$directories = [
    'assets',
    'assets/css',
    'assets/js',
    'assets/img',
    'assets/img/uploads',
    'config',
    'templates'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<div class='alert alert-success py-2'><i class='fas fa-check'></i> Created: $dir</div>";
        } else {
            echo "<div class='alert alert-danger py-2'><i class='fas fa-times'></i> Failed to create: $dir</div>";
        }
    } else {
        echo "<div class='alert alert-info py-2'><i class='fas fa-info'></i> Already exists: $dir</div>";
    }
}

echo "</div></div>";

// 2. Set proper permissions
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5><i class='fas fa-shield-alt'></i> Setting Permissions</h5></div>";
echo "<div class='card-body'>";

$permission_files = [
    'assets/img/uploads' => 0755,
    'config' => 0755,
    'templates' => 0755
];

foreach ($permission_files as $path => $permission) {
    if (is_dir($path)) {
        if (chmod($path, $permission)) {
            echo "<div class='alert alert-success py-2'><i class='fas fa-check'></i> Set permission $permission for: $path</div>";
        } else {
            echo "<div class='alert alert-warning py-2'><i class='fas fa-exclamation'></i> Could not set permission for: $path</div>";
        }
    }
}

echo "</div></div>";

// 3. Clean up old files
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5><i class='fas fa-trash'></i> Cleaning Up Old Files</h5></div>";
echo "<div class='card-body'>";

$old_files = [
    'dashboard_old.php',
    'presensi_old.php',
    'profil_old.php',
    'style_old.css',
    'script_old.js'
];

$cleaned = 0;
foreach ($old_files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "<div class='alert alert-success py-2'><i class='fas fa-check'></i> Deleted: $file</div>";
            $cleaned++;
        } else {
            echo "<div class='alert alert-warning py-2'><i class='fas fa-exclamation'></i> Could not delete: $file</div>";
        }
    }
}

if ($cleaned === 0) {
    echo "<div class='alert alert-info py-2'><i class='fas fa-info'></i> No old files found to clean</div>";
}

echo "</div></div>";

// 4. Create .htaccess for security
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5><i class='fas fa-lock'></i> Security Configuration</h5></div>";
echo "<div class='card-body'>";

$htaccess_content = "# Dashboard Security Configuration
RewriteEngine On

# Prevent access to sensitive files
<Files ~ \"^(config|templates|test_|optimize_).*\">
    Order allow,deny
    Deny from all
</Files>

# Prevent access to .php files in uploads
<Directory \"assets/img/uploads\">
    <Files \"*.php\">
        Order allow,deny
        Deny from all
    </Files>
</Directory>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Set cache headers
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css \"access plus 1 year\"
    ExpiresByType application/javascript \"access plus 1 year\"
    ExpiresByType image/png \"access plus 1 year\"
    ExpiresByType image/jpg \"access plus 1 year\"
    ExpiresByType image/jpeg \"access plus 1 year\"
</IfModule>";

if (file_put_contents('.htaccess', $htaccess_content)) {
    echo "<div class='alert alert-success py-2'><i class='fas fa-check'></i> Created .htaccess security configuration</div>";
} else {
    echo "<div class='alert alert-warning py-2'><i class='fas fa-exclamation'></i> Could not create .htaccess file</div>";
}

echo "</div></div>";

// 5. Create robots.txt
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5><i class='fas fa-robot'></i> SEO Configuration</h5></div>";
echo "<div class='card-body'>";

$robots_content = "User-agent: *
Disallow: /
# This is a private dashboard system";

if (file_put_contents('robots.txt', $robots_content)) {
    echo "<div class='alert alert-success py-2'><i class='fas fa-check'></i> Created robots.txt</div>";
} else {
    echo "<div class='alert alert-warning py-2'><i class='fas fa-exclamation'></i> Could not create robots.txt</div>";
}

echo "</div></div>";

// 6. Performance check
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5><i class='fas fa-tachometer-alt'></i> Performance Check</h5></div>";
echo "<div class='card-body'>";

$performance_tips = [
    'Enable PHP OPcache' => function_exists('opcache_get_status'),
    'Enable Gzip Compression' => extension_loaded('zlib'),
    'MySQL Extension' => extension_loaded('mysqli'),
    'GD Extension (for images)' => extension_loaded('gd'),
    'Session Support' => function_exists('session_start')
];

foreach ($performance_tips as $tip => $status) {
    $badge_class = $status ? 'success' : 'warning';
    $icon = $status ? 'check' : 'exclamation';
    $text = $status ? 'OK' : 'MISSING';
    
    echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
    echo "<span>$tip</span>";
    echo "<span class='badge bg-$badge_class'><i class='fas fa-$icon'></i> $text</span>";
    echo "</div>";
}

echo "</div></div>";

// 7. Final summary
echo "<div class='card'>";
echo "<div class='card-header'><h5><i class='fas fa-flag-checkered'></i> Optimization Complete</h5></div>";
echo "<div class='card-body'>";

echo "<div class='alert alert-success'>";
echo "<h4><i class='fas fa-check-circle'></i> Dashboard Optimized!</h4>";
echo "<p>Dashboard telah dioptimalkan dan siap digunakan dengan performa terbaik.</p>";
echo "</div>";

echo "<h6>Next Steps:</h6>";
echo "<ol>";
echo "<li>Test dashboard dengan mengakses <a href='test_dashboard.php' class='btn btn-sm btn-outline-primary'>Test Dashboard</a></li>";
echo "<li>Launch dashboard dengan mengakses <a href='index.php' class='btn btn-sm btn-success'>Dashboard</a></li>";
echo "<li>Hapus file test dan optimize ini setelah selesai testing</li>";
echo "</ol>";

echo "<div class='mt-3'>";
echo "<a href='test_dashboard.php' class='btn btn-primary me-2'><i class='fas fa-vial'></i> Run Tests</a>";
echo "<a href='index.php' class='btn btn-success'><i class='fas fa-rocket'></i> Launch Dashboard</a>";
echo "</div>";

echo "</div></div>";

echo "</div>"; // End container
echo "</body></html>";
?>