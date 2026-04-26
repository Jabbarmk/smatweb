<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM pages WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: pages.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [$_POST['title'], $_POST['slug'], $_POST['content'], $_POST['meta_description'] ?? '', isset($_POST['is_active']) ? 1 : 0];

    if ($id) {
        $db->prepare("UPDATE pages SET title=?, slug=?, content=?, meta_description=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    } else {
        $db->prepare("INSERT INTO pages (title, slug, content, meta_description, is_active) VALUES (?,?,?,?,?)")->execute($data);
    }
    header('Location: pages.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM pages ORDER BY title")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Manage Pages';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Page</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Title</label><input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>" required></div>
            <div class="form-group"><label>Slug</label><input type="text" name="slug" value="<?= e($editItem['slug'] ?? '') ?>" required></div>
        </div>
        <div class="form-group"><label>Content</label><textarea name="content" rows="8"><?= e($editItem['content'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Meta Description</label><input type="text" name="meta_description" value="<?= e($editItem['meta_description'] ?? '') ?>"></div>
        <div class="form-group"><label class="form-check"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($editItem): ?><a href="pages.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Title</th><th>Slug</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['title']) ?></td>
                <td><?= e($item['slug']) ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><?= date('d M Y', strtotime($item['updated_at'])) ?></td>
                <td><div class="btn-group"><a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a><a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
