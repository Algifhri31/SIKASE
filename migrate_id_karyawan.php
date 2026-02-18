<?php
/**
 * Migration helper: convert id_karyawan columns to VARCHAR(50) across DB.
 * Usage:
 *  - Open this file in browser to preview SQL changes.
 *  - To execute, append ?run=1 to the URL.
 *
 * WARNING: Always backup your database before running this.
 */
require_once 'koneksi.php';

echo "<h2>Migrate id_karyawan -> VARCHAR(50)</h2>";

$db = mysqli_real_escape_string($koneksi, mysqli_fetch_row(mysqli_query($koneksi, "SELECT DATABASE()"))[0]);
echo "<p>Database: <strong>" . htmlspecialchars($db) . "</strong></p>";

// Find all tables that have column named id_karyawan
$cols = mysqli_query($koneksi, "SELECT TABLE_NAME, COLUMN_NAME, IS_NULLABLE, COLUMN_TYPE, COLUMN_DEFAULT
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME = 'id_karyawan'");

$tables = [];
while ($c = mysqli_fetch_assoc($cols)) {
    $tables[] = $c;
}

if (empty($tables)) {
    echo "<p>No columns named <code>id_karyawan</code> found in this database.</p>";
    exit;
}

echo "<h3>Found columns</h3><pre>";
foreach ($tables as $t) {
    echo "Table: " . $t['TABLE_NAME'] . " | Type: " . $t['COLUMN_TYPE'] . " | Nullable: " . $t['IS_NULLABLE'] . "\n";
}
echo "</pre>";

// Find foreign keys that reference tb_karyawan(id_karyawan)
$fks_q = "SELECT kcu.CONSTRAINT_NAME, kcu.TABLE_NAME, kcu.COLUMN_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME,
    rc.UPDATE_RULE, rc.DELETE_RULE
    FROM information_schema.KEY_COLUMN_USAGE kcu
    LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
      ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
      AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
    WHERE kcu.REFERENCED_TABLE_NAME = 'tb_karyawan'
      AND kcu.REFERENCED_COLUMN_NAME = 'id_karyawan'
      AND kcu.CONSTRAINT_SCHEMA = DATABASE()";

$fks_res = mysqli_query($koneksi, $fks_q);
$fks = [];
while ($r = mysqli_fetch_assoc($fks_res)) {
    $fks[] = $r;
}

if (!empty($fks)) {
    echo "<h3>Foreign keys referencing tb_karyawan(id_karyawan)</h3><pre>";
    foreach ($fks as $fk) {
        echo "Constraint: " . $fk['CONSTRAINT_NAME'] . " | Table: " . $fk['TABLE_NAME'] . " | Column: " . $fk['COLUMN_NAME'] . " | ON UPDATE: " . $fk['UPDATE_RULE'] . " | ON DELETE: " . $fk['DELETE_RULE'] . "\n";
    }
    echo "</pre>";
} else {
    echo "<p>No foreign keys referencing tb_karyawan(id_karyawan) found.</p>";
}

// Build actions: for safety, first alter tb_karyawan, then other tables
$actions = [];

// tb_karyawan first
$actions[] = [
    'sql' => "ALTER TABLE `tb_karyawan` MODIFY `id_karyawan` VARCHAR(50) NOT NULL",
    'desc' => 'Modify tb_karyawan.id_karyawan to VARCHAR(50) NOT NULL'
];

// For each table other than tb_karyawan having the column, modify column type
foreach ($tables as $t) {
    $tbl = $t['TABLE_NAME'];
    if ($tbl === 'tb_karyawan') continue;
    $nullable = ($t['IS_NULLABLE'] === 'YES') ? 'NULL' : 'NOT NULL';
    // Keep default as is if present (skip default handling for simplicity)
    $actions[] = [
        'sql' => "ALTER TABLE `{$tbl}` MODIFY `id_karyawan` VARCHAR(50) {$nullable}",
        'desc' => "Modify {$tbl}.id_karyawan to VARCHAR(50) {$nullable}"
    ];
}

// If there are foreign keys, we need to drop and recreate them. Add drop/recreate steps.
foreach ($fks as $fk) {
    $constraint = $fk['CONSTRAINT_NAME'];
    $tbl = $fk['TABLE_NAME'];
    $col = $fk['COLUMN_NAME'];
    $on_update = $fk['UPDATE_RULE'] ?: 'NO ACTION';
    $on_delete = $fk['DELETE_RULE'] ?: 'NO ACTION';

    // Drop
    $actions[] = [
        'sql' => "ALTER TABLE `{$tbl}` DROP FOREIGN KEY `{$constraint}`",
        'desc' => "Drop foreign key {$constraint} on {$tbl}({$col})"
    ];
}

// Recreate foreign keys after modifications
foreach ($fks as $fk) {
    $constraint = $fk['CONSTRAINT_NAME'];
    $tbl = $fk['TABLE_NAME'];
    $col = $fk['COLUMN_NAME'];
    $on_update = $fk['UPDATE_RULE'] ?: 'NO ACTION';
    $on_delete = $fk['DELETE_RULE'] ?: 'NO ACTION';
    $actions[] = [
        'sql' => "ALTER TABLE `{$tbl}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$col}`) REFERENCES `tb_karyawan`(`id_karyawan`) ON UPDATE {$on_update} ON DELETE {$on_delete}",
        'desc' => "Recreate foreign key {$constraint} on {$tbl}({$col}) referencing tb_karyawan(id_karyawan)"
    ];
}

// Print planned actions
echo "<h3>Planned SQL actions</h3><ol>";
foreach ($actions as $a) {
    echo "<li><strong>" . htmlspecialchars($a['desc']) . "</strong><pre>" . htmlspecialchars($a['sql']) . "</pre></li>";
}
echo "</ol>";

// Safety note
echo "<div style='background:#fff3cd;padding:12px;border-radius:6px;'>";
echo "<strong>IMPORTANT:</strong> Backup your database before executing. This script will attempt to drop and recreate foreign keys. If you have custom constraint names or special rules, verify the above SQL carefully.<br>";
echo "To execute, append <code>?run=1</code> to the URL.";
echo "</div>";

if (isset($_GET['run']) && $_GET['run'] == '1') {
    echo "<h3>Executing...</h3>";
    foreach ($actions as $a) {
        echo "<p>Running: <code>" . htmlspecialchars($a['sql']) . "</code></p>";
        $res = mysqli_query($koneksi, $a['sql']);
        if ($res) {
            echo "<p style='color:green;'>SUCCESS</p>";
        } else {
            echo "<p style='color:red;'>FAILED: " . htmlspecialchars(mysqli_error($koneksi)) . "</p>";
            echo "<p>Stopping execution to avoid partial changes.</p>";
            exit;
        }
    }
    echo "<h3 style='color:green;'>All actions executed. Verify your application now.</h3>";
} else {
    echo "<p style='margin-top:12px;'>Preview only. No changes made.</p>";
}

?>