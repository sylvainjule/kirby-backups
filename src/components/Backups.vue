<template>
  <k-inside>
    <k-view class="k-backups-view">
      <k-header class="k-backups-view-header">
        {{ title }}

        <k-button-group slot="left">
          <k-button v-if="creationStatus == 'default'" icon="add" @click="createBackup" variant="filled" :text="$t('backups.create')" size="sm" />
          <k-button v-else-if="creationStatus == 'progress'" icon="loader" :disabled="true" variant="filled" :text="$t('backups.create.process')" size="sm" />
          <k-button v-else-if="creationStatus == 'success'" icon="check" :disabled="true" theme="positive" variant="filled" :text="$t('backups.create.success')" size="sm" />
          <k-button v-else-if="creationStatus == 'error'" icon="alert" :disabled="true" theme="negative" variant="filled" :text="$t('backups.create.error')" size="sm" />
        </k-button-group>

        <k-button-group slot="right" v-if="backups.length">
          <k-button icon="download" @click="copyAndDownload('latest')" v-if="downloading !== 'latest'" variant="filled" :text="$t('backups.download.latest')" size="sm" />
          <k-button icon="loader" :disabled="true" v-else variant="filled" :text="$t('backups.downloading')" size="sm" />
          <k-button icon="trash" @click="openBackupsDeleteDialog" variant="filled" :text="$t('backups.delete.some')" size="sm" />
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
        <div class="backups-placeholder-empty">{{ $t('backups.placeholder') }}</div>
      </div>

      <backup-delete-dialog ref="backup-delete" @deleted="$reload"/>
      <backups-delete-dialog ref="backups-delete" @deleted="$reload" :backups="backups"/>
    </k-view>
  </k-inside>
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
    props: {
        backups: Array,
        title: String
    },
    data() {
      return {
        downloading: false,
        creationStatus: 'default',
      }
    },
    mounted() {
      window.beforeunload = this.deletePublicBackups();
    },
    destroyed() {
      this.deletePublicBackups();
    },
    methods: {
      deletePublicBackups() {
        this.$api.post('backups/delete-public-backups');
      },
      async createBackup() {
        this.creationStatus = 'progress';

        try {
            const response = await this.$api.get('backups/create-backup');
            await this.$reload();
            this.setCreationStatus(response.status, true);
        } catch(error) {
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

      /* Download
      -------------------*/

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
      }
    }
  };
</script>

<style lang="scss">
  @import '../assets/css/styles.scss';
</style>
