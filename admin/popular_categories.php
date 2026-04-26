<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM popular_categories WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: popular_categories.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = $_POST['name'];
    $link = $_POST['link'] ?? '';
    $sort = (int)$_POST['sort_order'];
    $active = isset($_POST['is_active']) ? 1 : 0;
    $image = null;
    if (!empty($_FILES['image']['name'])) $image = uploadImage($_FILES['image'], 'categories');

    if ($id) {
        $sql = "UPDATE popular_categories SET name=?, link=?, sort_order=?, is_active=?";
        $params = [$name, $link, $sort, $active];
        if ($image) { $sql .= ", image=?"; $params[] = $image; }
        $sql .= " WHERE id=?"; $params[] = $id;
        $db->prepare($sql)->execute($params);
    } else {
        $db->prepare("INSERT INTO popular_categories (name, image, link, sort_order, is_active) VALUES (?,?,?,?,?)")
           ->execute([$name, $image ?? '', $link, $sort, $active]);
    }
    header('Location: popular_categories.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM popular_categories WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM popular_categories ORDER BY sort_order")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Popular Categories';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Popular Category</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= e($editItem['name'] ?? '') ?>" required></div>
            <div class="form-group"><label>Link</label><input type="text" name="link" value="<?= e($editItem['link'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Image</label><input type="file" name="image" accept="image/*" <?= $editItem ? '' : 'required' ?>></div>
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? '0') ?>"></div>
        </div>
        <div class="form-group"><label class="form-check"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($editItem): ?><a href="popular_categories.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Image</th><th>Name</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= getImageUrl($item['image'], 'categories') ?>" alt=""></td>
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
