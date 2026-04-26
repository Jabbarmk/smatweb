<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM classifieds WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: classifieds.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $image = null;
    if (!empty($_FILES['image']['name'])) $image = uploadImage($_FILES['image'], 'classifieds');

    $fields = ['title', 'description', 'price', 'currency', 'category_id', 'section_id', 'location',
               'age', 'model', 'warranty', 'color', 'brand', 'condition_status', 'version',
               'storage', 'memory', 'battery_health', 'accompaniments', 'carrier_lock'];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = $_POST[$f] ?? '';
    }
    $data['price'] = (float)$data['price'];
    $data['category_id'] = $data['category_id'] ?: null;
    $data['section_id'] = $data['section_id'] ?: null;
    $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $sets = [];
        $params = [];
        foreach ($data as $k => $v) {
            $sets[] = "$k = ?";
            $params[] = $v;
        }
        if ($image) { $sets[] = "image = ?"; $params[] = $image; }
        $params[] = $id;
        $db->prepare("UPDATE classifieds SET " . implode(', ', $sets) . " WHERE id = ?")->execute($params);
    } else {
        $data['image'] = $image ?? '';
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $db->prepare("INSERT INTO classifieds ($cols) VALUES ($placeholders)")->execute(array_values($data));
    }
    header('Location: classifieds.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM classifieds WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$categories = $db->query("SELECT * FROM classified_categories ORDER BY name")->fetchAll();
$sections = $db->query("SELECT * FROM classified_sections ORDER BY name")->fetchAll();
$items = $db->query("SELECT c.*, cc.name as cat_name, cs.name as sec_name FROM classifieds c LEFT JOIN classified_categories cc ON c.category_id = cc.id LEFT JOIN classified_sections cs ON c.section_id = cs.id ORDER BY c.created_at DESC")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Manage Classifieds';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Classified Item</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Title</label><input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>" required></div>
            <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" value="<?= e($editItem['price'] ?? '') ?>" required></div>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description"><?= e($editItem['description'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Currency</label><input type="text" name="currency" value="<?= e($editItem['currency'] ?? 'AED') ?>"></div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
                    <option value="">None</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($editItem['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Section</label>
                <select name="section_id">
                    <option value="">None</option>
                    <?php foreach ($sections as $sec): ?>
                    <option value="<?= $sec['id'] ?>" <?= ($editItem['section_id'] ?? '') == $sec['id'] ? 'selected' : '' ?>><?= e($sec['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Location</label><input type="text" name="location" value="<?= e($editItem['location'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Image</label><input type="file" name="image" accept="image/*"></div>
            <div class="form-group"><label>Brand</label><input type="text" name="brand" value="<?= e($editItem['brand'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Age</label><input type="text" name="age" value="<?= e($editItem['age'] ?? '') ?>"></div>
            <div class="form-group"><label>Model</label><input type="text" name="model" value="<?= e($editItem['model'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Warranty</label><input type="text" name="warranty" value="<?= e($editItem['warranty'] ?? '') ?>"></div>
            <div class="form-group"><label>Color</label><input type="text" name="color" value="<?= e($editItem['color'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Condition</label><input type="text" name="condition_status" value="<?= e($editItem['condition_status'] ?? '') ?>"></div>
            <div class="form-group"><label>Version</label><input type="text" name="version" value="<?= e($editItem['version'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Storage</label><input type="text" name="storage" value="<?= e($editItem['storage'] ?? '') ?>"></div>
            <div class="form-group"><label>Memory</label><input type="text" name="memory" value="<?= e($editItem['memory'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Battery Health</label><input type="text" name="battery_health" value="<?= e($editItem['battery_health'] ?? '') ?>"></div>
            <div class="form-group"><label>Accompaniments</label><input type="text" name="accompaniments" value="<?= e($editItem['accompaniments'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Carrier Lock</label><input type="text" name="carrier_lock" value="<?= e($editItem['carrier_lock'] ?? '') ?>"></div>
            <div class="form-group"><label class="form-check" style="margin-top:28px;"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($editItem): ?><a href="classifieds.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Image</th><th>Title</th><th>Price</th><th>Category</th><th>Section</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= getImageUrl($item['image'], 'classifieds') ?>" alt=""></td>
                <td><?= e($item['title']) ?></td>
                <td><?= e($item['currency']) ?> <?= number_format($item['price']) ?></td>
                <td><?= e($item['cat_name'] ?? '-') ?></td>
                <td><?= e($item['sec_name'] ?? '-') ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><div class="btn-group"><a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a><a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
