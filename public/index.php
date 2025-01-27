<?php

declare(strict_types=1);

set_exception_handler(fn (Throwable $exception) => require __DIR__ . '/../views/error.php');

$path = $_SERVER['REQUEST_URI'];

// Remove trailing slashes
if ($path !== rtrim($path, '/') && $path !== '/') {
    header('Location: ' . rtrim($path, '/'), response_code: 308);
    exit;
}

match($path) {
    '/' => require __DIR__ . '/../views/post-list.php',
    '/feed.atom' => require __DIR__ . '/../views/feed.php',
    '/bad-bot' => require __DIR__ . '/../views/log-bad-bot.php',
    '/robots.txt' => require __DIR__ . '/../views/robots.php',
    '/2015/10/19/Easily-Accessing-Arguments-In-A-Shell-Script' => header('Location: /bash-argument-parser', response_code: 308),
    '/2015/08/03/Download-Single-File-From-Private-BitBucket-Repo' => header('Location: /bitbucket-download-file', response_code: 308),
    '/2016/10/11/BitBucket-Team-Issue-Management' => header('Location: /bitbucket-team-issue-manager', response_code: 308),
    '/2015/10/19/Securely-Deleting-Files-In-Caja' => header('Location: /caja-file-shred', response_code: 308),
    '/2015/06/23/Columnated-Coloursied-Content-In-The-Shell' => header('Location: /columnated-colour-content-in-the-shell', response_code: 308),
    '/2015/04/09/Composer-Auto-Self-Update' => header('Location: /composer-auto-self-update', response_code: 308),
    '/2015/08/01/Composer-Update-On-Production-Servers' => header('Location: /composer-update-are-you-sure', response_code: 308),
    '/2017/06/05/2FA-In-Firefox-with-Yubikey' => header('Location: /firefox-u2f-udev-rules', response_code: 308),
    '/2015/07/15/Git-Branch-Download' => header('Location: /git-download', response_code: 308),
    '/2016/02/14/Making-Git-Repos-Use-A-Specific-SSH-Key' => header('Location: /git-separate-ssh-keys', response_code: 308),
    '/git-seperate-ssh-keys' => header('Location: /git-separate-ssh-keys', response_code: 308),
    '/2017/07/01/Fetching-Size-Of-GitHub-Downloads-With-cURL' => header('Location: /github-curl-release-size', response_code: 308),
    '/2015/10/31/Happy-Halloween!' => header('Location: /halloween-2015', response_code: 308),
    '/2015/11/04/Lets-Encrypt-Certificate' => header('Location: /lets-encrypt-certificate-issued', response_code: 308),
    '/2015/08/21/MATE-Places-Menu-Collapsing' => header('Location: /mate-places-menu-collapsing', response_code: 308),
    '/2015/08/31/Running-A-Script-When-Locking-MATE' => header('Location: /mate-run-on-lock', response_code: 308),
    '/2015/04/24/Multi-SSH-Key-Manager' => header('Location: /multi-ssh-key-manager', response_code: 308),
    '/2015/08/26/Capturing-A-Whole-HTTP-Request-With-PHP' => header('Location: /php-capture-whole-request', response_code: 308),
    '/2016/09/26/PHPs-uniqid()-Does-Not-Generate-Random-IDs' => header('Location: /php-uniqid-doesnt-generate-random-ids', response_code: 308),
    '/2017/05/15/Pi-Zero-Micro-Pirate-Box' => header('Location: /pi-zero-micro-pirate-box', response_code: 308),
    '/2015/04/17/Raspberry-Pi-Image-Manager' => header('Location: /raspberry-pi-image-writer', response_code: 308),
    '/2016/02/04/Raspberry-Pi-SD-Card-Corruption' => header('Location: /raspberry-pi-sd-card-corruption', response_code: 308),
    '/2017/12/11/Recursive-File-Shredding' => header('Location: /recursive-shred', response_code: 308),
    '/2016/02/22/A-Super-Capacitor-Powered-Raspberry-Pi' => header('Location: /run-raspberry-pi-off-super-caps', response_code: 308),
    '/2015/04/13/USB-RAID-Speeds-On-The-Raspberry-Pi' => header('Location: /usb-raid-speeds-on-the-raspberry-pi', response_code: 308),
    '/2015/04/08/Ubuntu-Kernel-Upgrade-/boot-Partition' => header('Location: /ubuntu-kernel-upgrade-boot-partition', response_code: 308),
    '/2015/06/10/Caching-USB-ID-Database-Updates' => header('Location: /update-usbids-optimized', response_code: 308),
    '/2016/03/08/Apache-Hostname-Header' => header('Location: /apache-hostname-header', response_code: 308),
    default => require __DIR__ . '/../views/post.php',
};
