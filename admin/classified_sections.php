<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM classified_sections WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: classified_sections.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [$_POST['name'], (int)$_POST['sort_order'], isset($_POST['is_active']) ? 1 : 0];

    if ($id) {
        $db->prepare("UPDATE classified_sections SET name=?, sort_order=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    } else {
        $db->prepare("INSERT INTO classified_sections (name, sort_order, is_active) VALUES (?,?,?)")->execute($data);
    }
    header('Location: classified_sections.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM classified_sections WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM classified_sections ORDER BY sort_order")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Classified Sections';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Section</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= e($editItem['name'] ?? '') ?>" required></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? '0') ?>"></div>
        </div>
        <div class="form-group"><label class="form-check"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($editItem): ?><a href="classified_sections.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['name']) ?></td>
                <td><?= $item['sort_order'] ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><div class="btn-group"><a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a><a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
