<?php

return [
    'view.backups'       => 'Sauvegardes',
    'backups.pluralized' => [1 => '{{ count }} Sauvegarde', '{{ count }} Sauvegardes'],
    'backups.placeholder'    => 'Il n\'y a actuellement aucune sauvegarde',

    'backups.filename'     => 'Nom du fichier',
    'backups.size'         => 'Poids',
    'backups.created'      => 'Date de création',

    'backups.create'         => 'Créer une nouvelle sauvegarde',
    'backups.create.process' => 'Sauvegarde en cours',
    'backups.create.success' => 'La sauvegarde a été réalisée avec succès',
    'backups.create.error'   => 'Une erreur s\'est produite lors de la sauvegarde',

    'backups.download'                    => 'Télécharger',
    'backups.downloading'                 => 'Préparation',
    'backups.download.latest'             => 'Télécharger la dernière sauvegarde',
    'backups.delete.some'                 => 'Supprimer des sauvegardes',
    'backups.delete.prefix'               => 'Vous avez demandé la suppression de',
    'backups.delete.button'               => 'Oui, supprimez-la',
    'backups.delete.multiple.button'      => 'Oui, supprimez-les',
    'backups.delete.multiple.question'    => 'Quelles sauvegardes voulez-vous supprimer ?',
    'backups.delete.multiple.placeholder' => 'Choissisez une période',
    'backups.delete.multiple.week'        => 'Supprimer les sauvegardes de plus d\'1 semaine',
    'backups.delete.multiple.month'       => 'Supprimer les sauvegardes de plus d\'1 mois',
    'backups.delete.multiple.half'        => 'Supprimer les sauvegardes de plus de 6 mois',
    'backups.delete.multiple.year'        => 'Supprimer les sauvegardes de plus d\'1 an',
    'backups.delete.warning.noMatch'      => 'Aucune sauvegarde ne correspond à votre choix, il n\'y a rien à supprimer.',
    'backups.delete.warning.deletable'    => [
        'Aucune sauvegarde ne sera supprimée. ',
        '1 sauvegarde sera supprimée. ',
        '{{ count }} sauvegardes seront supprimées. '
    ],
    'backups.delete.warning.remaining'  => [
        '<strong>Il ne restera plus aucune sauvegarde.</strong>',
        'Il restera 1 sauvegarde. ',
        'Il restera {{ count }} sauvegardes. '
    ],
    'backups.delete.warning.question'  => [
        'Voulez-vous tout de même continuer ?',
        'Voulez-vous continuer?',
        'Voulez-vous continuer?',
    ],
];
