<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db  = getDB();
$msg = '';

/* ── DELETE ── */
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM businesses WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: businesses.php?msg=deleted'); exit;
}
if (isset($_GET['delete_img'])) {
    $db->prepare("DELETE FROM business_gallery WHERE id = ? AND business_id = ?")
       ->execute([(int)$_GET['delete_img'], (int)$_GET['biz_id']]);
    header('Location: businesses.php?edit=' . (int)$_GET['biz_id'] . '&tab=gallery'); exit;
}
if (isset($_GET['delete_sub'])) {
    $db->prepare("DELETE FROM business_sub_categories WHERE id = ? AND business_id = ?")
       ->execute([(int)$_GET['delete_sub'], (int)$_GET['biz_id']]);
    header('Location: businesses.php?edit=' . (int)$_GET['biz_id'] . '&tab=subcats'); exit;
}

/* ── ADD GALLERY IMAGE ── */
if (isset($_POST['add_gallery'])) {
    $bizId = (int)$_POST['biz_id'];
    $img   = null;
    if (!empty($_FILES['gallery_image']['name'])) $img = uploadImage($_FILES['gallery_image'], 'businesses');
    if ($img || !empty($_POST['gallery_img_name'])) {
        $imgFile = $img ?? $_POST['gallery_img_name'];
        $db->prepare("INSERT INTO business_gallery (business_id, image, title, description, caption) VALUES (?,?,?,?,?)")
           ->execute([$bizId, $imgFile, $_POST['gallery_title'] ?? '', $_POST['gallery_desc'] ?? '', $_POST['gallery_title'] ?? '']);
    }
    header('Location: businesses.php?edit=' . $bizId . '&tab=gallery'); exit;
}

/* ── ADD SUB-CATEGORY ── */
if (isset($_POST['add_subcat'])) {
    $bizId = (int)$_POST['biz_id'];
    $img   = null;
    if (!empty($_FILES['subcat_image']['name'])) $img = uploadImage($_FILES['subcat_image'], 'businesses');
    $db->prepare("INSERT INTO business_sub_categories (business_id, name, image) VALUES (?,?,?)")
       ->execute([$bizId, $_POST['subcat_name'], $img ?? ($_POST['subcat_img_name'] ?? null)]);
    header('Location: businesses.php?edit=' . $bizId . '&tab=subcats'); exit;
}

/* ── SAVE BUSINESS ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['add_gallery']) && !isset($_POST['add_subcat'])) {
    $id   = (int)($_POST['id'] ?? 0);
    $logo = null;
    if (!empty($_FILES['logo']['name'])) $logo = uploadImage($_FILES['logo'], 'businesses');

    $data = [
        $_POST['name'],
        $_POST['category_id'] ?: null,
        $_POST['description'],
        (float)($_POST['rating'] ?? 0),
        $_POST['distance'] ?? '',
        $_POST['address'],
        $_POST['phone'],
        $_POST['whatsapp'],
        $_POST['email'] ?? '',
        $_POST['user_id'] ?: null,
        $_POST['emirate'] ?? null,
        $_POST['map_link'] ?? null,
        $_POST['map_link'] ?? null,
        $_POST['opening_time'] ?: null,
        $_POST['closing_time'] ?: null,
        implode(', ', $_POST['holiday'] ?? []) ?: null,
        $_POST['website'] ?? null,
        $_POST['facebook'] ?? null,
        $_POST['instagram'] ?? null,
        $_POST['youtube'] ?? null,
        $_POST['snapchat'] ?? null,
        $_POST['tiktok'] ?? null,
        $_POST['twitter'] ?? null,
        $_POST['linkedin'] ?? null,
        isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $sql = "UPDATE businesses SET
            name=?, category_id=?, description=?, rating=?, distance=?, address=?, phone=?,
            whatsapp=?, email=?, user_id=?, emirate=?, map_link=?, map_embed=?,
            opening_time=?, closing_time=?, holiday=?,
            website=?, facebook=?, instagram=?, youtube=?, snapchat=?, tiktok=?,
            twitter=?, linkedin=?, is_active=?";
        if ($logo) { $sql .= ", image=?, logo=?"; $data[] = $logo; $data[] = $logo; }
        $sql .= " WHERE id=?"; $data[] = $id;
        $db->prepare($sql)->execute($data);
    } else {
        $data[] = $logo ?? '';
        $data[] = $logo ?? '';
        $db->prepare("INSERT INTO businesses
            (name, category_id, description, rating, distance, address, phone, whatsapp, email,
             user_id, emirate, map_link, map_embed, opening_time, closing_time, holiday,
             website, facebook, instagram, youtube, snapchat, tiktok, twitter, linkedin,
             is_active, image, logo)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
           ->execute($data);
    }
    $savedId = $id ?: $db->lastInsertId();
    header('Location: businesses.php?edit=' . $savedId . '&msg=saved'); exit;
}

/* ── LOAD DATA ── */
$editItem = null;
$gallery  = [];
$subcats  = [];
$activeTab = $_GET['tab'] ?? 'basic';

