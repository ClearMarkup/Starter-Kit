<?php

if (!file_exists($rootpath . 'build')) {
    mkdir($rootpath . 'build');
} else {
    shell_exec('rm -rf ' . $rootpath . 'build');
    mkdir($rootpath . 'build');
}

$config = json_decode(file_get_contents($rootpath . 'ClearMarkup.json'), true);

$build_files = $config['buildFiles'];

foreach ($build_files as $file) {
    if (substr($file, -2) === '/!') {
        shell_exec('rsync -a -f"+ */" -f"- *" ' . $rootpath . substr($file, 0, -2) . ' ' . $rootpath . 'build/');
        continue;
    } else if (is_dir($rootpath . $file)) {
        shell_exec('rsync -a ' . $rootpath . $file . ' ' . $rootpath . 'build/' . $file);
    } else {
        copy($rootpath . $file, $rootpath . 'build/' . $file);
    }
}

if (isset($argv[2]) && $argv[2] === '-pwd') {
    $username = $argv[3];
    $password = $argv[4];
    $htpasswdPath = $rootpath . 'build/' . '.htpasswd';

    if (!file_exists($htpasswdPath)) {
        shell_exec('touch ' . $htpasswdPath);
    }

    shell_exec('htpasswd -b ' . $htpasswdPath . ' ' . $username . ' ' . $password);

    $htaccessPath = $rootpath . 'build/public/.htaccess';
    $htaccessContent = file_get_contents($htaccessPath);
    $htaccessContent = str_replace('# HTPASSWD', '
AuthType Basic
AuthName "Restricted Area"
AuthUserFile /var/www/.htpasswd
Require valid-user
', $htaccessContent);
    file_put_contents($htaccessPath, $htaccessContent);
}

echo "\033[32m✅ Build complete!\033[0m You can find the build files in the build/ directory\n";

// Gul text
/* echo "\033[33mYou need to move ClearMarkup-config.php to the parent directory of the public directory\033[0m\n"; */