<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM user_profiles WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: profiles.php?msg=deleted'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $image = null;
    if (!empty($_FILES['photo']['name'])) $image = uploadImage($_FILES['photo'], 'profiles');

    $data = [
        $_POST['full_name'], $_POST['title'], $_POST['email'], $_POST['phone'],
        $_POST['whatsapp'], $_POST['linkedin'], $_POST['location'],
        (int)$_POST['experience_years'], $_POST['education'], $_POST['current_company'],
        $_POST['work_experience'], $_POST['technical_skills'], $_POST['certifications'],
        $_POST['education_details'], $_POST['projects'], $_POST['languages'],
        isset($_POST['is_active']) ? 1 : 0
    ];

    if ($id) {
        $sql = "UPDATE user_profiles SET full_name=?, title=?, email=?, phone=?, whatsapp=?, linkedin=?, location=?, experience_years=?, education=?, current_company=?, work_experience=?, technical_skills=?, certifications=?, education_details=?, projects=?, languages=?, is_active=?";
        if ($image) { $sql .= ", photo=?"; $data[] = $image; }
        $sql .= " WHERE id=?"; $data[] = $id;
        $db->prepare($sql)->execute($data);
    } else {
        $data[] = $image ?? '';
        $db->prepare("INSERT INTO user_profiles (full_name, title, email, phone, whatsapp, linkedin, location, experience_years, education, current_company, work_experience, technical_skills, certifications, education_details, projects, languages, is_active, photo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
    }
    header('Location: profiles.php?msg=saved'); exit;
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM user_profiles WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $editItem = $stmt->fetch();
}

$items = $db->query("SELECT * FROM user_profiles ORDER BY created_at DESC")->fetchAll();
if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved!' : 'Deleted!';

$pageTitle = 'Manage Profiles / CVs';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 16px;"><?= $editItem ? 'Edit' : 'Add' ?> Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">
        <div class="form-row">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="<?= e($editItem['full_name'] ?? '') ?>" required></div>
            <div class="form-group"><label>Title / Expertise</label><input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Photo</label><input type="file" name="photo" accept="image/*"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($editItem['email'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= e($editItem['phone'] ?? '') ?>"></div>
            <div class="form-group"><label>WhatsApp</label><input type="text" name="whatsapp" value="<?= e($editItem['whatsapp'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>LinkedIn URL</label><input type="text" name="linkedin" value="<?= e($editItem['linkedin'] ?? '') ?>"></div>
            <div class="form-group"><label>Location</label><input type="text" name="location" value="<?= e($editItem['location'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Years of Experience</label><input type="number" name="experience_years" value="<?= e($editItem['experience_years'] ?? '0') ?>"></div>
            <div class="form-group"><label>Education (summary)</label><input type="text" name="education" value="<?= e($editItem['education'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>Current Company</label><input type="text" name="current_company" value="<?= e($editItem['current_company'] ?? '') ?>"></div>
        <div class="form-group"><label>Work Experience (one per line: Role | Company | Dates | Location)</label><textarea name="work_experience" rows="4"><?= e($editItem['work_experience'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Technical Skills (comma separated)</label><textarea name="technical_skills" rows="2"><?= e($editItem['technical_skills'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Certifications</label><textarea name="certifications" rows="2"><?= e($editItem['certifications'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Education Details (one per line)</label><textarea name="education_details" rows="3"><?= e($editItem['education_details'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Projects</label><textarea name="projects" rows="3"><?= e($editItem['projects'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Languages (comma separated)</label><input type="text" name="languages" value="<?= e($editItem['languages'] ?? '') ?>"></div>
        <div class="form-group"><label class="form-check"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active</label></div>
        <button type="submit" class="btn btn-primary">Save Profile</button>
        <?php if ($editItem): ?><a href="profiles.php" class="btn btn-info">Cancel</a><?php endif; ?>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Photo</th><th>Name</th><th>Title</th><th>Exp</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= getImageUrl($item['photo'], 'profiles') ?>" alt="" style="border-radius:50%;"></td>
                <td><?= e($item['full_name']) ?></td>
                <td><?= e(substr($item['title'] ?? '', 0, 40)) ?></td>
                <td><?= $item['experience_years'] ?> yrs</td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td><div class="btn-group"><a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm">Edit</a><a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm">Delete</a></div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
