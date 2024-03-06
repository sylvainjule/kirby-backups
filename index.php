<?php

load([
    'sylvainjule\\backups' => __DIR__ . '/lib/backups.php',
]);

Kirby::plugin('sylvainjule/backups', [
    'areas' => [
        'backups' => function () {
            return [
                'icon' => 'server',
                'label' => t('view.backups'),
                'menu'  => true,
                'views' => [
                    'backups' => [
                        'pattern' => 'backups',
                        'action'  => function () {
                            $backups = (new SylvainJule\Backups())->getBackupsArray(true);
                            $count   = count($backups);

                            return [
                                'component' => 'k-backups-view',
                                'props' => [
                                    'backups' => $backups,
                                    'title'   => tc('backups.pluralized', $count)
                                ]
                            ];
                        }
                    ]
                ]
            ];
        }
    ],
    'options' => [
        'publicFolder' => 'backups-temp',
        'prefix'       => 'backup-',
        'maximum'      => false,
    ],
    'routes' => [
        [
            'pattern' => 'backups-webhook/(:any)/create-backup',
            'action' => function($secret) {
                if ($secret != janitor()->option('secret')) {
                    \Kirby\Http\Header::status(401);
                    die();
                }

                try {
                    $janitor = (new SylvainJule\Backups())->createBackup();
                    unset($janitor['path']);

                    return $janitor;
                }
                catch(Exception $e) {
                    return [
                        'status' => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
            }
        ],
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'backups/create-backup',
                'action'  => function() {
                    $b = new SylvainJule\Backups();
                    return $b->createBackup();
                }
            ],
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
        'en' => require_once __DIR__ . '/i18n/en.php',
        'fr' => require_once __DIR__ . '/i18n/fr.php',
        'de' => require_once __DIR__ . '/i18n/de.php',
    ],
]);
