<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db  = getDB();
$msg = '';

// ── DELETE ─────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $db->prepare("DELETE FROM offer_reviews WHERE offer_id = ?")->execute([$did]);
    $db->prepare("DELETE FROM offers        WHERE id       = ?")->execute([$did]);
    header('Location: offers.php?msg=deleted'); exit;
}

// ── TOGGLE ACTIVE ──────────────────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $tid  = (int)$_GET['toggle'];
    $curr = $db->prepare("SELECT is_active FROM offers WHERE id=?"); $curr->execute([$tid]);
    $row  = $curr->fetch();
    if ($row) {
        $db->prepare("UPDATE offers SET is_active=? WHERE id=?")->execute([$row['is_active'] ? 0 : 1, $tid]);
    }
    header('Location: offers.php?msg=updated'); exit;
}

// ── SAVE (add / edit) ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        // ensure uploads/offers dir exists
        $dir = UPLOAD_PATH . 'offers/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $image = uploadImage($_FILES['image'], 'offers');
    }

    $data = [
        'business_id'      => (int)$_POST['business_id'],
        'category_id'      => $_POST['category_id']      ?: null,
        'title'            => trim($_POST['title'] ?? ''),
        'description'      => trim($_POST['description'] ?? ''),
        'details'          => trim($_POST['details'] ?? ''),
        'price'            => (float)($_POST['price'] ?? 0),
        'original_price'   => $_POST['original_price'] !== '' ? (float)$_POST['original_price'] : null,
        'currency'         => $_POST['currency'] ?: 'AED',
        'discount_percent' => $_POST['discount_percent'] !== '' ? (int)$_POST['discount_percent'] : null,
        'rating'           => (float)($_POST['rating'] ?? 0),
        'emirate'          => trim($_POST['emirate'] ?? ''),
        'ranking'          => (int)($_POST['ranking'] ?? 0),
        'valid_from'       => $_POST['valid_from'] ?: null,
        'valid_to'         => $_POST['valid_to']   ?: null,
        'is_active'        => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id) {
        $sets   = [];
        $params = [];
        foreach ($data as $k => $v) { $sets[] = "$k = ?"; $params[] = $v; }
        if ($image) { $sets[] = "image = ?"; $params[] = $image; }
        $params[] = $id;
        $db->prepare("UPDATE offers SET " . implode(', ', $sets) . " WHERE id = ?")->execute($params);
    } else {
        if ($image) $data['image'] = $image;
        $cols  = implode(', ', array_keys($data));
        $ph    = implode(', ', array_fill(0, count($data), '?'));
        $db->prepare("INSERT INTO offers ($cols) VALUES ($ph)")->execute(array_values($data));
    }
    header('Location: offers.php?msg=saved'); exit;
}

// ── LOAD DATA ──────────────────────────────────────────────────────────────
$editItem = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT o.*, b.name AS business_name FROM offers o JOIN businesses b ON o.business_id=b.id WHERE o.id=?");
    $s->execute([(int)$_GET['edit']]);
    $editItem = $s->fetch();
}

$items = $db->query("
    SELECT o.id, o.title, o.price, o.currency, o.discount_percent, o.emirate,
           o.ranking, o.rating, o.is_active, o.created_at,
           b.name AS business_name,
           bc.name AS category_name
    FROM offers o
    LEFT JOIN businesses b ON o.business_id = b.id
    LEFT JOIN business_categories bc ON o.category_id = bc.id
    ORDER BY o.ranking DESC, o.created_at DESC
")->fetchAll();

$mainCategories = $db->query("SELECT id, name FROM main_categories WHERE is_active=1 ORDER BY sort_order")->fetchAll();
$categories     = $db->query("SELECT id, name, main_category_id FROM business_categories ORDER BY sort_order, name")->fetchAll();

// For edit: find which main category the offer's business category belongs to
$editMainCatId = null;
if ($editItem && $editItem['category_id']) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $editItem['category_id']) { $editMainCatId = $cat['main_category_id']; break; }
    }
}

$EMIRATES = ['Dubai','Abu Dhabi','Sharjah','Ajman','Ras Al Khaimah','Fujairah','Umm Al Quwain'];

