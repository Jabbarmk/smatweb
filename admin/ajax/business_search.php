<?php
require_once __DIR__ . '/../../includes/config.php';
if (!isset($_SESSION['admin_id'])) { http_response_code(403); exit; }

$db         = getDB();
$q          = trim($_GET['q'] ?? '');
$categoryId = (int)($_GET['category_id'] ?? 0);

$where  = ['b.is_active = 1'];
$params = [];

if ($q !== '') {
    $where[]  = '(b.name LIKE ? OR b.address LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($categoryId) {
    $where[]  = 'b.category_id = ?';
    $params[] = $categoryId;
}

$sql = "SELECT b.id, b.name, b.emirate, bc.name AS category_name
        FROM businesses b
        LEFT JOIN business_categories bc ON b.category_id = bc.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY b.name ASC
        LIMIT 100";

$stmt = $db->prepare($sql);
$stmt->execute($params);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll());
