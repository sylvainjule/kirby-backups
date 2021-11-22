<?php

return [
    'view.backups'       => 'Backups',
    'backups.pluralized' => [1 => '{{ count }} Backup', '{{ count }} Backups'],

    'backups.placeholder'    => 'There is no backup to display',

    'backups.filename'     => 'Filename',
    'backups.size'         => 'Size',
    'backups.created'      => 'Created',

    'backups.create'         => 'Create a new backup',
    'backups.create.process' => 'Creating the backup',
    'backups.create.success' => 'Backup successfully created',
    'backups.create.error'   => 'An error occurred during the backup creation',

    'backups.download'                    => 'Download',
    'backups.downloading'                 => 'Downloading',
    'backups.download.latest'             => 'Download the latest backup',
    'backups.delete.some'                 => 'Delete some backups',
    'backups.delete.prefix'               => 'You are about to delete',
    'backups.delete.button'               => 'Yes, delete it',
    'backups.delete.multiple.button'      => 'Yes, delete them',
    'backups.delete.multiple.question'    => 'Which backups do you want to delete?',
    'backups.delete.multiple.placeholder' => 'Please select a period',
    'backups.delete.multiple.week'        => 'Delete backups older than a week',
    'backups.delete.multiple.month'       => 'Delete backups older than a month',
    'backups.delete.multiple.half'        => 'Delete backups older than 6 months',
    'backups.delete.multiple.year'        => 'Delete backups older than a year',
    'backups.delete.warning.noMatch'      => 'No backup matches your criteria, there is nothing to delete.',
    'backups.delete.warning.deletable'    => [
        'No backup will be deleted. ',
        '1 backup will be deleted. ',
        '{{ count }} backups will be deleted. '
    ],
    'backups.delete.warning.remaining'  => [
        '<strong>There will be no backup left.</strong>',
        'There will be 1 backup left. ',
        'There will be {{ count }} backups left. '
    ],
    'backups.delete.warning.question'  => [
        'Do you want to delete them anyway?',
        'Do you want to continue?',
        'Do you want to continue?'
    ],
];
