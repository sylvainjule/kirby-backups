<template>
    <li class="backup-entry">
        <div class="backup-filename">
            <k-icon type="backup" />
            <span>{{ backup.filename }}</span>
        </div>
        <div class="backup-size">{{ backup.size }}</div>
        <div class="backup-date">{{ backup.date }}</div>
        <div class="backup-actions">
            <k-button class="backup-download" icon="download" @click="$emit('download', backup.filename)" v-if="!downloading" size="sm" variant="filled">{{ $t('backups.download') }}</k-button>
            <k-button class="backup-download" icon="backupsLoader" :disabled="true" size="sm" variant="filled" v-else>{{ $t('backups.downloading') }}</k-button>
            <k-button class="backup-delete" icon="trash" @click="$emit('delete')" theme="negative" variant="filled" size="sm" />
        </div>
    </li>
</template>

<script>
export default {
    props: ['backup', 'downloading'],
};
</script>
