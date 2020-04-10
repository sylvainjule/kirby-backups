<template>
    <li class="backup-entry">
        <div class="backup-filename">
            <k-icon type="backup" />
            <span>{{ backup.filename }}</span>
        </div>
        <div class="backup-size">{{ backup.size }}</div>
        <div class="backup-date">{{ backup.date }}</div>
        <div class="backup-actions">
            <k-button class="backup-download" icon="download" @click="$emit('download', backup.filename)" v-if="!downloading">Download</k-button>
            <k-button class="backup-download" icon="backupsLoader" :disabled="true" v-else>Downloading</k-button>
            <k-button icon="trash" theme="negative" @click="$emit('deleteClicked')"></k-button>
        </div>
    </li>
</template>

<script>
export default {
    props: ['backup', 'downloading'],
};
</script>
