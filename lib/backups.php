<?php

namespace SylvainJule;

use Kirby\Toolkit\Dir;
use Kirby\Toolkit\F;

class Backups {

    protected $dir;
    protected $publicDir;
    protected $publicUrl;
    protected $maximum;
    protected $prefix;

    function __construct() {
        $this->dir = realpath(kirby()->roots()->accounts() . '/../') .'/backups/';
        $this->publicDir = kirby()->root('assets') . '/'. option('sylvainjule.backups.publicFolder')  .'/';
        $this->publicUrl = kirby()->url('assets') . '/'. option('sylvainjule.backups.publicFolder')  .'/';
        $this->maximum   = kirby()->option('sylvainjule.backups.maximum');
        $this->prefix    = kirby()->option('sylvainjule.backups.prefix');
    }

    public function getBackupsArray($toApi = false) {
        $backups = Dir::read($this->dir);
        rsort($backups);

        if($toApi) {
            $backups = array_map(array($this, 'toResponseArray'), $backups);
        }

        return $backups;
    }

    public function getBackupsCount() {
        return count($this->getBackupsArray());
    }

    public function getToDeleteArray($stamp) {
        $backups = $this->getBackupsArray();

        $backups = array_filter($backups, function($filename) use($stamp) {
            return $this->isOlderThan($filename, $stamp);
        });

        return $backups;
    }

    public function createBackup() {
        $count  = $this->getBackupsCount();
        $output  = realpath(kirby()->roots()->accounts() .'/../') . '/backups/'. $this->prefix .'{{ timestamp }}.zip';

        if($this->maximum && $count == $this->maximum) {
            $this->deleteOldestBackup();
        }

        return janitor()->command('janitor:backupzip --output '. $output .' --quiet');
    }

    public function deleteBackup($filename) {
        $path     = $this->dir . $filename;
        $deleted  = false;

        if(F::exists($path)) {
            F::remove($path);
            $deleted = true;
        }

        return $deleted;
    }

    public function deleteOldestBackup() {
        $backups  = $this->getBackupsArray();
        $filename = end($backups);

        $this->deleteBackup($filename);
    }

    public function simulateDeletion($period) {
        $stamp    = $this->toTimestamp($period);
        $backups  = $this->getBackupsArray();
        $toDelete = $this->getToDeleteArray($stamp);

        $backupsCount   = count($backups);
        $toDeleteCount  = count($toDelete);
        $remainingCount = $backupsCount - $toDeleteCount;

        if($toDeleteCount) {
            $firstStr  = tc('backups.delete.warning.deletable', $toDeleteCount);
            $secondStr = tc('backups.delete.warning.remaining', $remainingCount);
            $thirdStr  = tc('backups.delete.warning.question',  $remainingCount);

            $text = $firstStr . $secondStr . '<br>' . $thirdStr;
        }
        else {
            $text = t('backups.delete.warning.noMatch');
        }

        return [
            'count' => $toDeleteCount,
            'text'  => $text
        ];
    }

    public function deleteBackups($period) {
        $stamp    = $this->toTimestamp($period);
        $toDelete = $this->getToDeleteArray($stamp);
        $deleted  = [];

        foreach($toDelete as $filename) {
            F::remove($this->dir . $filename);
            $deleted[] = $filename;
        }

        return $deleted;
    }

    public function copyToAssets($filename) {
        $path       = $this->dir . $filename;
        $publicPath = $this->publicDir . $filename;
        $publicUrl  = $this->publicUrl . $filename;

        F::copy($path, $publicPath);

        return $publicUrl;
    }

    public function deletePublicBackups() {
        if(Dir::exists($this->publicDir)) {
            Dir::remove($this->publicDir);
        }
        return [];
    }

    public function toResponseArray($filename) {
        return [
            'filename' => $filename,
            'size'     => F::niceSize($this->dir . $filename),
            'date'     => date('d/m/Y, H:i:s', F::modified($this->dir . $filename))
        ];
    }

    public function isOlderThan($filename, $stamp) {
        return F::modified($this->dir . $filename) < $stamp;
    }
    public function toTimestamp($period) {
        $period = $period == 'half' ? '-6 months' : '-1 '. $period;
        return strtotime($period);
    }

}
