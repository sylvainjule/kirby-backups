<template>
    <k-dialog :visible="visible" class="backups-delete-dialog" size="medium"
              :submit-button="submitButton" @cancel="$emit('cancel')" @submit="deleteBackups">
        <k-fieldset class="backups-delete-form" :fields="fields" :value="value"
                    :disabled="busy" @input="onPeriodChange" @submit="deleteBackups" />

        <k-box theme="info" v-if="hasWarning">
            <k-text :text="$h(warning)" />
        </k-box>
        <div class="warning-loading" v-else-if="loadingSimulation">
            <k-icon type="backupsLoader" />
        </div>
    </k-dialog>
</template>

<script>
const periods = ['week', 'month', 'half', 'year'];

export default {
    props: { visible: { type: Boolean, default: true } },
    emits: ['cancel', 'close'],
    data() {
        return {
            status: 'open',
            warning: '',
            toDelete: 0,
            value: { period: null },
            simulationId: 0,
            busy: false,
            active: true,
            fields: {
                period: {
                    label: this.$t('backups.delete.multiple.question'),
                    type: 'select',
                    placeholder: this.$t('backups.delete.multiple.placeholder'),
                    options: periods.map(value => ({
                        value, text: this.$t(`backups.delete.multiple.${value}`),
                    })),
                },
            },
        };
    },
    computed: {
        hasWarning() {
            return this.status === 'warning' && this.warning.length > 0;
        },
        loadingSimulation() {
            return this.status === 'loading';
        },
        canDelete() {
            return !this.busy && this.hasWarning && this.toDelete > 0 && periods.includes(this.value.period);
        },
        submitButton() {
            return {
                text: this.$t('backups.delete.multiple.button'),
                theme: 'negative',
                icon: this.busy ? 'loader' : 'trash',
                disabled: !this.canDelete,
            };
        },
    },
    beforeUnmount() {
        this.active = false;
        this.simulationId++;
    },
    methods: {
        onPeriodChange(value) {
            if(this.busy) return;
            this.value = {...value};
            return this.simulate();
        },
        async simulate() {
            const id = ++this.simulationId;
            const period = this.value.period;
            this.warning = '';
            this.toDelete = 0;
            this.status = 'open';
            if(!periods.includes(period)) return;
            this.status = 'loading';
            try {
                const response = await this.$api.get('backups/simulate-deletion', {period});
                if(id !== this.simulationId) return;
                this.warning = response.toDelete.text;
                this.toDelete = response.toDelete.count;
                this.status = 'warning';
            } 
            catch (error) {
                if(id !== this.simulationId) return;
                this.status = 'open';
                this.$panel.notification.error(error.message);
            }
        },
        async deleteBackups() {
            if(!this.canDelete) return;
            this.busy = true;
            try {
                await this.$api.post('backups/delete-backups', {period: this.value.period});
                if(this.active) this.$emit('close');
                await this.$panel.reload();
            } 
            catch (error) {
                this.$panel.notification.error(error.message);
                if(this.active) await this.simulate();
            } 
            finally {
                this.busy = false;
            }
        },
    },
};
</script>
