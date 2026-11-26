<template>
    <k-panel-inside>
        <k-view class="k-backups-view">
            <k-header class="k-backups-view-header">
                {{ title }}

                <template #buttons>
                    <k-button-group>
                    <k-button v-if="creationStatus == 'default'" icon="add" @click="createBackup" variant="filled" :text="$t('backups.create')" size="sm" />
                    <k-button v-else-if="creationStatus == 'progress'" icon="loader" :disabled="true" variant="filled" :text="$t('backups.create.process')" size="sm" />
                    <k-button v-else-if="creationStatus == 'success'" icon="check" :disabled="true" theme="positive" variant="filled" :text="$t('backups.create.success')" size="sm" />
                    <k-button v-else-if="creationStatus == 'error'" icon="alert" :disabled="true" theme="negative" variant="filled" :text="$t('backups.create.error')" size="sm" />

                    <template v-if="backups.length">
                        <k-button icon="download" @click="copyAndDownload('latest')" v-if="downloading !== 'latest'" variant="filled" :text="$t('backups.download.latest')" size="sm" />
                        <k-button icon="loader" :disabled="true" v-else variant="filled" :text="$t('backups.downloading')" size="sm" />

                        <k-button icon="trash" @click="openBackupsDeleteDialog" variant="filled" :text="$t('backups.delete.some')" size="sm" />
                    </template>
                    </k-button-group>
                </template>
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
        </k-view>
    </k-panel-inside>
</template>

<script>
export default {
    props: {
        backups: { type: Array, default: () => [] },
        title: String,
        limit: { type: [Number, String, Boolean], default: 20 },
    },
    data() {
        return {
            downloading: false,
            creationStatus: 'default',
            page: 1,
            statusTimer: null,
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
                    text: this.$t('backups.download'),
                    icon: 'download',
                    click: 'download'
                },
                "-",
                {
                    text: this.$t('delete'),
                    icon: 'trash',
                    click: 'delete'
                }
            ];
        },
        pageLimit() {
            const limit = Number(this.limit);
            return Number.isInteger(limit) && limit > 0 ? limit : 20;
        },
        pagination() {
            const page = Math.min(this.page, Math.max(1, Math.ceil(this.backups.length / this.pageLimit)));
            const offset = (page - 1) * this.pageLimit;

            return {
                page: page,
                offset: offset,
                limit: this.pageLimit,
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
        this.deletePublicBackups()
    },
    beforeUnmount() {
        clearTimeout(this.statusTimer);
        this.deletePublicBackups();
    },
    watch: {
        "pagination.page"(page) { this.page = page; }
    },
    methods: {

        /* Header
        -------------------*/

        async createBackup() {
            this.creationStatus = 'progress';

            try {
                const response = await this.$api.get('backups/create-backup');
                await this.$panel.reload();
                this.setCreationStatus(response.status, true);
            }
            catch(error) {
                this.setCreationStatus('error', true);
            }
        },
        setCreationStatus(status, resetAfter = true) {
            this.creationStatus = status == 200 ? 'success' : 'error';

            if(resetAfter) {
                clearTimeout(this.statusTimer);
                this.statusTimer = setTimeout(() => {
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
            if(this.downloading || (filename === 'latest' && !this.backups.length)) return;
            this.downloading = filename;
            try {
                filename = filename === 'latest' ? this.backups[0].filename : filename;
                const response = await this.$api.get('backups/copy-to-assets', {filename});
                if(!response.url) throw new Error(this.$t('backups.request.error'));
                window.location = response.url;
            } 
            catch (error) {
                this.$panel.notification.error(error.message);
            } 
            finally {
                this.downloading = false;
            }
        },

        /* Dialogs
        -------------------*/

        openBackupDeleteDialog(backup) {
            return this.$panel.dialog.open({
                component: 'k-backups-delete-dialog',
                props: { filename: backup.filename, visible: true }
            });
        },
        openBackupsDeleteDialog() {
            return this.$panel.dialog.open({
                component: 'k-backups-delete-multiple-dialog',
                props: { visible: true }
            });
        },

        /* Cleanup
        -------------------*/

        deletePublicBackups() {
            return this.$api.post('backups/delete-public-backups').catch(error => {
                console.warn('[backups] Temporary download cleanup failed', error);
            });
        },
    }
};
</script>

<style lang="scss">
  @use '../assets/css/styles.scss';
</style>