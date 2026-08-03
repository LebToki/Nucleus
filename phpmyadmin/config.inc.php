<?php
/**
 * phpMyAdmin Configuration for Nucleus Sovereign Platform
 * Auto-login as root with no password for daily convenience
 */

$cfg['blowfish_secret'] = 'nucleus_sovereign_phpmyadmin_secret_key_2026_9876543210';

$i = 0;
$i++;

/* Server parameters */
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowRoot'] = true;

/* Directories for saving/loading files from server */
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['TempDir'] = __DIR__ . '/tmp/';
