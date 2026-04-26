<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

// Delete
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: sliders.php?msg=deleted');
    exit;
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'title' => $_POST['title'] ?? '',
        'subtitle' => $_POST['subtitle'] ?? '',
        'button_text' => $_POST['button_text'] ?? '',
        'button_link' => $_POST['button_link'] ?? '',
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = uploadImage($_FILES['image'], 'slides');
    }

    if ($id) {
        $sql = "UPDATE sliders SET title=?, subtitle=?, button_text=?, button_link=?, sort_order=?, is_active=?";
        $params = array_values($data);
        if ($image) { $sql .= ", image=?"; $params[] = $image; }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $db->prepare($sql)->execute($params);
    } else {
        $data['image'] = $image ?? '';
        $db->prepare("INSERT INTO sliders (title, subtitle, button_text, button_link, sort_order, is_active, image) VALUES (?,?,?,?,?,?,?)")
           ->execute(array_values($data));
    }
    header('Location: sliders.php?msg=saved');
    exit;
}

// Edit mode
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM sliders WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM sliders ORDER BY sort_order")->fetchAll();

if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved successfully!' : 'Deleted successfully!';

$pageTitle = 'Manage Sliders';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit Slider' : 'Add New Slider' ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" value="<?= e($editItem['subtitle'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Button Text</label>
                <input type="text" name="button_text" value="<?= e($editItem['button_text'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Button Link</label>
                <input type="text" name="button_link" value="<?= e($editItem['button_link'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Image <?= $editItem ? '(leave empty to keep current)' : '' ?></label>
                <input type="file" name="image" accept="image/*" <?= $editItem ? '' : 'required' ?>>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? '0') ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Save Slider</button>
        <?php if ($editItem): ?><a href="sliders.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <h2>All Sliders (<?= count($items) ?>)</h2>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Subtitle</th>
                <th>Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= getImageUrl($item['image'], 'slides') ?>" alt=""></td>
                <td><?= e($item['title']) ?></td>
                <td><?= e($item['subtitle']) ?></td>
                <td><?= $item['sort_order'] ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                    <div class="btn-group">
                        <a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a>
                        <a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
