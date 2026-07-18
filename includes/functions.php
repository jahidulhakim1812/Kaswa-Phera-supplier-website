<?php
/**
 * Shared helper functions used across the public site.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function product_image_url(?string $image): string
{
    if (!empty($image) && file_exists(__DIR__ . '/../uploads/products/' . $image)) {
        return 'uploads/products/' . $image;
    }
    return 'assets/images/product-placeholder.svg';
}

function product_has_photo(?string $image): bool
{
    return !empty($image) && file_exists(__DIR__ . '/../uploads/products/' . $image);
}

function base_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    return $protocol . $_SERVER['HTTP_HOST'] . $dir;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
