<?php
// Temporary deployment helper. Delete this file immediately after use.
header('Cache-Control: no-store');
header('Content-Type: text/plain; charset=UTF-8');

echo function_exists('opcache_reset') && opcache_reset()
    ? "OPcache reset successfully.\n"
    : "OPcache unavailable or reset blocked.\n";
