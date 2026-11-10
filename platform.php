<?php
/**
 * Platform identity — Nucleus (Linux)
 *
 * This is the ONLY file that is allowed to differ between Nucleus and
 * Laragon-Dashboard. Shared core code reads identity from here and must never
 * hardcode a product name or a GitHub repository.
 *
 * Returned keys:
 *   product     — stable id used to validate update packages
 *   platform    — 'linux' | 'windows'
 *   update_repo — GitHub "owner/repo" the updater pulls releases from
 */

return [
    'product'     => 'nucleus',
    'platform'    => 'linux',
    'update_repo' => 'LebToki/Nucleus',
];
