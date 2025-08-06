# Kirby Backups

This plugin allows you to create, download and manage backups from a dedicated View. Works together with [kirby-janitor](https://github.com/bnomei/kirby3-janitor).

![screenshot-k5](https://github.com/user-attachments/assets/33bcced6-ca33-4058-8c42-cd89481dc2ad)

<br/>

## Overview

> This plugin is completely free and published under the MIT license. However, if you are using it in a commercial project and want to help me keep up with maintenance, you can consider [making a donation of your choice](https://www.paypal.me/sylvainjl).

- [1. Installation](#1-installation)
- [2. Usage](#2-usage)
- [3. License](#3-license)
- [4. Credits](#4-credits)

<br/>

## 1. Installation

> Kirby 3: up to 1.0.3. Kirby 4: up to 1.0.5. Kirby 5: 1.1.0+.<br>
> You also **must install [kirby-janitor](https://github.com/bnomei/kirby3-janitor)** (and the CLI per janitor's instructions) for this plugin to work.<br>
> For older releases, install the kirby-janitor release from the same timeframe.

Download and copy this repository to ```/site/plugins/backups```

Alternatively, you can install it with composer: ```composer require sylvainjule/backups```

<br/>

## 2. Usage

The plugin will work out of the box, no need for additionnal setup.

You can, however, change the prefix of the backups' filename (default is `backup-{TIMESTAMP}.zip`)

```php
// site/config.php
return [
    'sylvainjule.backups.prefix' => 'backup-',
];
```

Any backup, created either with this plugin or with any of the janitor's options (CLI, CRON job, etc), will now show up in the Backups view.

Janitor stores the backups in a `site/backups` folder. This folder isn't public and we should keep it that way. Therefore, anytime a user triggers a *Download* button, the plugin will create a copy of the given backup in a `backups-temp` folder and expose an url from there.

When the user leaves the view, copies will be deleted.

This default public `assets/backup-temp` folder can be changed to any name you'd like:

```php
// site/config.php
return [
    // backups will be copied in assets/my-secretly-public-backups
    'sylvainjule.backups.publicFolder' => 'my-secretly-public-backups',
];
```

If you want more control over where backups are created / copied, you can set both paths with their dedicated option (if you change the public path, make sure to fill both the `publicPath` and `publicUrl` options):

```php
// site/config.php
return [
    // if filled, backups will be created here
    'sylvainjule.backups.path' => '{{ kirby.root("site") }}/my-custom-folder',
    // if filled, backups will be copied here for downloading (in this case, it will take precedence over the publicFolder option)
    'sylvainjule.backups.publicPath' => '{{ kirby.root("assets") }}/my-custom-folder',
    'sylvainjule.backups.publicUrl'  => '{{ kirby.url("assets") }}/my-custom-folder',
];
```

You can enforce a maximum number of backups by setting the `maximum` option to any integer (default is `false`).
If this number is reached the oldest backup will be deleted automatically whenever a new backup is created.
If included in a client website, you should include a note specifying an expected frequency of backup creation / deletion (or set up a CRON job).

```php
// site/config.php
return [
    'sylvainjule.backups.maximum' => 5,
];
```

If you want to create backups from a CRON job and still benefit from this `maximum` option, the plugin exposes a tweaked janitor webhook:

```
https://your.domain/backups-webhook/{janitor.secret}/create-backup
```

If you have set the `maximum` option to a high amount, you can choose the pagination limit of the table with the `limit` option (default is `20`).

```php
// site/config.php
return [
    'sylvainjule.backups.limit' => 20,
];
```


There's also a way to disable the "Backups" menu-item for specific user roles:

```yml
# site/blueprints/users/{role}.yml
permissions:
  access:
    backups: false
```

<br/>

## 3. License

MIT

<br/>

## 4. Credits

- The plugin relies on the Janitor plugin by [@bnomei](https://github.com/bnomei), who tweaked it a bit in order to make the development of Backups easier. 🙏
