<template>
    <div class="book-time">
        <div
            :title="displaySpentTime()"
        >{{ displayPctCompleted() }}</div>
        <div
            :title="`${ displayRemaningMins() } left before completing the book`"
        >ETA: {{ displayRemaningMins(true) }}</div>
    </div>
</template>
<script>
    /**
     * Amount of minutes in a week
     *
     * @var {Number}
     */
    const WEEK_IN_MINUTES = 10080;

    /**
     * Amount of minutes in a day
     *
     * @var {Number}
     */
    const DAY_IN_MINUTES = 1440;

    /**
     * Amount of minutes in a hour
     *
     * @var {Number}
     */
    const HOUR_IN_MINUTES = 60;

    /**
     * Amount of minutes in a minute
     *
     * @var {Number}
     */
    const MINUTE_IN_MINUTES = 1;

    export default {
        name: "BookProgress",

        props : {
            totalPages     : { type : Number },
            totalWords     : { type : Number },
            totalPagesRead : { type : Number },
            readLogs       : { type : Array }, // TODO: process logs
        },

        data() {
            return {
                pct_read       : 0,
                wpm            : 0,
                words_per_page : 0
            };
        },

        methods : {
            /**
             * Calculate words per minute
             */
            calcWordsPerMin() {
                // TODO: Calculate words per minute (WPM)
                this.wpm = 250; // for example
            },

            /**
             * Represents the time in weeks, days, hours, and minutes
             *
             * @param {Number} minutes
             *
             * @returns {String}
             */
            simplifyTime(minutes) {
                const units = {
                    w : WEEK_IN_MINUTES,
                    d : DAY_IN_MINUTES,
                    h : HOUR_IN_MINUTES,
                    m : MINUTE_IN_MINUTES
                };

                let splits = [];
                for (let abbrev in units) {
                    let unit = units[abbrev];
                    let number = Math.floor(minutes / unit);

                    if (number > 0) {
                        splits.push(number + abbrev);
                    }

                    minutes %= unit;
                }

                return (splits.length > 0) ? splits.join(' ') : '0m';
            },

            /**
             * Shortens the estimated remaining time
             *
             * @param {Number} minutes
             *
             * @returns {String}
             */
            shortenETA(minutes) {
                const week_count = Math.floor(minutes / WEEK_IN_MINUTES);

                return (week_count > 99) ? '>100w': this.simplifyTime(minutes);
            },

            /**
             * Displays the total spent time reading the book
             *
             * @returns {String}
             */
            displaySpentTime() {
                let totalLoggedTime = 0; // in minutes, for example
                let remainingPages =  this.totalPages - this.totalPagesRead;
                let estReadWords = this.words_per_page * this.totalPagesRead;
                // TODO: calculate total logged time from logs
                totalLoggedTime = this.simplifyTime(estReadWords / this.wpm);

                return `You spent ${ totalLoggedTime } reading this book`;
            },

            /**
             * Displays the how close to finishing what you're reading, in percentage
             *
             * @returns {String}
             */
            displayPctCompleted() {
                // TODO: calculate total pages read from logs
                const roundoff_places = 2;
                let unrounded = this.totalPagesRead / this.totalPages;
                unrounded *= Math.pow(10, roundoff_places + 2);
                let rounded = Math.round(unrounded);
                this.pct_read = rounded / Math.pow(10, roundoff_places);

                return `${ this.pct_read }% Completed`;
            },

            /**
             * Calculate the remaining minutes
             *
             * @returns {Number}
             */
            calcRemainingMins() {
                let remainingPages =  this.totalPages - this.totalPagesRead;
                let estRemainingWords = this.words_per_page * remainingPages;

                return estRemainingWords / this.wpm;
            },

            /**
             * Displays the remaing minutes to spend, shortened or not
             *
             * @param {Boolean} shortened
             *
             * @returns {String}
             */
            displayRemaningMins(shortened = false) {
                let remainingMins = this.calcRemainingMins();

                if (shortened) {
                    return this.shortenETA(remainingMins);
                }

                return this.simplifyTime(remainingMins);
            }
        },

        mounted() {
            // Calculate words per page
            this.words_per_page = this.totalWords / this.totalPages;

            this.calcWordsPerMin();
        }
    }
</script>

<style scoped>
    .book-time {
        right: 0;
    }
</style>
