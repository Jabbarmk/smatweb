<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM jobs WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: jobs.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        $_POST['title'], $_POST['company'], (float)$_POST['salary_min'], (float)$_POST['salary_max'],
        $_POST['currency'] ?: 'AED', $_POST['location'], $_POST['job_type'],
        $_POST['description'], $_POST['requirements'], $_POST['benefits'],
        isset($_POST['is_featured']) ? 1 : 0, isset($_POST['is_active']) ? 1 : 0
    ];

    if ($id) {
        $db->prepare("UPDATE jobs SET title=?, company=?, salary_min=?, salary_max=?, currency=?, location=?, job_type=?, description=?, requirements=?, benefits=?, is_featured=?, is_active=? WHERE id=?")->execute([...$data, $id]);
    } else {
        $db->prepare("INSERT INTO jobs (title, company, salary_min, salary_max, currency, location, job_type, description, requirements, benefits, is_featured, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
    }
    header('Location: jobs.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM jobs WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM jobs ORDER BY posted_at DESC")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Manage Jobs';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Job</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Job Title</label><input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>" required></div>
            <div class="form-group"><label>Company</label><input type="text" name="company" value="<?= e($editItem['company'] ?? '') ?>" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Salary Min</label><input type="number" step="0.01" name="salary_min" value="<?= e($editItem['salary_min'] ?? '') ?>"></div>
            <div class="form-group"><label>Salary Max</label><input type="number" step="0.01" name="salary_max" value="<?= e($editItem['salary_max'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Currency</label><input type="text" name="currency" value="<?= e($editItem['currency'] ?? 'AED') ?>"></div>
            <div class="form-group"><label>Location</label><input type="text" name="location" value="<?= e($editItem['location'] ?? '') ?>"></div>
        </div>
        <div class="form-group">
            <label>Job Type</label>
            <select name="job_type">
                <?php foreach (['Fulltime', 'Part Time', 'Contract', 'Freelance'] as $type): ?>
                <option value="<?= $type ?>" <?= ($editItem['job_type'] ?? '') === $type ? 'selected' : '' ?>><?= $type ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description"><?= e($editItem['description'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Requirements (one per line)</label><textarea name="requirements"><?= e($editItem['requirements'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Benefits (one per line)</label><textarea name="benefits"><?= e($editItem['benefits'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label class="form-check"><input type="checkbox" name="is_featured" <?= ($editItem['is_featured'] ?? 0) ? 'checked' : '' ?>> Featured</label></div>
            <div class="form-group"><label class="form-check"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <?php if ($editItem): ?><a href="jobs.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Title</th><th>Company</th><th>Salary</th><th>Type</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['title']) ?></td>
                <td><?= e($item['company']) ?></td>
                <td><?= e($item['currency']) ?> <?= number_format($item['salary_min']) ?>-<?= number_format($item['salary_max']) ?></td>
                <td><?= e($item['job_type']) ?></td>
                <td><?= $item['is_featured'] ? 'Yes' : 'No' ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><div class="btn-group"><a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a><a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
