import Backups from './components/Backups.vue';

panel.plugin('sylvainjule/backups', {
  components: {
    "k-backups-view": Backups,
  },
  icons: {
    backup: '<path d="M7.12,11.86a.56.56,0,0,1-.4-.16l-2-2a.56.56,0,0,1,.79-.79L7.12,10.5l3.56-3.56a.56.56,0,0,1,.79.79l-4,4A.56.56,0,0,1,7.12,11.86Z"/><path d="M14,15.45H2.17A2.17,2.17,0,0,1,0,13.28V1.41A.69.69,0,0,1,.69.72H6.13a.69.69,0,0,1,.59.33L8,3.19h7.52a.69.69,0,0,1,.69.69v9.4A2.17,2.17,0,0,1,14,15.45ZM1.38,2.1V13.28a.8.8,0,0,0,.79.79H14a.8.8,0,0,0,.8-.79V4.57H7.61A.69.69,0,0,1,7,4.23L5.74,2.1Z"/>',
    backupsLoader: '<g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="1.75"><circle cx="7" cy="7" r="7.2" stroke="#000" stroke-opacity=".2"/><path d="M14.2,7c0-4-3.2-7.2-7.2-7.2" stroke="#000"><animateTransform attributeName="transform" type="rotate" from="0 7 7" to="360 7 7" dur="1s" repeatCount="indefinite"/></path></g></g>'
  }
});
