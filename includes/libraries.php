<?php
/**
 * Nucleus - Downloadable Libraries Catalog
 * Version: 1.0.0
 * Description: Catalog of pre-packaged platform libraries that can be
 * downloaded, installed and mapped to a directory in the document root.
 * This mirrors Laragon's "Quick Add" feature.
 *
 * Each library entry:
 *  - key:        unique identifier used in the API
 *  - name:       display name
 *  - description: short description shown in the wizard
 *  - icon:       iconify icon name
 *  - color:      bootstrap color variant for the card
 *  - url:        direct download URL for the .zip archive
 *  - dir:        target directory name (mapped based on this name)
 *  - archive:    expected archive filename (used for extraction detection)
 *  - strip:      number of leading path segments to strip when extracting
 *                (e.g. 1 strips the top-level "wordpress/" folder)
 *  - category:   grouping category (CMS, Tools, E-Commerce, etc.)
 *  - requires_db: whether the platform typically needs a database
 */

if (!function_exists('getDownloadableLibraries')) {
    function getDownloadableLibraries() {
        return [
            // ===================== CMS / Blogging =====================
            'wordpress' => [
                'key'         => 'wordpress',
                'name'        => 'WordPress',
                'description' => 'The world\'s most popular CMS for blogs and websites.',
                'icon'        => 'devicon-plain:wordpress',
                'color'       => 'primary',
                'url'         => 'https://wordpress.org/latest.zip',
                'dir'         => 'wordpress',
                'archive'     => 'latest.zip',
                'strip'       => 1,
                'category'    => 'CMS',
                'requires_db' => true,
            ],
            'joomla' => [
                'key'         => 'joomla',
                'name'        => 'Joomla',
                'description' => 'A flexible and powerful open source CMS.',
                'icon'        => 'devicon-plain:joomla',
                'color'       => 'info',
                'url'         => 'https://downloads.joomla.org/cms/joomla5/5-2-4/Joomla_5-2-4-Stable-Full_Package.zip',
                'dir'         => 'joomla',
                'archive'     => 'joomla.zip',
                'strip'       => 0,
                'category'    => 'CMS',
                'requires_db' => true,
            ],
            'drupal' => [
                'key'         => 'drupal',
                'name'        => 'Drupal',
                'description' => 'A robust CMS used by millions of websites.',
                'icon'        => 'devicon-plain:drupal',
                'color'       => 'info',
                'url'         => 'https://ftp.drupal.org/files/projects/drupal-11.1.4.zip',
                'dir'         => 'drupal',
                'archive'     => 'drupal.zip',
                'strip'       => 1,
                'category'    => 'CMS',
                'requires_db' => true,
            ],
            'ghost' => [
                'key'         => 'ghost',
                'name'        => 'Ghost',
                'description' => 'A modern publishing platform for professional bloggers.',
                'icon'        => 'simple-icons:ghost',
                'color'       => 'dark',
                'url'         => 'https://ghost.org/zip/ghost-latest.zip',
                'dir'         => 'ghost',
                'archive'     => 'ghost.zip',
                'strip'       => 0,
                'category'    => 'CMS',
                'requires_db' => true,
            ],
            'mediawiki' => [
                'key'         => 'mediawiki',
                'name'        => 'MediaWiki',
                'description' => 'The software that powers Wikipedia.',
                'icon'        => 'simple-icons:mediawiki',
                'color'       => 'warning',
                'url'         => 'https://releases.wikimedia.org/mediawiki/1.42/mediawiki-1.42.5.zip',
                'dir'         => 'mediawiki',
                'archive'     => 'mediawiki.zip',
                'strip'       => 1,
                'category'    => 'CMS',
                'requires_db' => true,
            ],
            'phpbb' => [
                'key'         => 'phpbb',
                'name'        => 'phpBB',
                'description' => 'A popular open source bulletin board system.',
                'icon'        => 'simple-icons:php',
                'color'       => 'primary',
                'url'         => 'https://download.phpbb.com/pub/release/3.3/3.3.14/phpBB-3.3.14.zip',
                'dir'         => 'phpbb',
                'archive'     => 'phpbb.zip',
                'strip'       => 1,
                'category'    => 'Forum',
                'requires_db' => true,
            ],
            'flarum' => [
                'key'         => 'flarum',
                'name'        => 'Flarum',
                'description' => 'A delightfully simple community platform.',
                'icon'        => 'simple-icons:flarum',
                'color'       => 'danger',
                'url'         => 'https://flarum.org/download/flarum.zip',
                'dir'         => 'flarum',
                'archive'     => 'flarum.zip',
                'strip'       => 0,
                'category'    => 'Forum',
                'requires_db' => true,
            ],

            // ===================== E-Commerce =====================
            'opencart' => [
                'key'         => 'opencart',
                'name'        => 'OpenCart',
                'description' => 'A free and open source e-commerce platform.',
                'icon'        => 'simple-icons:opencart',
                'color'       => 'success',
                'url'         => 'https://github.com/opencart/opencart/releases/download/4.0.2.3/opencart-4.0.2.3.zip',
                'dir'         => 'opencart',
                'archive'     => 'opencart.zip',
                'strip'       => 0,
                'category'    => 'E-Commerce',
                'requires_db' => true,
            ],
            'prestashop' => [
                'key'         => 'prestashop',
                'name'        => 'PrestaShop',
                'description' => 'A powerful e-commerce solution for online stores.',
                'icon'        => 'simple-icons:prestashop',
                'color'       => 'danger',
                'url'         => 'https://download.prestashop.com/download/releases/prestashop_8.2.1.zip',
                'dir'         => 'prestashop',
                'archive'     => 'prestashop.zip',
                'strip'       => 0,
                'category'    => 'E-Commerce',
                'requires_db' => true,
            ],
            'magento' => [
                'key'         => 'magento',
                'name'        => 'Magento',
                'description' => 'A feature-rich enterprise e-commerce platform.',
                'icon'        => 'simple-icons:magento',
                'color'       => 'warning',
                'url'         => 'https://github.com/magento/magento2/archive/refs/heads/2.4-develop.zip',
                'dir'         => 'magento',
                'archive'     => 'magento.zip',
                'strip'       => 1,
                'category'    => 'E-Commerce',
                'requires_db' => true,
            ],
            'woocommerce' => [
                'key'         => 'woocommerce',
                'name'        => 'WooCommerce',
                'description' => 'The most popular e-commerce plugin for WordPress.',
                'icon'        => 'simple-icons:woocommerce',
                'color'       => 'primary',
                'url'         => 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip',
                'dir'         => 'woocommerce',
                'archive'     => 'woocommerce.zip',
                'strip'       => 1,
                'category'    => 'E-Commerce',
                'requires_db' => true,
            ],

            // ===================== Tools / Admin =====================
            'phpmyadmin' => [
                'key'         => 'phpmyadmin',
                'name'        => 'phpMyAdmin',
                'description' => 'A web-based MySQL/MariaDB administration tool.',
                'icon'        => 'tabler:brand-mysql',
                'color'       => 'info',
                'url'         => 'https://files.phpmyadmin.net/phpMyAdmin/5.2.2/phpMyAdmin-5.2.2-all-languages.zip',
                'dir'         => 'phpmyadmin',
                'archive'     => 'phpmyadmin.zip',
                'strip'       => 1,
                'category'    => 'Tools',
                'requires_db' => false,
            ],
            'adminer' => [
                'key'         => 'adminer',
                'name'        => 'Adminer',
                'description' => 'A lightweight database management tool in a single file.',
                'icon'        => 'tabler:database',
                'color'       => 'success',
                'url'         => 'https://www.adminer.org/latest.php',
                'dir'         => 'adminer',
                'archive'     => 'adminer.php',
                'strip'       => 0,
                'category'    => 'Tools',
                'requires_db' => false,
            ],
            'phpinfo' => [
                'key'         => 'phpinfo',
                'name'        => 'PHP Info',
                'description' => 'A simple PHP info page for environment diagnostics.',
                'icon'        => 'mdi:language-php',
                'color'       => 'primary',
                'url'         => '',
                'dir'         => 'phpinfo',
                'archive'     => '',
                'strip'       => 0,
                'category'    => 'Tools',
                'requires_db' => false,
            ],

            // ===================== Learning / Documentation =====================
            'phpmyfaq' => [
                'key'         => 'phpmyfaq',
                'name'        => 'phpMyFAQ',
                'description' => 'A multilingual FAQ management system.',
                'icon'        => 'simple-icons:php',
                'color'       => 'primary',
                'url'         => 'https://github.com/thorsten/phpMyFAQ/releases/download/3.2.10/phpMyFAQ-3.2.10.zip',
                'dir'         => 'phpmyfaq',
                'archive'     => 'phpmyfaq.zip',
                'strip'       => 1,
                'category'    => 'Documentation',
                'requires_db' => true,
            ],
            'bookstack' => [
                'key'         => 'bookstack',
                'name'        => 'BookStack',
                'description' => 'A self-hosted documentation and wiki platform.',
                'icon'        => 'simple-icons:bookstack',
                'color'       => 'info',
                'url'         => 'https://github.com/BookStackApp/BookStack/releases/download/v24.10.2/BookStack-v24.10.2.zip',
                'dir'         => 'bookstack',
                'archive'     => 'bookstack.zip',
                'strip'       => 1,
                'category'    => 'Documentation',
                'requires_db' => true,
            ],

            // ===================== Analytics / Monitoring =====================
            'matomo' => [
                'key'         => 'matomo',
                'name'        => 'Matomo',
                'description' => 'A privacy-friendly web analytics platform.',
                'icon'        => 'simple-icons:matomo',
                'color'       => 'danger',
                'url'         => 'https://builds.matomo.org/matomo.zip',
                'dir'         => 'matomo',
                'archive'     => 'matomo.zip',
                'strip'       => 1,
                'category'    => 'Analytics',
                'requires_db' => true,
            ],
        ];
    }
}

/**
 * Get a single library by key
 */
if (!function_exists('getDownloadableLibrary')) {
    function getDownloadableLibrary($key) {
        $libraries = getDownloadableLibraries();
        return $libraries[$key] ?? null;
    }
}

/**
 * Get libraries grouped by category
 */
if (!function_exists('getDownloadableLibrariesByCategory')) {
    function getDownloadableLibrariesByCategory() {
        $libraries = getDownloadableLibraries();
        $grouped = [];
        foreach ($libraries as $lib) {
            $category = $lib['category'] ?? 'Other';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $lib;
        }
        return $grouped;
    }
}
