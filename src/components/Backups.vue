<template>
    <k-view class="k-backups-view">
        <k-header class="k-backups-view-header">
            <span v-if="listLoading">{{ $t('view.backups') }}</span>
            <span v-else>{{ backups.length }} {{ $t('backups.pluralized', {}, backups.length) }}</span>

            <k-button-group slot="left">
                <k-button v-if="creationStatus == 'default'" icon="add" @click="createBackup">{{ $t('backups.create') }}</k-button>
                <k-button v-else-if="creationStatus == 'progress'" icon="backupsLoader" :disabled="true">{{ $t('backups.create.process') }}</k-button>
                <k-button v-else-if="creationStatus == 'success'" icon="check" :disabled="true" theme="positive">{{ $t('backups.create.success') }}</k-button>
                <k-button v-else-if="creationStatus == 'error'" icon="alert" :disabled="true" theme="negative">{{ $t('backups.create.error') }}</k-button>
            </k-button-group>

            <k-button-group slot="right" v-if="backups.length">
                <k-button icon="download" @click="copyAndDownload('latest')" v-if="downloading !== 'latest'">{{ $t('backups.download.latest') }}</k-button>
                <k-button icon="backupsLoader" :disabled="true" v-else>{{ $t('backups.downloading') }}</k-button>
                <k-button icon="trash" @click="openBackupsDeleteDialog">{{ $t('backups.delete.some') }}</k-button>
            </k-button-group>
        </k-header>

        <section class="backups-section" v-if="backups.length">
            <header class="backups-header">
                <div class="backup-filename">{{ $t('backups.filename') }}</div>
                <div class="backup-size">{{ $t('backups.size') }}</div>
                <div class="backup-date">{{ $t('backups.created') }}</div>
            </header>
            <ul class="backups-list">
                <backup-entry v-for="backup in backups" :key="backup.filename" :backup="backup" :downloading="downloading == backup.filename" @download="copyAndDownload" @delete="openBackupDeleteDialog(backup)" />
            </ul>
        </section>
        <div class="backups-placeholder" v-else>
            <div class="backups-placeholder-loader" v-if="listLoading"><k-icon type="backupsLoader" /></div>
            <div class="backups-placeholder-empty" v-else>{{ $t('backups.placeholder') }}</div>
        </div>

        <backup-delete-dialog ref="backup-delete" @deleted="onBackupDeleted"/>
        <backups-delete-dialog ref="backups-delete" @deleted="onBackupsDeleted" :backups="backups"/>
    </k-view>
</template>

<script>
import BackupEntry         from './BackupEntry.vue'
import BackupDeleteDialog  from './BackupDeleteDialog.vue'
import BackupsDeleteDialog from './BackupsDeleteDialog.vue'

export default {
    components: {
        'backup-entry': BackupEntry,
        'backup-delete-dialog': BackupDeleteDialog,
        'backups-delete-dialog': BackupsDeleteDialog,
    },
    data() {
        return {
            backups: [],
            listLoading: true,
            downloading: false,
            creationStatus: 'default',
        }
    },
    mounted() {
        this.getBackups();
        window.beforeunload = this.deletePublicBackups();
    },
    destroyed() {
        this.deletePublicBackups();
    },
    methods: {
        getBackups() {
            this.$api.get('backups/get-backups-list')
                .then(response => {
                    this.backups = response
                    this.listLoading = false
                })
        },
        deletePublicBackups() {
            this.$api.post('backups/delete-public-backups');
        },
        createBackup() {
            this.creationStatus = 'progress';

            this.$api.get('plugin-janitor/backupZip')
                .then(response => {
                    this.setCreationStatus(response.status, true)

                    if(response.status == 200) {
                        let newBackup = {
                            filename: response.filename + '.zip',
                            size: response.nicesize,
                            date: response.modified
                        }

                        this.backups.unshift(newBackup)
                    }
                })
                .catch(error => {
                    this.setCreationStatus('error', true);
                })
        },
        setCreationStatus(status, resetAfter = true) {
            this.creationStatus = status == 200 ? 'success' : 'error';

            if(resetAfter) {
                setTimeout(() => {
                    this.creationStatus = 'default'
                }, 2000);
            }
        },

        /* Download
        -------------------*/

        copyAndDownload(filename) {
            this.downloading = filename

            filename = filename == 'latest' ? this.backups[0].filename : filename

            this.$api.get('backups/copy-to-assets', {filename: filename})
                .then(response => {
                    if(response.url) window.location = response.url
                    this.downloading = false
                })
        },

        /* Dialogs
        -------------------*/

        openBackupDeleteDialog(backup) {
            this.$refs['backup-delete'].open(backup)
        },
        onBackupDeleted(filename) {
            this.backups = this.backups.filter(el => el.filename !== filename)
        },
        openBackupsDeleteDialog() {
            this.$refs['backups-delete'].open()
        },
        onBackupsDeleted(filenames) {
            this.backups = this.backups.filter(el => !filenames.includes(el.filename));
        },
    }
};
</script>

<style lang="scss">
    @import '../assets/css/styles.scss'
</style>
