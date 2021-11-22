<?php

return [
    'view.backups'       => 'Backups',
    'backups.pluralized' => [1 => '{{ count }} Backup', '{{ count }} Backups'],

    'backups.placeholder'    => 'Es ist kein anzeigbares Backup vorhanden',

    'backups.filename'     => 'Dateiname',
    'backups.size'         => 'Größe',
    'backups.created'      => 'Erstellt',

    'backups.create'         => 'Ein neues Backup erstellen',
    'backups.create.process' => 'Das Backup wird erstellt',
    'backups.create.success' => 'Das Backup wurde erfolgreich erstellt',
    'backups.create.error'   => 'Es ist ein Fehler bei der Backup-Erstellung aufgetreten',

    'backups.download'                    => 'Herunterladen',
    'backups.downloading'                 => 'Wird heruntergeladen',
    'backups.download.latest'             => 'Das letzte Backup herunterladen',
    'backups.delete.some'                 => 'Ältere Backups löschen',
    'backups.delete.prefix'               => 'Du bist dabei folgendes zu löschen:',
    'backups.delete.button'               => 'Ja, lösche es',
    'backups.delete.multiple.button'      => 'Ja, lösche sie',
    'backups.delete.multiple.question'    => 'Welche Backups möchtest du löschen?',
    'backups.delete.multiple.placeholder' => 'Bitte wähle einen Zeitraum',
    'backups.delete.multiple.week'        => 'Lösche Backups älter als 1 Woche',
    'backups.delete.multiple.month'       => 'Lösche Backups älter als 1 Monat',
    'backups.delete.multiple.half'        => 'Lösche Backups älter als 6 Monate',
    'backups.delete.multiple.year'        => 'Lösche Backups älter als 1 Jahr',
    'backups.delete.warning.noMatch'      => 'Kein Backup entspricht deiner Auswahl, es gibt nichts zu löschen.',
    'backups.delete.warning.deletable'    => [
        'Kein Backup wird gelöscht. ',
        '1 Backup wird gelöscht. ',
        '{{ count }} Backups werden gelöscht. '
    ],
    'backups.delete.warning.remaining'  => [
        '<strong>Es wird kein Backup verbleiben.</strong>',
        'Es wird 1 Backup verbleiben. ',
        'Es werden {{ count }} Backups verbleiben. '
    ],
    'backups.delete.warning.question'  => [
        'Willst du sie dennoch löschen?',
        'Willst du fortfahren?',
        'Willst du fortfahren?'
    ],
];
