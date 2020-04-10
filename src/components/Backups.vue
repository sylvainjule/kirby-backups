<template>
    <k-view class="k-backups-view">
        <k-header class="k-backups-view-header">
            <span v-if="!listLoading">{{ backups.length }}</span> Backups

            <k-button-group slot="left">
                <k-button v-if="creationStatus == 'default'" icon="add" @click="createBackup">Create a new backup</k-button>
                <k-button v-else-if="creationStatus == 'progress'" icon="backupsLoader" :disabled="true">Creating the backup</k-button>
                <k-button v-else-if="creationStatus == 'success'" icon="check" :disabled="true" theme="positive">Backup successfully created</k-button>
                <k-button v-else-if="creationStatus == 'error'" icon="alert" :disabled="true" theme="negative">An error occurred during the backup creation</k-button>
            </k-button-group>

            <k-button-group slot="right" v-if="backups.length">
                <k-button icon="download" @click="copyAndDownload('latest')" v-if="downloading !== 'latest'">Download the latest backup</k-button>
                <k-button icon="backupsLoader" :disabled="true" v-else>Downloading</k-button>
                <k-button icon="trash" @click="openBackupsDeleteDialog">Delete some backups</k-button>
            </k-button-group>
        </k-header>

        <section class="backups-section" v-if="backups.length">
            <header class="backups-header">
                <div class="backup-filename">Filename</div>
                <div class="backup-size">Size</div>
                <div class="backup-date">Created</div>
            </header>
            <ul class="backups-list">
                <backup-entry v-for="backup in backups" :key="backup.filename" :backup="backup" :downloading="downloading == backup.filename" @download="copyAndDownload" @deleteClicked="openBackupDeleteDialog(backup)" />
            </ul>
        </section>
        <div class="backups-placeholder" v-else>
            <div class="backups-placeholder-loader" v-if="listLoading"><k-icon type="backupsLoader" /></div>
            <div class="backups-placeholder-empty" v-else>There is no backup to display</div>
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
                })
                .catch(error => {
                    this.setCreationStatus('error', true);
                })
        },
        setCreationStatus(status, resetAfter = true) {
            this.creationStatus = status == 200 ? 'success' : 'error';

            if(status == 200) this.listNewBackup();

            if(resetAfter) {
                setTimeout(() => {
                    this.creationStatus = 'default'
                }, 2000);
            }
        },
        listNewBackup() {
            this.$api.get('backups/get-newest-backup')
                .then(response => { response && this.backups.unshift(response); })
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
