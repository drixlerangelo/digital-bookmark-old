<template>
    <div class="even-spacing">
        <progress class="progress is-small is-danger goal-meter" :value="goalProgress" max="100"></progress>
        <div class="goal-message">{{ currentMessage }}</div>
    </div>
</template>

<script>
    /**
     * Array position, if there's no goal yet
     *
     * @var {Number}
     */
    const MSG_NO_GOAL = 0;

    /**
     * Array position, it happens after logging in when there are no logs
     *
     * @var {Number}
     */
    const MSG_AFTER_LOGIN = 1;

    /**
     * Array position, it happens after logging in when the user reaches its daily goal
     *
     * @var {Number}
     */
    const MSG_GOAL_OVERDID = 2;

    /**
     * Array position, when the user has some logs but yet to achieve the daily goal
     *
     * @var {Number}
     */
    const MSG_TO_GET_GOAL = 3;

    /**
     * Array position, when pages read is equal to daily goal
     *
     * @var {Number}
     */
    const MSG_GOAL_ACHIEVED = 4;

    /**
     * Array position, if the day today has no daily goal
     *
     * @var {Number}
     */
    const MSG_GOAL_HOLIDAY = 5;

    export default {
        name: "GoalDisplay",

        props : {
            reminder : { type : Object|Array, default : function () { return {}; } },
            logs     : { type : Array, default : function () { return []; } }
        },

        data() {
            return {
                days : ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'],
                goalProgress   : 0,
                currentMessage : '',
                messages       : [
                    'You have no goals yet. Better get started!',
                    '%s day/s to go this week!',
                    'You overdid yourself today!',
                    'Good going, %s pages left for today!',
                    'You achieve your daily goal, good work!',
                    'You have no daily goal for today!'
                ]
            };
        },

        methods : {
            /**
             * Checks for any existing reminder
             */
            checkForReminders() {
                let hasReminder = Object.keys(this.reminder).length > 0;
                let noLogs = this.logs.length === 0;

                if (hasReminder) {
                    if (noLogs) {
                        let dayToday = (new Date).getDay();
                        let daysLeft = this.reminder.days.split(',').filter(function (day) {
                            return this.days.indexOf(day) >= dayToday;
                        }.bind(this)).length;

                        this.currentMessage = this.messages[MSG_AFTER_LOGIN].replace('%s', daysLeft);
                    } else {
                        // TODO: calculate progress from logs
                    }
                } else {
                    this.currentMessage = this.messages[MSG_NO_GOAL];
                }
            }
        },

        created() {
            this.checkForReminders();
        },

        watch : {
            reminder() {
                this.checkForReminders();
            }
        }
    }
</script>

<style scoped>
    .goal-message, .goal-meter {
        width: 60%;
        margin-left: auto;
        margin-right: auto;
        cursor: pointer;
    }

    .goal-meter {
        margin-bottom: 0px !important;
    }

    .goal-message {
        text-align: center;
        color: #BF1B28;
        font-family: Roboto;
    }

    .goal-meter::-webkit-progress-value {
        background: #BF1B28 !important;
    }

    .goal-meter::-webkit-progress-bar {
        background: #C4C4C4 !important;
    }
</style>