if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM businesses WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
    if ($editItem) {
        $gallery = $db->prepare("SELECT * FROM business_gallery WHERE business_id = ? ORDER BY sort_order, id");
        $gallery->execute([$editItem['id']]); $gallery = $gallery->fetchAll();
        $subcats = $db->prepare("SELECT * FROM business_sub_categories WHERE business_id = ? ORDER BY sort_order, id");
        $subcats->execute([$editItem['id']]); $subcats = $subcats->fetchAll();
    }
}

$mainCategories = $db->query("SELECT id, name FROM main_categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
$categories     = $db->query("SELECT id, name, main_category_id, group_name FROM business_categories ORDER BY sort_order, name")->fetchAll();
$users          = $db->query("SELECT id, name, email, mobile FROM users ORDER BY name")->fetchAll();

// Find the main_category_id for the current business's category (for pre-selecting the dropdown)
$editMainCatId = null;
if ($editItem && $editItem['category_id']) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $editItem['category_id']) { $editMainCatId = $cat['main_category_id']; break; }
    }
}
$items      = $db->query("SELECT b.*, bc.name as cat_name FROM businesses b LEFT JOIN business_categories bc ON b.category_id = bc.id ORDER BY b.created_at DESC")->fetchAll();

$holidays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$savedHolidays = $editItem ? array_map('trim', explode(',', $editItem['holiday'] ?? '')) : [];

if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Saved successfully!' : 'Deleted!';

$pageTitle = 'Manage Businesses';
include __DIR__ . '/includes/header.php';
?>

