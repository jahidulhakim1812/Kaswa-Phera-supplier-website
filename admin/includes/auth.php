<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/db.php';

// Product photo uploads can be several 25MB files in one request; raise the
// limits that PHP allows changing at runtime. upload_max_filesize and
// post_max_size must come from php.ini or .htaccess (see /.htaccess).
@ini_set('memory_limit', '256M');
@ini_set('max_execution_time', '120');

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

function admin_name(): string
{
    return $_SESSION['admin_name'] ?? 'Admin';
}

/** Path to a product image relative to the site root (for use with a leading ../ in admin pages) */
function product_thumb_admin(?string $image): string
{
    if (!empty($image) && file_exists(__DIR__ . '/../../uploads/products/' . $image)) {
        return 'uploads/products/' . $image;
    }
    return 'assets/images/product-placeholder.svg';
}

function admin_slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text ?: 'item-' . time();
}

/**
 * Validate and store one or more uploaded product photos.
 * Each file may be up to 25MB. Returns ['files' => [savedFileName, ...], 'error' => string|null].
 */
function admin_handle_multi_image_upload(array $filesArray, string $slug, string $destDir): array
{
    $maxBytes = 25 * 1024 * 1024; // 25MB per photo
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $saved = [];
    $error = null;

    // Normalize the $_FILES['images'] structure (multiple files under one field name)
    // into a flat list of individual file entries.
    $count = is_array($filesArray['name'] ?? null) ? count($filesArray['name']) : 0;
    for ($i = 0; $i < $count; $i++) {
        if (empty($filesArray['name'][$i]) || $filesArray['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($filesArray['error'][$i] !== UPLOAD_ERR_OK) {
            $error = 'One of the photos failed to upload. Please try again.';
            continue;
        }
        $ext = strtolower(pathinfo($filesArray['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $error = 'Each photo must be JPG, PNG or WEBP.';
            continue;
        }
        if ($filesArray['size'][$i] > $maxBytes) {
            $error = 'Each photo must be 25MB or smaller.';
            continue;
        }
        $newName = $slug . '-' . time() . '-' . substr(md5(uniqid((string)$i, true)), 0, 6) . '.' . $ext;
        if (move_uploaded_file($filesArray['tmp_name'][$i], rtrim($destDir, '/') . '/' . $newName)) {
            $saved[] = $newName;
        }
    }

    return ['files' => $saved, 'error' => $error];
}
