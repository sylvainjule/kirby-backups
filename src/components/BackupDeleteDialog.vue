<template>
    <k-dialog size="medium" ref="dialog" theme="negative" icon="trash" button="Yes, delete it" @close="resetBackup" @cancel="resetBackup" @submit="deleteBackup">
        <k-text>
            You are about to delete <strong>{{ filename }}</strong>
        </k-text>
    </k-dialog>
</template>

<script>
export default {
    data() {
        return {
            backup: null,
        }
    },
    computed: {
        filename() {
            return this.backup ? this.backup.filename : ''
        }
    },
    methods: {
        open(backup) {
            this.backup = backup
            this.$refs.dialog.open()
        },
        resetBackup() {
            this.backup = null
        },
        deleteBackup() {
            this.$api.post('backups/delete-backup', {filename: this.backup.filename})
                .then(response => {
                    response.deleted && this.$emit('deleted', this.backup.filename)
                    this.$refs.dialog.close()
                })
        },
    }
};
</script>
