<?php
/**
 * AudiencePulse — i18n System
 * AR_EG primary, EN fallback
 */

// Current locale (session or default)
function currentLocale(): string {
    if (isset($_SESSION['locale'])) {
        return $_SESSION['locale'];
    }
    return 'en';
}

// Set locale
function setAppLocale(string $locale): void {
    $_SESSION['locale'] = $locale === 'ar' ? 'ar' : 'en';
}

// Load translation file
function loadTranslations(string $locale): array {
    static $cache = [];
    if (isset($cache[$locale])) {
        return $cache[$locale];
    }
    $file = APP_ROOT . '/i18n/' . $locale . '.json';
    $cache[$locale] = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    return $cache[$locale];
}

// Translate a key
function t(string $key, array $params = []): string {
    $locale = currentLocale();
    $translations = loadTranslations($locale);
    $text = $translations[$key] ?? $key;
    foreach ($params as $k => $v) {
        $text = str_replace('{' . $k . '}', $v, $text);
    }
    return $text;
}

// Get localized field (ar or en)
function l10n(array $row, string $field): string {
    $locale = currentLocale();
    $arField = $field . '_ar';
    if ($locale === 'ar' && !empty($row[$arField])) {
        return $row[$arField];
    }
    return $row[$field] ?? '';
}

// HTML direction
function htmlDir(): string {
    return currentLocale() === 'ar' ? 'rtl' : 'ltr';
}

// HTML lang
function htmlLang(): string {
    return currentLocale() === 'ar' ? 'ar' : 'en';
}
