<template>
    <k-dialog :visible="visible" size="medium" :submit-button="submitButton"
              @cancel="$emit('cancel')" @submit="deleteBackup">
        <k-text>
            {{ $t('backups.delete.prefix') }} <strong>{{ filename }}</strong>
        </k-text>
    </k-dialog>
</template>

<script>
export default {
    props: {
        filename: { type: String, required: true },
        visible: { type: Boolean, default: true },
    },
    emits: ['cancel', 'close'],
    data() {
        return { busy: false, active: true };
    },
    computed: {
        submitButton() {
            return {
                text: this.$t('backups.delete.button'),
                theme: 'negative',
                icon: this.busy ? 'loader' : 'trash',
                disabled: this.busy,
            };
        },
    },
    beforeUnmount() {
        this.active = false;
    },
    methods: {
        async deleteBackup() {
            if(this.busy) return;
            this.busy = true;
            try {
                const response = await this.$api.post('backups/delete-backup', {filename: this.filename});
                if(!response.deleted) throw new Error(this.$t('backups.request.error'));
                if(this.active) this.$emit('close');
                await this.$panel.reload();
            } 
            catch (error) {
                this.$panel.notification.error(error.message);
            } 
            finally {
                this.busy = false;
            }
        },
    },
};
</script>
