<template>
    <div class="even-spacing">
        <progress class="progress is-small is-danger goal-meter" :title="goalTooltip" :value="goalProgress" max="100"></progress>
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
            reminder : { type : Object|Array, default : function () { return {}; } }
        },

        data() {
            return {
                days : {sun : 0, mon : 1, tue : 2, wed : 3, thu : 4, fri : 5, sat : 6},
                goalProgress : 0,
                currentMessage : '',
                messages : [
                    'Better get started!',
                    '%s day/s to go this week!',
                    'You overdid yourself today!',
                    'Good going, %s page(s) to go!',
                    'Daily goal achieved, nice work!',
                    'You have no daily goal for today!'
                ],
                logs : [],
                daysPagesRead : {},
                reminderDays : [],
                reminderTimestamps : [],
                goalTooltip : '',
                timestampToday : 0
            };
        },

        methods : {
            /**
             * Checks for any existing reminder
             */
            checkForReminders() {
                let hasReminder = Object.keys(this.reminder).length > 0;
                this.updateCurrentTimestamp();

                if (hasReminder) {
                    this.reminderDays = this.reminder.days.split(',');

                    this.findReminderDates();
                    this.processCurrentWeekLogs();
                    this.calcGoalProgress();
                    this.calcTodaysProgress();
                } else {
                    this.currentMessage = this.messages[MSG_NO_GOAL];
                }
            },

            /**
             * Create storage for pages read in the current week
             */
            findCurrentWeekRange() {
                let indexesOFDays = Object.values(this.days);

                for (let day of indexesOFDays) {
                    let adjustedDate = new Date();

                    adjustedDate.setMilliseconds(0);
                    adjustedDate.setHours(0, 0, 0);
                    adjustedDate.setDate(adjustedDate.getDate() - (adjustedDate.getDay() - day));

                    this.daysPagesRead[adjustedDate.getTime()] = 0;
                }
            },

            /**
             * Find the dates the user should be reminded of their goal
             */
            findReminderDates() {
                let dayToday = (new Date()).getDay();
                this.reminderTimestamps = [];

                for (let day of this.reminderDays) {
                    let dayRelPos = this.days[day] - dayToday;
                    let reminderDate = new Date();

                    reminderDate.setDate(reminderDate.getDate() + dayRelPos);
                    reminderDate.setMilliseconds(0);
                    reminderDate.setHours(0, 0, 0);

                    let reminderTimestamp = reminderDate.getTime();

                    if (this.reminderTimestamps.indexOf(reminderTimestamp) === -1) {
                        this.reminderTimestamps.push(reminderTimestamp);
                    }
                }
            },

            /**
             * Sum all the pages read of the current week
             */
            processCurrentWeekLogs() {
                this.findCurrentWeekRange();

                for (let log of this.logs) {
                    let dtStart = new Date(log.start_time * 1000);
                    dtStart.setHours(0, 0, 0);

                    let timestamp = dtStart.getTime();
                    let dayExists = typeof this.daysPagesRead[timestamp] !== 'undefined';

                    if (dayExists) {
                        this.daysPagesRead[timestamp] += log.pages_read;
                    }
                }
            },

            /**
             * Compares the pages read this week from the goal's pages to read to find the progress
             */
            calcGoalProgress() {
                let reminderPagesToRead = parseInt(this.reminder.pages_to_read);
                let totalPagesToRead = this.reminderTimestamps.length * reminderPagesToRead;
                let daysReadCount = 0;
                let totalPagesRead = 0;

                for (let timestamp in this.daysPagesRead) {
                    let pagesRead = this.daysPagesRead[timestamp];

                    totalPagesRead += (reminderPagesToRead < pagesRead)
                        ? reminderPagesToRead
                        : pagesRead;

                    daysReadCount += (this.reminderTimestamps.indexOf(timestamp) !== 1 && pagesRead > 0) ? 1 : 0;
                }

                this.goalProgress = (this.reminderTimestamps.indexOf(this.timestampToday) !== -1)
                    ? parseInt((totalPagesRead / totalPagesToRead) * 10000) / 100
                    : 0;
                this.goalTooltip = `You've read in ${daysReadCount} out of ${this.reminderTimestamps.length} days.`;
                this.goalTooltip += ` That's a progress of around ${this.goalProgress}%.`;
            },

            /**
             * Calculate the daily goal if it has been achieved and send out an appropriate message
             */
            calcTodaysProgress() {
                let today = new Date(this.timestampToday);
                let reminderPagesToRead = parseInt(this.reminder.pages_to_read);
                let todaysRead = this.daysPagesRead[this.timestampToday];

                if (this.reminderTimestamps.indexOf(this.timestampToday) === -1) {
                    this.currentMessage = this.messages[MSG_GOAL_HOLIDAY];
                    this.goalTooltip = 'Nothing to see here. Enjoy your day!';
                } else if (todaysRead === 0) {
                    let daysLeft = this.reminderDays.filter(function (day) {
                        return this.days[day] >= today.getDate();
                    }.bind(this)).length;
                    this.currentMessage = this.messages[MSG_AFTER_LOGIN].replace('%s', daysLeft);
                } else if (todaysRead > reminderPagesToRead) {
                    this.currentMessage = this.messages[MSG_GOAL_OVERDID];
                } else if (todaysRead === reminderPagesToRead) {
                    this.currentMessage = this.messages[MSG_GOAL_ACHIEVED];
                } else {
                    let remainingPages = reminderPagesToRead - todaysRead;
                    this.currentMessage = this.messages[MSG_TO_GET_GOAL].replace('%s', remainingPages);
                }
            },

            /**
             * Change the current timestamp
             */
            updateCurrentTimestamp() {
                let today = new Date();

                today.setMilliseconds(0);
                today.setHours(0, 0, 0);

                this.timestampToday = today.getTime();
            },

            /**
             * Prevents duplicate logs
             *
             * @param {Array} logs
             */
            filterNewLog(logs) {
                for (let log of logs) {
                    const indexes = ArrayHelper.arrayColumn('id', this.logs);

                    if (indexes.indexOf(log.id) === -1) {
                        this.logs.push(log);
                    }
                }
            },

            /**
             * Fetch the current week's logs
             */
            fetchCurrentWeekLogs() {
                axios.get('/log/current')
                .then(function ({ data }) {
                    this.filterNewLog(data.data.logs);
                    this.checkForReminders();
                }.bind(this));
            }
        },

        created() {
            this.fetchCurrentWeekLogs();
            this.checkForReminders();

            window.eventBus.$on('share-logs', function (logs) {
                this.filterNewLog(logs);
                this.checkForReminders();
            }.bind(this));
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
        margin-top: 5px;
    }

    .goal-meter::-webkit-progress-value {
        background: #BF1B28 !important;
    }

    .goal-meter::-webkit-progress-bar {
        background: #C4C4C4 !important;
    }
</style>
