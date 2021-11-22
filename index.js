!function(){"use strict";function t(t,e,a,s,n,i,o,l){var c,u="function"==typeof t?t.options:t;if(e&&(u.render=e,u.staticRenderFns=a,u._compiled=!0),s&&(u.functional=!0),i&&(u._scopeId="data-v-"+i),o?(c=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),n&&n.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(o)},u._ssrRegister=c):n&&(c=l?function(){n.call(this,(u.functional?this.parent:this).$root.$options.shadowRoot)}:n),c)if(u.functional){u._injectStyles=c;var r=u.render;u.render=function(t,e){return c.call(e),r(t,e)}}else{var d=u.beforeCreate;u.beforeCreate=d?[].concat(d,c):[c]}return{exports:t,options:u}}const e={};var a=t({props:["backup","downloading"]},(function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("li",{staticClass:"backup-entry"},[a("div",{staticClass:"backup-filename"},[a("k-icon",{attrs:{type:"backup"}}),a("span",[t._v(t._s(t.backup.filename))])],1),a("div",{staticClass:"backup-size"},[t._v(t._s(t.backup.size))]),a("div",{staticClass:"backup-date"},[t._v(t._s(t.backup.date))]),a("div",{staticClass:"backup-actions"},[t.downloading?a("k-button",{staticClass:"backup-download",attrs:{icon:"backupsLoader",disabled:!0}},[t._v(t._s(t.$t("backups.downloading")))]):a("k-button",{staticClass:"backup-download",attrs:{icon:"download"},on:{click:function(e){return t.$emit("download",t.backup.filename)}}},[t._v(t._s(t.$t("backups.download")))]),a("k-button",{attrs:{icon:"trash",theme:"negative"},on:{click:function(e){return t.$emit("delete")}}})],1)])}),[],!1,s,null,null,null);function s(t){for(let a in e)this[a]=e[a]}var n=function(){return a.exports}();const i={};var o=t({data:()=>({backup:null}),computed:{filename(){return this.backup?this.backup.filename:""}},methods:{open(t){this.backup=t,this.$refs.dialog.open()},resetBackup(){this.backup=null},deleteBackup(){this.$api.post("backups/delete-backup",{filename:this.backup.filename}).then((t=>{t.deleted&&this.$emit("deleted",this.backup.filename),this.$refs.dialog.close()}))}}},(function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("k-dialog",{ref:"dialog",attrs:{size:"medium",theme:"negative",icon:"trash",button:t.$t("backups.delete.button")},on:{close:t.resetBackup,cancel:t.resetBackup,submit:t.deleteBackup}},[a("k-text",[t._v(" "+t._s(t.$t("backups.delete.prefix"))+" "),a("strong",[t._v(t._s(t.filename))])])],1)}),[],!1,l,null,null,null);function l(t){for(let e in i)this[e]=i[e]}var c=function(){return o.exports}();const u={};var r=t({data(){return{status:"closed",warning:"",toDelete:0,value:{period:null},fields:{period:{label:this.$t("backups.delete.multiple.question"),type:"select",placeholder:this.$t("backups.delete.multiple.placeholder"),options:[{value:"week",text:this.$t("backups.delete.multiple.week")},{value:"month",text:this.$t("backups.delete.multiple.month")},{value:"half",text:this.$t("backups.delete.multiple.half")},{value:"year",text:this.$t("backups.delete.multiple.year")}]}}}},props:{backups:Object},computed:{hasWarning(){return"warning"==this.status&&this.warning.length},loadingSimulation(){return"warning"==this.status&&!this.hasWarning},canDelete(){return this.hasWarning&&this.toDelete>0}},methods:{open(){this.resetValue(),this.status="open",this.$refs.dialog.open()},onPeriodChange(){this.value.period&&this.value.period.length?this.simulate():this.status="open"},simulate(){this.status="warning",this.warning="",this.$api.get("backups/simulate-deletion",{period:this.value.period}).then((t=>{this.warning=t.toDelete.text,this.toDelete=t.toDelete.count}))},deleteBackups(){this.$api.post("backups/delete-backups",{period:this.value.period}).then((t=>{this.$emit("deleted",t.deleted),this.$refs.dialog.close()}))},resetValue(){this.value={period:null},this.warning=""}}},(function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("k-dialog",{ref:"dialog",class:["backups-delete-dialog",{"can-delete":t.canDelete}],attrs:{button:t.$t("backups.delete.multiple.button"),theme:"negative",icon:"trash",size:"medium"},on:{submit:t.deleteBackups}},[a("k-form",{ref:"form",attrs:{fields:t.fields},on:{input:t.onPeriodChange},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}}),t.hasWarning?a("k-box",{attrs:{theme:"info"}},[a("k-text",{domProps:{innerHTML:t._s(t.warning)}})],1):t.loadingSimulation?a("div",{staticClass:"warning-loading"},[a("k-icon",{attrs:{type:"backupsLoader"}})],1):t._e()],1)}),[],!1,d,null,null,null);function d(t){for(let e in u)this[e]=u[e]}const p={};var k=t({components:{"backup-entry":n,"backup-delete-dialog":c,"backups-delete-dialog":function(){return r.exports}()},props:{backups:Array,title:String},data:()=>({downloading:!1,creationStatus:"default"}),mounted(){window.beforeunload=this.deletePublicBackups()},destroyed(){this.deletePublicBackups()},methods:{deletePublicBackups(){this.$api.post("backups/delete-public-backups")},async createBackup(){this.creationStatus="progress";try{const t=await this.$api.get("plugin-janitor/backupZip");await this.$reload(),this.setCreationStatus(t.status,!0)}catch(t){this.setCreationStatus("error",!0)}},setCreationStatus(t,e=!0){this.creationStatus=200==t?"success":"error",e&&setTimeout((()=>{this.creationStatus="default"}),2e3)},async copyAndDownload(t){this.downloading=t,t="latest"==t?this.backups[0].filename:t;const e=await this.$api.get("backups/copy-to-assets",{filename:t});e.url&&(window.location=e.url),this.downloading=!1},openBackupDeleteDialog(t){this.$refs["backup-delete"].open(t)},openBackupsDeleteDialog(){this.$refs["backups-delete"].open()}}},(function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("k-inside",[a("k-view",{staticClass:"k-backups-view"},[a("k-header",{staticClass:"k-backups-view-header"},[t._v(" "+t._s(t.title)+" "),a("k-button-group",{attrs:{slot:"left"},slot:"left"},["default"==t.creationStatus?a("k-button",{attrs:{icon:"add"},on:{click:t.createBackup}},[t._v(t._s(t.$t("backups.create")))]):"progress"==t.creationStatus?a("k-button",{attrs:{icon:"backupsLoader",disabled:!0}},[t._v(t._s(t.$t("backups.create.process")))]):"success"==t.creationStatus?a("k-button",{attrs:{icon:"check",disabled:!0,theme:"positive"}},[t._v(t._s(t.$t("backups.create.success")))]):"error"==t.creationStatus?a("k-button",{attrs:{icon:"alert",disabled:!0,theme:"negative"}},[t._v(t._s(t.$t("backups.create.error")))]):t._e()],1),t.backups.length?a("k-button-group",{attrs:{slot:"right"},slot:"right"},["latest"!==t.downloading?a("k-button",{attrs:{icon:"download"},on:{click:function(e){return t.copyAndDownload("latest")}}},[t._v(t._s(t.$t("backups.download.latest")))]):a("k-button",{attrs:{icon:"backupsLoader",disabled:!0}},[t._v(t._s(t.$t("backups.downloading")))]),a("k-button",{attrs:{icon:"trash"},on:{click:t.openBackupsDeleteDialog}},[t._v(t._s(t.$t("backups.delete.some")))])],1):t._e()],1),t.backups.length?a("section",{staticClass:"backups-section"},[a("header",{staticClass:"backups-header"},[a("div",{staticClass:"backup-filename"},[t._v(t._s(t.$t("backups.filename")))]),a("div",{staticClass:"backup-size"},[t._v(t._s(t.$t("backups.size")))]),a("div",{staticClass:"backup-date"},[t._v(t._s(t.$t("backups.created")))])]),a("ul",{staticClass:"backups-list"},t._l(t.backups,(function(e){return a("backup-entry",{key:e.filename,attrs:{backup:e,downloading:t.downloading==e.filename},on:{download:t.copyAndDownload,delete:function(a){return t.openBackupDeleteDialog(e)}}})})),1)]):a("div",{staticClass:"backups-placeholder"}),a("backup-delete-dialog",{ref:"backup-delete",on:{deleted:t.$reload}}),a("backups-delete-dialog",{ref:"backups-delete",attrs:{backups:t.backups},on:{deleted:t.$reload}})],1)],1)}),[],!1,b,null,null,null);function b(t){for(let e in p)this[e]=p[e]}var h=function(){return k.exports}();panel.plugin("sylvainjule/backups",{components:{"k-backups-view":h},icons:{backup:'<path d="M7.12,11.86a.56.56,0,0,1-.4-.16l-2-2a.56.56,0,0,1,.79-.79L7.12,10.5l3.56-3.56a.56.56,0,0,1,.79.79l-4,4A.56.56,0,0,1,7.12,11.86Z"/><path d="M14,15.45H2.17A2.17,2.17,0,0,1,0,13.28V1.41A.69.69,0,0,1,.69.72H6.13a.69.69,0,0,1,.59.33L8,3.19h7.52a.69.69,0,0,1,.69.69v9.4A2.17,2.17,0,0,1,14,15.45ZM1.38,2.1V13.28a.8.8,0,0,0,.79.79H14a.8.8,0,0,0,.8-.79V4.57H7.61A.69.69,0,0,1,7,4.23L5.74,2.1Z"/>',backupsLoader:'<g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="1.75"><circle cx="7" cy="7" r="7.2" stroke="#000" stroke-opacity=".2"/><path d="M14.2,7c0-4-3.2-7.2-7.2-7.2" stroke="#000"><animateTransform attributeName="transform" type="rotate" from="0 7 7" to="360 7 7" dur="1s" repeatCount="indefinite"/></path></g></g>'}})}();
