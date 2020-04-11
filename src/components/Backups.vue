<template>
    <k-view class="k-backups-view">
        <k-header class="k-backups-view-header">
            {{ $t('view.backups') }}
            <k-button-group slot="left">
                <k-button v-if="creationStatus == 'default'" icon="add" @click="createBackup">{{ $t('backups.create') }}</k-button>
                <k-button v-else-if="creationStatus == 'progress'" icon="backupsLoader" :disabled="true">{{ $t('backups.create.process') }}</k-button>
                <k-button v-else-if="creationStatus == 'success'" icon="check" :disabled="true" theme="positive">{{ $t('backups.create.success') }}</k-button>
                <k-button v-else-if="creationStatus == 'error'" icon="alert" :disabled="true" theme="negative">{{ $t('backups.create.error') }}</k-button>
            </k-button-group>
            <k-button-group slot="right" v-if="backups.length">
                <k-button icon="download" @click="copyAndDownload('latest')" v-if="downloading !== 'latest'">{{ $t('backups.download.latest') }}</k-button>
                <k-button icon="backupsLoader" :disabled="true" v-else>{{ $t('backups.downloading') }}</k-button>
            </k-button-group>
        </k-header>

        <section class="backups-section" v-if="backups.length">
            <header class="backups-header">
                <div class="backup-filename">{{ $t('backups.filename') }}</div>
                <div class="backup-size">{{ $t('backups.size') }}</div>
                <div class="backup-date">{{ $t('backups.created') }}</div>
            </header>
            <ul class="backups-list">
                <backup-entry v-for="backup in backups" :key="backup.filename" :backup="backup"
                              :downloading="downloading == backup.filename"
                              @download="copyAndDownload"
                              @delete="openDeleteDialog(backup)" />
            </ul>
        </section>
        <div class="backups-placeholder" v-else>
            {{ $t('backups.placeholder') }}
        </div>

        <k-dialog size="medium" ref="delete-dialog" theme="negative" icon="trash" :button="$t('backups.delete.button')"
                  @close="resetDelete" @cancel="resetDelete" @submit="deleteBackup">
            <k-text>{{ $t('backups.delete.prefix') }} <strong>{{ this.toDelete.filename }}</strong></k-text>
        </k-dialog>
    </k-view>
</template>

<script>
import BackupEntry from './BackupEntry.vue'

export default {
    components: {'backup-entry': BackupEntry},
    data() {
        return {
            backups: [],
            downloading: false,
            toDelete: {},
            creationStatus: 'default',
        }
    },
    mounted() {
        this.$api.get('backups/get-backups-list')
            .then(response => {
                this.backups = response;
            })
        window.beforeunload = this.deletePublicBackups();
    },
    destroyed() {
        this.deletePublicBackups();
    },
    methods: {
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
        deletePublicBackups() {
            this.$api.post('backups/delete-public-backups');
        },
        copyAndDownload(filename) {
            this.downloading = filename

            filename = filename == 'latest' ? this.backups[0].filename : filename

            this.$api.get('backups/copy-to-assets', {filename: filename})
                .then(response => {
                    if(response.url) window.location = response.url
                    this.downloading = false
                })
        },
        openDeleteDialog(backup) {
            this.toDelete = backup;
            this.$refs['delete-dialog'].open();
        },
        resetDelete() {
            this.toDelete = {}
        },
        deleteBackup() {
            this.$api.post('backups/delete-backup', {filename: this.toDelete.filename})
                .then(response => {
                    if(response.deleted) {
                        this.backups = this.backups.filter(el => el.filename !== this.toDelete.filename);
                    }
                    this.$refs['delete-dialog'].close();
                })
        }
    }
};
</script>

<style lang="scss">
    @import '../assets/css/styles.scss'
</style>
