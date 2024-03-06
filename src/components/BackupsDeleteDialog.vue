<template>
    <k-dialog :class="['backups-delete-dialog', {'can-delete': canDelete}]" ref="dialog"
              :button="$t('backups.delete.multiple.button')" theme="negative" icon="trash" size="medium"
              @submit="deleteBackups">

        <k-form ref="form" class="backups-delete-form" :fields="fields" v-model="value" @input="onPeriodChange"/>

        <k-box theme="info" v-if="hasWarning">
            <k-text v-html="warning" />
        </k-box>
        <div class="warning-loading" v-else-if="loadingSimulation">
            <k-icon type="backupsLoader" />
        </div>
    </k-dialog>
</template>

<script>
export default {
    data() {
        return {
            status: 'closed',
            warning: '',
            toDelete: 0,
            value: { period: null },
            fields: {
                period: {
                    label: this.$t('backups.delete.multiple.question'),
                    type: 'select',
                    placeholder: this.$t('backups.delete.multiple.placeholder'),
                    options: [
                        {value: 'week', text: this.$t('backups.delete.multiple.week')},
                        {value: 'month', text: this.$t('backups.delete.multiple.month')},
                        {value: 'half', text: this.$t('backups.delete.multiple.half')},
                        {value: 'year', text: this.$t('backups.delete.multiple.year')},
                    ]
                },
            },
        }
    },
    props: {
        backups: Object,
    },
    computed: {
        hasWarning() {
            return this.status == 'warning' && this.warning.length
        },
        loadingSimulation() {
            return this.status == 'warning' && !this.hasWarning
        },
        canDelete() {
            return this.hasWarning && this.toDelete > 0
        }
    },
    methods: {
        open() {
            this.resetValue()
            this.status = 'open'
            this.$refs.dialog.open();
        },
        onPeriodChange() {
            if(this.value.period && this.value.period.length) {
                this.simulate()
            }
            else {
                this.status = 'open'
            }
        },
        simulate() {
            this.status  = 'warning'
            this.warning = ''

            this.$api.get('backups/simulate-deletion', {period: this.value.period})
                .then(response => {
                    this.warning  = response.toDelete.text
                    this.toDelete = response.toDelete.count
                })
        },
        deleteBackups() {
            this.$api.post('backups/delete-backups', {period: this.value.period})
                .then(response => {
                    this.$emit('deleted', response.deleted);
                    this.$refs.dialog.close();
                })
        },
        resetValue() {
            this.value   = { period: null }
            this.warning = ''
        }
    }
};
</script>
