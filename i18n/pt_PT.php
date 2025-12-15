<?php

return [
    'view.backups'       => 'Backups',
    'backups.pluralized' => [1 => '{{ count }} Backup', '{{ count }} Backups'],

    'backups.placeholder'    => 'Nenhum backup ainda',

    'backups.filename'     => 'Nome do Ficheiro',
    'backups.size'         => 'Tamanho',
    'backups.created'      => 'Criado',

    'backups.create'         => 'Criar um novo backup',
    'backups.create.process' => 'A criar o backup',
    'backups.create.success' => 'Backup criado com sucesso',
    'backups.create.error'   => 'Ocorreu um error ao criar o backup',

    'backups.download'                    => 'Download',
    'backups.downloading'                 => 'Downloading',
    'backups.download.latest'             => 'Download do último backup',
    'backups.delete.some'                 => 'Eliminar alguns backups',
    'backups.delete.prefix'               => 'Está prestes a eliminar',
    'backups.delete.button'               => 'Sim, eliminar',
    'backups.delete.multiple.button'      => 'Sim, eliminar',
    'backups.delete.multiple.question'    => 'Quais os backups que pretende eliminar?',
    'backups.delete.multiple.placeholder' => 'Por favor, selecione um período',
    'backups.delete.multiple.week'        => 'Eliminar backups com mais de uma semana',
    'backups.delete.multiple.month'       => 'Eliminar backups com mais de um mês',
    'backups.delete.multiple.half'        => 'Eliminar backups com mais de 6 meses',
    'backups.delete.multiple.year'        => 'Eliminar backups com mais de um ano',
    'backups.delete.warning.noMatch'      => 'Nenhum backup corresponde aos seus critérios, não há nada a eliminar.',
    'backups.delete.warning.deletable'    => [
        'Nenhum backup será eliminado. ',
        '1 backup será eliminado. ',
        '{{ count }} backups serão eliminados. '
    ],
    'backups.delete.warning.remaining'  => [
        '<strong>Não vai restar nenhum backup.</strong>',
        'Vai restar 1 backup. ',
        'Vão restar {{ count }} backups. '
    ],
    'backups.delete.warning.question'  => [
        'Pretende eliminá-los mesmo assim?',
        'Pretende continuar?',
        'Pretende continuar?'
    ],
];
