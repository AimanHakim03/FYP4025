<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧩 PHP + ADOdb + SQL Server Connection Test</h2>";

require_once('adodb5/adodb.inc.php');
require_once('setupDB.php');

// --- Use your TRANSACTION DB info
$conn = ADONewConnection($DB_dbtype);

try {
    echo "<p>Connecting to: <b>$DB_hostname</b></p>";
    $conn->Connect($DB_hostname, $DB_username, $DB_password);
    echo "<p>✅ Connection successful to <b>$DB_dbname</b>!</p>";
} catch (Exception $e) {
    echo "<p>❌ Connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// --- Test a simple query (make sure 'setup' table exists)
echo "<h3>📊 Testing SELECT query from setup table</h3>";

$sql = "SELECT TOP 1 * FROM setup";
$rs = $conn->Execute($sql);

if ($rs === false) {
    echo "<p>❌ Query failed: " . $conn->ErrorMsg() . "</p>";
} elseif ($rs->EOF) {
    echo "<p>⚠️ No rows found in 'setup' table.</p>";
} else {
    echo "<p>✅ Data retrieved from 'setup' table!</p>";
    echo "<pre>";
    print_r($rs->fields);
    echo "</pre>";
}

$rs->Close();
$conn->Close();

echo "<hr><p>✨ Test completed, PHP 8 style, period 💅</p>";
?>
