<template>
    <k-panel-inside>
        <k-view class="k-backups-view">
            <k-header class="k-backups-view-header">
                {{ title }}

                <k-button-group slot="buttons" v-if="backups.length">
                <k-button v-if="creationStatus == 'default'" icon="add" @click="createBackup" variant="filled" :text="$t('backups.create')" size="sm" />
                <k-button v-else-if="creationStatus == 'progress'" icon="loader" :disabled="true" variant="filled" :text="$t('backups.create.process')" size="sm" />
                <k-button v-else-if="creationStatus == 'success'" icon="check" :disabled="true" theme="positive" variant="filled" :text="$t('backups.create.success')" size="sm" />
                <k-button v-else-if="creationStatus == 'error'" icon="alert" :disabled="true" theme="negative" variant="filled" :text="$t('backups.create.error')" size="sm" />

                <k-button icon="download" @click="copyAndDownload('latest')" v-if="downloading !== 'latest'" variant="filled" :text="$t('backups.download.latest')" size="sm" />
                <k-button icon="loader" :disabled="true" v-else variant="filled" :text="$t('backups.downloading')" size="sm" />

                <k-button icon="trash" @click="openBackupsDeleteDialog" variant="filled" :text="$t('backups.delete.some')" size="sm" />
                </k-button-group>
            </k-header>

            <k-table :columns="columns" 
                     :options="options"
                     :empty="$t('backups.placeholder')" 
                     :pagination="pagination" 
                     :index="index" 
                     :sortable="false" 
                     :rows="paginatedBackups" 
                     @paginate="page = $event.page"
                     @option="onOption">
            </k-table>

            <backup-delete-dialog ref="backup-delete" @deleted="$reload"/>
            <backups-delete-dialog ref="backups-delete" @deleted="$reload" :backups="backups"/>
        </k-view>
    </k-panel-inside>
</template>

<script>
import BackupDeleteDialog  from './BackupDeleteDialog.vue'
import BackupsDeleteDialog from './BackupsDeleteDialog.vue'

export default {
    components: {
        'backup-delete-dialog': BackupDeleteDialog,
        'backups-delete-dialog': BackupsDeleteDialog,
    },
    props: {
        backups: Array,
        title: String,
        limit: Number,
    },
    data() {
        return {
            downloading: false,
            creationStatus: 'default',
            page: 1,
        }
    },
    computed: {
        columns() {
            return {
                filename: { label: this.$t('backups.filename'), type: "text" },
                size:     { label: this.$t('backups.size'),     type: "text" },
                date:     { label: this.$t('backups.created'),  type: "text" }
            }
        },
        options() {
            return [
                {
                    text: 'Download',
                    icon: 'download',
                    click: 'download'
                },
                "-",
                {
                    text: 'Delete',
                    icon: 'trash',
                    click: 'delete'
                }
            ];
        },
        pagination() {
            let offset = 0;

            if (this.limit) {
                offset = (this.page - 1) * this.limit;
            }

            return {
                page: this.page,
                offset: offset,
                limit: this.limit,
                total: this.backups.length,
                align: 'center',
                details: true
            };
        },
        index() {
            return (this.pagination.page - 1) * this.pagination.limit + 1;
        },
        paginatedBackups() {
            return this.backups.slice(
                this.index - 1,
                this.pagination.limit * this.pagination.page,
            );
        },
    },
    mounted() {
        window.beforeunload = this.deletePublicBackups()
    },
    destroyed() {
        this.deletePublicBackups()
    },
    methods: {

        /* Header
        -------------------*/

        async createBackup() {
            this.creationStatus = 'progress';

            try {
                const response = await this.$api.get('backups/create-backup');
                await this.$reload();
                this.setCreationStatus(response.status, true);
            }
            catch(error) {
                this.setCreationStatus('error', true);
            }
        },
        setCreationStatus(status, resetAfter = true) {
            this.creationStatus = status == 200 ? 'success' : 'error';

            if(resetAfter) {
                setTimeout(() => {
                    this.creationStatus = 'default'
                }, 2000);
            }
        },

        /* Options
        -------------------*/

        onOption(option, backup, backupIndex) {
            if(option == 'download') {
                this.copyAndDownload(backup.filename)
            }
            else if(option == 'delete') {
                this.openBackupDeleteDialog(backup)
            }
        },
        async copyAndDownload(filename) {
            this.downloading = filename

            filename = filename == 'latest' ? this.backups[0].filename : filename

            const response = await this.$api.get('backups/copy-to-assets', {filename: filename})

            if (response.url) {
                window.location = response.url
            }

            this.downloading = false
        },

        /* Dialogs
        -------------------*/

        openBackupDeleteDialog(backup) {
            this.$refs['backup-delete'].open(backup)
        },
        openBackupsDeleteDialog() {
            this.$refs['backups-delete'].open()
        },

        /* Cleanup
        -------------------*/

        deletePublicBackups() {
            this.$api.post('backups/delete-public-backups');
        },
    }
};
</script>

<style lang="scss">
  @import '../assets/css/styles.scss';
</style>
