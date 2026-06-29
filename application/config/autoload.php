<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER
| -------------------------------------------------------------------
| Specifies which systems should be loaded by default.
*/

// 1. Auto-load Packages
$autoload['packages'] = array();

// 2. Auto-load Libraries (Pondasi Utama Sistem Peminjaman & Login)
$autoload['libraries'] = array('database', 'session', 'form_validation', 'email');

// 3. Auto-load Drivers
$autoload['drivers'] = array();

// 4. Auto-load Helper Files (Untuk bypass URL, Form Input, dan Upload berkas)
$autoload['helper'] = array('url', 'form', 'file');

// 5. Auto-load Config files
$autoload['config'] = array();

// 6. Auto-load Language files
$autoload['language'] = array();

// 7. Auto-load Models (Dikosongkan dulu, nanti kita panggil lewat Controller saja)
$autoload['model'] = array();