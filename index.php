<?php

use Kirby\Toolkit\Dir;
use Kirby\Toolkit\F;

Kirby::plugin('sylvainjule/backups', [
    'api' => [
        'routes' => [
            [
                'pattern' => 'backups/get-backups-list',
                'action'  => function() {
                    $dir     = \Bnomei\BackupZipJob::directory();
                    $backups = Dir::read($dir);

                    // we want the most recent backups first
                    rsort($backups);

                    // for each backup, we want to return its filename, size and creation date
                    $backups = array_map(function($filename) use($dir) {
                        return [
                            'filename' => $filename,
                            'size'     => F::niceSize($dir .'/'. $filename),
                            'date'     => date('d/m/Y, H:i:s', F::modified($dir .'/'. $filename))
                        ];
                    }, $backups);

                    // we return the backups array
                    return $backups;
                }
            ],
            [
                'pattern' => 'backups/copy-to-assets',
                'action'  => function() {
                    $filename   = get('filename');
                    $dir        = \Bnomei\BackupZipJob::directory();
                    $path       = $dir . '/' . $filename;
                    $publicPath = kirby()->root('assets') .'/backups-temp/'. $filename;
                    $publicUrl  = kirby()->url('assets')  .'/backups-temp/'. $filename;

                    // we copy the file to its publicPath
                    F::copy($path, $publicPath);

                    // and we return the public url
                    return ['url' => $publicUrl];
                }
            ],
            [
                'pattern' => 'backups/delete-public-backups',
                'method'  => 'POST',
                'action'  => function() {
                    $publicPath = kirby()->root('assets') .'/backups-temp';

                    if(Dir::exists($publicPath)) {
                        Dir::remove($publicPath);
                    }

                    return [];
                }
            ],
            [
                'pattern' => 'backups/delete-backup',
                'method'  => 'POST',
                'action'  => function() {
                    $dir      = \Bnomei\BackupZipJob::directory();
                    $filename = get('filename');
                    $path     = $dir .'/'. $filename;
                    $deleted  = false;

                    if(F::exists($path)) {
                        F::remove($path);
                        $deleted = true;
                    }

                    return ['deleted' => $deleted];
                }
            ]
        ]
    ],
    'translations' => [
        'en' => require_once __DIR__ . '/languages/en.php',
        'fr' => require_once __DIR__ . '/languages/fr.php',
    ],
]);
