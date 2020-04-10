<?php

namespace SylvainJule;

use Kirby\Toolkit\Dir;
use Kirby\Toolkit\F;

class Backups {

    protected $dir;
    protected $publicDir;
    protected $publicUrl;

    function __construct() {
        $this->dir = \Bnomei\BackupZipJob::directory() . '/';
        $this->publicDir = kirby()->root('assets') . '/backups/';
        $this->publicUrl = kirby()->url('assets')  . '/backups/';
    }

    public function getBackupsArray($toApi = false) {
        $backups = Dir::read($this->dir);
        rsort($backups);

        if($toApi) {
            $backups = array_map(array($this, 'toResponseArray'), $backups);
        }

        return $backups;
    }

    public function getToDeleteArray($stamp) {
        $backups = $this->getBackupsArray();

        $backups = array_filter($backups, function($filename) use($stamp) {
            return $this->isOlderThan($filename, $stamp);
        });

        return $backups;
    }

    public function getNewestBackup() {
        $backups = $this->getBackupsArray(true);
        return $backups[0];
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

    public function simulateDeletion($period) {
        $stamp    = $this->toTimestamp($period);
        $backups  = $this->getBackupsArray();
        $toDelete = $this->getToDeleteArray($stamp);

        $backupsCount   = count($backups);
        $toDeleteCount  = count($toDelete);
        $remainingCount = $backupsCount - $toDeleteCount;

        if($toDeleteCount) {
            $firstStr  = $toDeleteCount  == 1 ? '1 backup will be deleted. '         : $toDeleteCount .' backups will be deleted. ';
            $secondStr = $remainingCount == 0 ? '<strong>There will be no backup left.</strong> '     : 'There will be '. $remainingCount .' backups left. ';
            $secondStr = $remainingCount == 1 ? 'There will be 1 backup left. '      : $secondStr;
            $thirdStr  = $remainingCount == 0 ? 'Do you want to delete them anyway?' : 'Do you want to continue?';

            $text = $firstStr . $secondStr . '<br>' . $thirdStr;
        }
        else {
            $text = 'No backup matches your criteria, there is nothing to delete.';
        }

        return [
            'count' => $toDeleteCount,
            'text'  => $text
        ];
    }

    public function deleteBackups($period) {
        $stamp    = $this->toTimestamp($period);
        $toDelete = $this->getToDeleteArray($stamp);

        foreach($toDelete as $filename) {
            F::remove($this->dir . $filename);
        }

        return $toDelete;
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