if (isset($_GET['msg'])) $msg = $_GET['msg'] === 'saved' ? 'Offer saved successfully!' : 'Offer deleted.';

$pageTitle = 'Manage Offers';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<!-- ── Add / Edit Form ───────────────────────────────────────────────────── -->
<div class="admin-form" style="margin-bottom:24px;">
    <h2 style="margin-bottom:16px;"><?= $editItem ? 'Edit' : 'Add' ?> Offer</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editItem['id'] ?? '' ?>">

        <!-- Cascade: Main Category → Business Category → Business -->
        <div class="form-row">
            <div class="form-group">
                <label>Main Category</label>
                <select id="mainCatSel" onchange="onMainCatChange(this.value)">
                    <option value="">— Select Main Category —</option>
                    <?php foreach ($mainCategories as $mc): ?>
                    <option value="<?= $mc['id'] ?>" <?= $editMainCatId == $mc['id'] ? 'selected' : '' ?>>
                        <?= e($mc['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Business Category</label>
                <select name="category_id" id="bizCatSel" onchange="onBizCatChange(this.value)">
                    <option value="">— Select Category —</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>"
                            data-main="<?= (int)$c['main_category_id'] ?>"
                            <?= ($editItem['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Business <span style="color:#E53935;">*</span></label>
            <select name="business_id" id="bizSel" required>
                <option value="">— Select Business —</option>
                <?php if ($editItem): ?>
                <option value="<?= (int)$editItem['business_id'] ?>" selected><?= e($editItem['business_name']) ?></option>
                <?php endif; ?>
            </select>
            <small id="bizHint" style="color:#9ca3af;">Select a business category first to filter businesses.</small>
        </div>

        <div class="form-group">
            <label>Title <span style="color:#E53935;">*</span></label>
            <input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Short description</label>
            <textarea name="description" rows="2"><?= e($editItem['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Full details (shown on Read More page)</label>
            <textarea name="details" rows="5"><?= e($editItem['details'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Price <span style="color:#E53935;">*</span></label>
                <input type="number" step="0.01" name="price" value="<?= e($editItem['price'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Original price</label>
                <input type="number" step="0.01" name="original_price" value="<?= e($editItem['original_price'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Currency</label>
                <select name="currency">
                    <?php foreach (['AED','USD','EUR','GBP','SAR'] as $c): ?>
                    <option <?= ($editItem['currency'] ?? 'AED') === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Discount %</label>
                <input type="number" name="discount_percent" placeholder="e.g. 30" value="<?= e($editItem['discount_percent'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Emirate / Location</label>
                <select name="emirate">
                    <option value="">— Select —</option>
                    <?php foreach ($EMIRATES as $em): ?>
                    <option <?= ($editItem['emirate'] ?? '') === $em ? 'selected' : '' ?>><?= $em ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Ranking (higher = top)</label>
                <input type="number" name="ranking" value="<?= (int)($editItem['ranking'] ?? 0) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Rating (0–5)</label>
                <input type="number" step="0.1" min="0" max="5" name="rating" value="<?= e($editItem['rating'] ?? '0') ?>">
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" accept="image/*">
                <?php if (!empty($editItem['image'])): ?>
                <small style="color:#6A1B9A;">Current: <?= e($editItem['image']) ?></small>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Valid from</label>
                <input type="date" name="valid_from" value="<?= e(($editItem['valid_from'] ?? '') ? substr($editItem['valid_from'],0,10) : '') ?>">
            </div>
            <div class="form-group">
                <label>Valid until</label>
                <input type="date" name="valid_to" value="<?= e(($editItem['valid_to'] ?? '') ? substr($editItem['valid_to'],0,10) : '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>>
                Active (visible on site)
            </label>
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> <?= $editItem ? 'Update Offer' : 'Add Offer' ?>
            </button>
            <?php if ($editItem): ?>
            <a href="offers.php" class="btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ── Offer list ─────────────────────────────────────────────────────────── -->
<div class="admin-table-wrap">
    <h2 style="margin-bottom:16px;">All Offers (<?= count($items) ?>)</h2>
    <?php if (empty($items)): ?>
    <p style="color:#9ca3af;text-align:center;padding:32px 0;">No offers yet. Add your first one above!</p>
    <?php else: ?>
    <div style="overflow-x:auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th><th>Business</th><th>Title</th><th>Price</th>
                <th>Location</th><th>Rank</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $row): ?>
        <tr>
            <td style="color:#9ca3af;"><?= $row['id'] ?></td>
            <td><?= e($row['business_name']) ?></td>
            <td>
                <?= e($row['title']) ?>
                <?php if ($row['discount_percent']): ?>
                <span style="font-size:11px;color:#E53935;font-weight:700;margin-left:4px;">-<?= $row['discount_percent'] ?>%</span>
                <?php endif; ?>
            </td>
            <td style="white-space:nowrap;">
                <?= e($row['currency']) ?> <?= number_format((float)$row['price'], 0) ?>
            </td>
            <td><?= e($row['emirate'] ?: '—') ?></td>
            <td><?= (int)$row['ranking'] ?></td>
            <td>
                <a href="?toggle=<?= $row['id'] ?>" class="badge <?= $row['is_active'] ? 'badge-active' : 'badge-inactive' ?>"
                   onclick="return confirm('Toggle status?')">
                    <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                </a>
            </td>
            <td style="white-space:nowrap;">
                <a href="?edit=<?= $row['id'] ?>" class="btn-icon btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                <a href="<?= SITE_URL ?>/pages/offer_detail.php?id=<?= $row['id'] ?>" target="_blank" class="btn-icon" title="View" style="color:#0984E3;"><i class="fas fa-eye"></i></a>
                <a href="?delete=<?= $row['id'] ?>" class="btn-icon btn-delete btn-delete-confirm" title="Delete"><i class="fas fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<script>
// Store a master copy of all bizCat options for client-side filtering
document.addEventListener('DOMContentLoaded', function () {
    const catSel = document.getElementById('bizCatSel');
    const allOpts = document.createElement('select');
    allOpts.id = 'bizCatAll';
    allOpts.style.display = 'none';
    allOpts.innerHTML = catSel.innerHTML;
    document.body.appendChild(allOpts);

    // Apply initial filter on page load (edit mode)
    const mainSel = document.getElementById('mainCatSel');
    if (mainSel.value) filterBizCats(mainSel.value);

    // If editing and category is already set, load businesses for it
    const catVal = catSel.value;
    if (catVal) loadBusinesses(catVal, <?= (int)($editItem['business_id'] ?? 0) ?>);
});

function onMainCatChange(mainId) {
    filterBizCats(mainId);
    // Reset downstream
    document.getElementById('bizSel').innerHTML = '<option value="">— Select Business —</option>';
}

function filterBizCats(mainId) {
    const sel = document.getElementById('bizCatSel');
    const prev = sel.value;
    sel.innerHTML = '<option value="">— Select Category —</option>';
    document.querySelectorAll('#bizCatAll option').forEach(opt => {
        if (opt.value === '') return;
        if (!mainId || opt.dataset.main === mainId) sel.appendChild(opt.cloneNode(true));
    });
    sel.value = document.querySelector(`#bizCatSel option[value="${prev}"]`) ? prev : '';
}

function onBizCatChange(categoryId) {
    const bizSel = document.getElementById('bizSel');
    bizSel.innerHTML = '<option value="">— Loading… —</option>';
    if (!categoryId) {
        bizSel.innerHTML = '<option value="">— Select Business —</option>';
        return;
    }
    loadBusinesses(categoryId, 0);
}

function loadBusinesses(categoryId, preselect) {
    const bizSel = document.getElementById('bizSel');
    const hint   = document.getElementById('bizHint');
    fetch('<?= SITE_URL ?>/admin/ajax/business_search.php?category_id=' + encodeURIComponent(categoryId))
        .then(r => r.json())
        .then(list => {
            bizSel.innerHTML = '<option value="">— Select Business —</option>';
            list.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.textContent = b.name + (b.emirate ? ' – ' + b.emirate : '');
                if (preselect && b.id == preselect) opt.selected = true;
                bizSel.appendChild(opt);
            });
            hint.textContent = list.length + ' business' + (list.length !== 1 ? 'es' : '') + ' found.';
        });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
