<?php

require_once __DIR__ . '/lib/backups.php';

Kirby::plugin('sylvainjule/backups', [
    'api' => [
        'routes' => [
            [
                'pattern' => 'backups/get-backups-list',
                'action'  => function() {
                    $b = new SylvainJule\Backups();
                    return $b->getBackupsArray(true);
                }
            ],
            [
                'pattern' => 'backups/copy-to-assets',
                'action'  => function() {
                    $filename  = get('filename');
                    $publicUrl = $filename ? (new SylvainJule\Backups())->copyToAssets($filename) : null;

                    return ['url' => $publicUrl];
                }
            ],
            [
                'pattern' => 'backups/delete-public-backups',
                'method'  => 'POST',
                'action'  => function() {
                    $b = new SylvainJule\Backups();
                    return $b->deletePublicBackups();
                }
            ],
            [
                'pattern' => 'backups/get-newest-backup',
                'action'  => function() {
                    $b = new SylvainJule\Backups();
                    return $b->getNewestBackup();
                }
            ],
            [
                'pattern' => 'backups/delete-backup',
                'method'  => 'POST',
                'action'  => function() {
                    $filename = get('filename');
                    $b        = new SylvainJule\Backups();
                    $deleted  = $b->deleteBackup($filename);

                    return ['deleted' => $deleted];
                }
            ],
            [
                'pattern' => 'backups/simulate-deletion',
                'action'  => function() {
                    $period   = get('period');
                    $b        = new SylvainJule\Backups();
                    $toDelete = $b->simulateDeletion($period);

                    return ['toDelete' => $toDelete];
                }
            ],
            [
                'pattern' => 'backups/delete-backups',
                'method'  => 'POST',
                'action'  => function() {
                    $period   = get('period');
                    $b        = new SylvainJule\Backups();
                    $deleted  = $b->deleteBackups($period);

                    return ['deleted' => $deleted];
                }
            ],
        ]
    ],
    'translations' => [
        'en' => require_once __DIR__ . '/lib/languages/en.php',
        'fr' => require_once __DIR__ . '/lib/languages/fr.php',
    ],
]);
