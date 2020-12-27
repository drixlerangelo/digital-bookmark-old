<template>
    <div>
        <navbar :username="username" @reminder-checked="showGoalReminder"></navbar>
        <notification ref="notifDialog"></notification>
    </div>
</template>

<script>
    import Navbar from '../homepage/Navbar.vue'
    import NotificationDialog from '../homepage/NotificationDialog';

    export default {
        name: "HomePage",

        props : {
            username : { type : String }
        },

        components: {
            'navbar'       : Navbar,
            'notification' : NotificationDialog
        },

        methods : {
            /**
             * Shows a notification if no goal was set
             */
            showGoalReminder(hasReminder) {
                if (hasReminder === false) {
                    this.$refs.notifDialog.openNotif('No goal set', 'You have not yet set a goal.');
                }
            }
        }
    }
</script>

<style scoped>
    @import '../../../css/home.css';
</style>
