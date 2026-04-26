<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM business_categories WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: business_categories.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $main_category_id = (int)($_POST['main_category_id'] ?? 0) ?: null;
    $data = [$_POST['name'], $main_category_id, $_POST['icon'], $_POST['group_name'], (int)$_POST['sort_order'], isset($_POST['is_active']) ? 1 : 0];

    if ($id) {
        $db->prepare("UPDATE business_categories SET name=?, main_category_id=?, icon=?, group_name=?, sort_order=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    } else {
        $db->prepare("INSERT INTO business_categories (name, main_category_id, icon, group_name, sort_order, is_active) VALUES (?,?,?,?,?,?)")->execute($data);
    }
    header('Location: business_categories.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM business_categories WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$mainCategories = $db->query("SELECT id, name FROM main_categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
$items = $db->query("SELECT bc.*, mc.name AS main_category_name FROM business_categories bc LEFT JOIN main_categories mc ON bc.main_category_id = mc.id ORDER BY bc.sort_order")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Business Categories';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Business Category</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= e($editItem['name'] ?? '') ?>" required></div>
            <div class="form-group"><label>Icon (Emoji)</label><input type="text" name="icon" value="<?= e($editItem['icon'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Main Category</label>
                <select name="main_category_id">
                    <option value="">-- None --</option>
                    <?php foreach ($mainCategories as $mc): ?>
                    <option value="<?= $mc['id'] ?>" <?= ($editItem['main_category_id'] ?? '') == $mc['id'] ? 'selected' : '' ?>><?= e($mc['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Group Name</label><input type="text" name="group_name" value="<?= e($editItem['group_name'] ?? '') ?>" placeholder="e.g. Popular Categories, Health and Fitness"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? '0') ?>"></div>
            <div class="form-group"><label class="form-check" style="margin-top:28px;"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($editItem): ?><a href="business_categories.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Icon</th><th>Name</th><th>Main Category</th><th>Group</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td style="font-size:24px;"><?= $item['icon'] ?></td>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['main_category_name'] ?? '—') ?></td>
                <td><?= e($item['group_name'] ?? '—') ?></td>
                <td><?= $item['sort_order'] ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><div class="btn-group"><a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a><a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
