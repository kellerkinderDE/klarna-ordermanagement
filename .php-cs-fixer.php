<?php

require 'vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

return K10r\Codestyle\PHP72::create($finder, [
    'declare_strict_types' => false
], true);