<style>
.tabs { display:flex; gap:0; border-bottom:2px solid #e8eaf2; margin-bottom:22px; flex-wrap:wrap; }
.tab-btn { padding:9px 16px; border:none; background:none; cursor:pointer; font-size:13px; font-weight:600; color:#9ca3af; border-bottom:3px solid transparent; margin-bottom:-2px; white-space:nowrap; font-family:inherit; }
.tab-btn.active { color:#E53935; border-bottom-color:#E53935; }
.tab-pane { display:none; } .tab-pane.active { display:block; }
.form-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.day-pill { display:inline-flex; align-items:center; padding:5px 13px; border-radius:20px; border:1.5px solid #ddd; font-size:12px; font-weight:600; cursor:pointer; background:#fff; color:#555; margin:4px; }
.day-pill.active { background:#E53935; color:#fff; border-color:#E53935; }
.social-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.social-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.gallery-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:12px; margin-bottom:20px; }
.gallery-thumb { position:relative; border-radius:10px; overflow:hidden; border:1px solid #e8eaf2; }
.gallery-thumb img { width:100%; height:90px; object-fit:cover; display:block; }
.gallery-thumb .del-btn { position:absolute; top:4px; right:4px; background:#ef4444; color:#fff; border:none; border-radius:5px; width:22px; height:22px; cursor:pointer; font-size:11px; display:flex; align-items:center; justify-content:center; text-decoration:none; }
.gallery-thumb .caption { padding:5px 7px; font-size:11px; font-weight:600; color:#374151; }
.subcat-list { display:flex; flex-direction:column; gap:10px; margin-bottom:20px; }
.subcat-item { display:flex; align-items:center; gap:12px; background:#f8f9fc; border-radius:10px; padding:10px 14px; }
.subcat-item img { width:42px; height:42px; border-radius:8px; object-fit:cover; }
.logo-preview { max-width:140px; max-height:140px; border-radius:12px; object-fit:cover; border:2px solid #e8eaf2; display:block; margin-bottom:10px; }
</style>

<?php if ($msg): ?>
<div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<div class="admin-form" style="margin-bottom:28px;">
    <h2 style="margin-bottom:18px;"><?= $editItem ? 'Edit: '.e($editItem['name']) : 'Add Business' ?></h2>

    <div class="tabs">
        <?php foreach ([
            ['basic',    'fa-info-circle',    'Basic'],
            ['location', 'fa-map-marker-alt', 'Location'],
            ['hours',    'fa-clock',          'Hours'],
            ['social',   'fa-share-alt',      'Social'],
            ['logo',     'fa-image',          'Logo'],
            ['gallery',  'fa-images',         'Gallery'],
            ['subcats',  'fa-th-large',       'Sub-cats'],
        ] as [$key, $icon, $label]): ?>
        <button type="button" class="tab-btn <?= $activeTab === $key ? 'active' : '' ?>"
                onclick="switchTab('<?= $key ?>')" id="tab-<?= $key ?>">
            <i class="fas <?= $icon ?>"></i> <?= $label ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- ── BASIC / LOCATION / HOURS / SOCIAL / LOGO FORM ── -->
    <form method="POST" enctype="multipart/form-data" id="mainForm">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">

        <!-- BASIC -->
        <div class="tab-pane <?= $activeTab === 'basic' ? 'active' : '' ?>" id="pane-basic">
            <div class="form-row">
                <div class="form-group">
                    <label>Business Name *</label>
                    <input type="text" name="name" value="<?= e($editItem['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Main Category</label>
                    <select id="main_category_select" onchange="filterBizCategories(this.value)">
                        <option value="">— Select Main Category —</option>
                        <?php foreach ($mainCategories as $mc): ?>
                        <option value="<?= $mc['id'] ?>" <?= $editMainCatId == $mc['id'] ? 'selected' : '' ?>>
                            <?= e($mc['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Business Category</label>
                    <select name="category_id" id="category_id_select">
                        <option value="">— Select Category —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                                data-main="<?= (int)$cat['main_category_id'] ?>"
                                <?= ($editItem['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Linked User</label>
                    <select name="user_id">
                        <option value="">— None —</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($editItem['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= e($u['name'] ?: ($u['email'] ?: $u['mobile'])) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Rating (0–5)</label>
                    <input type="number" step="0.1" min="0" max="5" name="rating" value="<?= e($editItem['rating'] ?? '0') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?= e($editItem['description'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= e($editItem['phone'] ?? '') ?>" placeholder="+971 50 000 0000"></div>
                <div class="form-group"><label>WhatsApp</label><input type="text" name="whatsapp" value="<?= e($editItem['whatsapp'] ?? '') ?>" placeholder="+971 50 000 0000"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($editItem['email'] ?? '') ?>"></div>
                <div class="form-group"><label>Distance</label><input type="text" name="distance" value="<?= e($editItem['distance'] ?? '') ?>" placeholder="e.g. 2 km away"></div>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>> Active
                </label>
            </div>
        </div>

        <!-- LOCATION -->
        <div class="tab-pane <?= $activeTab === 'location' ? 'active' : '' ?>" id="pane-location">
            <div class="form-row">
                <div class="form-group">
                    <label>Emirate</label>
                    <select name="emirate">
                        <option value="">— Select Emirate —</option>
                        <?php foreach (['Abu Dhabi','Dubai','Sharjah','Ajman','Umm Al Quwain','Ras Al Khaimah','Fujairah'] as $em): ?>
                        <option value="<?= $em ?>" <?= ($editItem['emirate'] ?? '') === $em ? 'selected' : '' ?>><?= $em ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Area / Address</label>
                    <input type="text" name="address" value="<?= e($editItem['address'] ?? '') ?>" placeholder="Street, Area…">
                </div>
            </div>
            <div class="form-group">
                <label>Google Maps Link</label>
                <input type="url" name="map_link" value="<?= e($editItem['map_link'] ?? $editItem['map_embed'] ?? '') ?>" placeholder="https://maps.google.com/…">
            </div>
            <?php if (!empty($editItem['map_link'] ?? $editItem['map_embed'])): ?>
            <a href="<?= e($editItem['map_link'] ?? $editItem['map_embed']) ?>" target="_blank" style="font-size:13px;color:#E53935;">
                <i class="fas fa-external-link-alt"></i> Open in Maps
            </a>
            <?php endif; ?>
        </div>

        <!-- HOURS -->
        <div class="tab-pane <?= $activeTab === 'hours' ? 'active' : '' ?>" id="pane-hours">
            <div class="form-row">
                <div class="form-group">
                    <label>Opening Time</label>
                    <input type="time" name="opening_time" value="<?= e($editItem['opening_time'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Closing Time</label>
                    <input type="time" name="closing_time" value="<?= e($editItem['closing_time'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Holiday / Closed Days</label>
                <div style="margin-top:8px;">
                    <?php foreach ($holidays as $day): ?>
                    <label class="day-pill <?= in_array($day, $savedHolidays) ? 'active' : '' ?>" onclick="toggleDay(this)">
                        <input type="checkbox" name="holiday[]" value="<?= $day ?>" <?= in_array($day, $savedHolidays) ? 'checked' : '' ?> style="display:none">
                        <?= $day ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- SOCIAL -->
        <div class="tab-pane <?= $activeTab === 'social' ? 'active' : '' ?>" id="pane-social">
            <?php $socials = [
                ['website',   'fas fa-globe',      '#6C5CE7', 'Website',    'https://yoursite.com'],
                ['facebook',  'fab fa-facebook',   '#1877f2', 'Facebook',   'facebook.com/page'],
                ['instagram', 'fab fa-instagram',  '#e1306c', 'Instagram',  '@handle'],
                ['youtube',   'fab fa-youtube',    '#ff0000', 'YouTube',    'youtube.com/channel/…'],
                ['snapchat',  'fab fa-snapchat',   '#FFCA28', 'Snapchat',   '@snapname'],
                ['tiktok',    'fab fa-tiktok',     '#010101', 'TikTok',     '@tiktokname'],
                ['twitter',   'fab fa-twitter',    '#1da1f2', 'X / Twitter','@handle'],
                ['linkedin',  'fab fa-linkedin',   '#0077b5', 'LinkedIn',   'linkedin.com/company/…'],
            ]; ?>
            <?php foreach ($socials as [$field, $icon, $color, $label, $ph]): ?>
            <div class="social-row">
                <div class="social-icon" style="background:<?= $color ?>22;">
                    <i class="<?= $icon ?>" style="color:<?= $color ?>"></i>
                </div>
                <div style="flex:1">
                    <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:4px;"><?= $label ?></label>
                    <input type="text" name="<?= $field ?>" value="<?= e($editItem[$field] ?? '') ?>" placeholder="<?= $ph ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- LOGO -->
        <div class="tab-pane <?= $activeTab === 'logo' ? 'active' : '' ?>" id="pane-logo">
            <div class="form-group" style="text-align:center;">
                <?php $logoFile = $editItem['logo'] ?? $editItem['image'] ?? ''; ?>
                <?php if ($logoFile): ?>
                <img src="<?= getImageUrl($logoFile, 'businesses') ?>" alt="Logo" class="logo-preview">
                <?php endif; ?>
                <label>Upload New Logo</label>
                <input type="file" name="logo" accept="image/*">
                <small style="display:block;color:#888;margin-top:6px;">JPG, PNG, WEBP. Current: <?= e($logoFile ?: '—') ?></small>
            </div>
        </div>

        <div style="margin-top:22px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Business</button>
            <?php if ($editItem): ?>
            <a href="businesses.php" class="btn btn-info">Cancel</a>
            <a href="<?= SITE_URL ?>/businesses/<?= $editItem['id'] ?>" target="_blank" class="btn btn-info">
                <i class="fas fa-eye"></i> View
            </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- ── GALLERY ── -->
    <div class="tab-pane <?= $activeTab === 'gallery' ? 'active' : '' ?>" id="pane-gallery">
        <?php if (!$editItem): ?>
        <div style="text-align:center;padding:30px;color:#9ca3af;">
            <i class="fas fa-info-circle" style="font-size:24px;display:block;margin-bottom:10px;"></i>
            Save the business first, then add gallery images.
        </div>
        <?php else: ?>

        <?php if ($gallery): ?>
        <div class="gallery-grid">
            <?php foreach ($gallery as $g): ?>
            <div class="gallery-thumb">
                <img src="<?= getImageUrl($g['image'], 'businesses') ?>" alt="">
                <?php if ($g['title']): ?><div class="caption"><?= e($g['title']) ?></div><?php endif; ?>
                <a href="?delete_img=<?= $g['id'] ?>&biz_id=<?= $editItem['id'] ?>" class="del-btn"
                   onclick="return confirm('Delete image?')"><i class="fas fa-times"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" style="background:#f8f9fc;border-radius:12px;padding:18px;">
            <input type="hidden" name="add_gallery" value="1">
            <input type="hidden" name="biz_id" value="<?= $editItem['id'] ?>">
            <h4 style="margin-bottom:14px;font-size:14px;">Add Image</h4>
            <div class="form-group"><label>Upload Image</label><input type="file" name="gallery_image" accept="image/*"></div>
            <div class="form-row">
                <div class="form-group"><label>Title</label><input type="text" name="gallery_title" placeholder="Image title"></div>
                <div class="form-group"><label>Description</label><input type="text" name="gallery_desc" placeholder="Short description"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add to Gallery</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- ── SUB-CATEGORIES ── -->
    <div class="tab-pane <?= $activeTab === 'subcats' ? 'active' : '' ?>" id="pane-subcats">
        <?php if (!$editItem): ?>
        <div style="text-align:center;padding:30px;color:#9ca3af;">
            <i class="fas fa-info-circle" style="font-size:24px;display:block;margin-bottom:10px;"></i>
            Save the business first, then add sub-categories.
        </div>
        <?php else: ?>

        <?php if ($subcats): ?>
        <div class="subcat-list">
            <?php foreach ($subcats as $s): ?>
            <div class="subcat-item">
                <?php if ($s['image']): ?>
                <img src="<?= getImageUrl($s['image'], 'businesses') ?>" alt="">
                <?php endif; ?>
                <span style="flex:1;font-weight:600;font-size:14px;"><?= e($s['name']) ?></span>
                <a href="?delete_sub=<?= $s['id'] ?>&biz_id=<?= $editItem['id'] ?>" class="btn btn-delete btn-sm"
                   onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" style="background:#f8f9fc;border-radius:12px;padding:18px;">
            <input type="hidden" name="add_subcat" value="1">
            <input type="hidden" name="biz_id" value="<?= $editItem['id'] ?>">
            <h4 style="margin-bottom:14px;font-size:14px;">Add Sub-Category</h4>
            <div class="form-row">
                <div class="form-group"><label>Name *</label><input type="text" name="subcat_name" required placeholder="e.g. Starters, Electronics…"></div>
                <div class="form-group"><label>Image (optional)</label><input type="file" name="subcat_image" accept="image/*"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Sub-Category</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- ── TABLE ── -->
<div class="admin-table-wrap">
    <h2 style="margin-bottom:14px;">All Businesses (<?= count($items) ?>)</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Logo</th><th>Name</th><th>Category</th>
                <th>Emirate</th><th>Phone</th><th>Hours</th>
                <th>Rating</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= getImageUrl($item['logo'] ?: $item['image'], 'businesses') ?>" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:8px;"></td>
                <td><strong><?= e($item['name']) ?></strong></td>
                <td><?= e($item['cat_name'] ?? '—') ?></td>
                <td><?= e($item['emirate'] ?? '—') ?></td>
                <td><?= e($item['phone'] ?? '—') ?></td>
                <td style="white-space:nowrap;font-size:12px;">
                    <?php if ($item['opening_time'] || $item['closing_time']): ?>
                    <i class="fas fa-clock" style="color:#6C5CE7"></i>
                    <?= e($item['opening_time'] ?? '—') ?> – <?= e($item['closing_time'] ?? '—') ?>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><i class="fas fa-star" style="color:#FDCB6E"></i> <?= number_format($item['rating'], 1) ?></td>
                <td><span class="badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                    <div class="btn-group">
                        <a href="?edit=<?= $item['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-pen"></i> Edit</a>
                        <a href="?delete=<?= $item['id'] ?>" class="btn btn-delete btn-sm" onclick="return confirm('Delete this business?')"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function switchTab(key) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + key).classList.add('active');
    document.getElementById('pane-' + key).classList.add('active');
}
function toggleDay(el) {
    el.classList.toggle('active');
    el.querySelector('input').checked = el.classList.contains('active');
}
function filterBizCategories(mainId) {
    const sel = document.getElementById('category_id_select');
    const current = sel.value;
    sel.innerHTML = '<option value="">— Select Category —</option>';
    document.querySelectorAll('#category_id_select_all option').forEach(opt => {
        if (!mainId || opt.dataset.main === mainId) {
            sel.appendChild(opt.cloneNode(true));
        }
    });
    sel.value = (document.querySelector('#category_id_select option[value="' + current + '"]')) ? current : '';
}
// Keep a hidden clone of all options to filter from
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('category_id_select');
    const clone = document.createElement('select');
    clone.id = 'category_id_select_all';
    clone.style.display = 'none';
    clone.innerHTML = sel.innerHTML;
    document.body.appendChild(clone);
    // Apply initial filter if a main category is pre-selected
    const mainSel = document.getElementById('main_category_select');
    if (mainSel.value) filterBizCategories(mainSel.value);
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
