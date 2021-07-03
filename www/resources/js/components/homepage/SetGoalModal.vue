<template>
    <modal title="Set a goal"
        @modal-proceed="registerGoal"
        @modal-close="modalClose"
        :action-disabled="validationFailed"
        :action-name="mode">
        <div class="new-goal-info">
            <p>Pages to Read per day</p>
            <div class="pages-to-read-holder">
                <textfield
                    type="number"
                    placeholder="Pages to read"
                    tooltip="This field should not be less than one (1)"
                    :rounded="true"
                    :small="true"
                    :use-callout="true"
                    :error-font-size="12"
                    :is-integer-only="true"
                    :validation-step="validatePagesToRead"
                    :error-message="errors.pagesToRead"
                    ref="pagesToReadInput"
                ></textfield>
            </div>

            <p>Time Frame</p>
            <div class="time-frame-holder">
                <div class="time-from-holder">
                    <textfield
                        type="time"
                        tooltip="This field is the time you started reading"
                        :rounded="true"
                        :small="true"
                        :use-callout="true"
                        :error-font-size="12"
                        :validation-step="validateTimeFrame('From')"
                        :error-message="errors.timeFrom"
                        ref="timeFromInput"
                    ></textfield>
                </div>
                <div class="time-to-holder">
                    <textfield
                        type="time"
                        tooltip="This field is the time you finished reading"
                        :rounded="true"
                        :small="true"
                        :use-callout="true"
                        :error-font-size="12"
                        :validation-step="validateTimeFrame('To')"
                        :error-message="errors.timeTo"
                        ref="timeToInput"
                    ></textfield>
                </div>
            </div>

            <p>Days</p>
            <div class="days-holder" @mouseover="toggleDaysCalloutMessage" @mouseleave="toggleDaysCalloutMessage">
                <template v-for="(day, index) in days">
                    <div :class="['day', checkIfSelected(day)]" @click="toggleDay($event, day)" :key="index">{{ day }}</div>
                </template>
                <div class="days-error-callout" ref="daysErrorCallout">{{ errors.days }}</div>
            </div>
        </div>
    </modal>
</template>

<script>
    import Modal from '../parent/Modal';
    import TextField from '../parent/TextField';
    import Validator from '../../lib/validation';

    export default {
        name: "SetGoalModal",

        components : {
            'textfield' : TextField,
            'modal'     : Modal
        },

        props : {
            existingGoal : { type : Object }
        },

        data() {
            return {
                days : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                goal : {
                    idx         : 0,
                    pagesToRead : 0,
                    timeFrom    : '',
                    timeTo      : '',
                    days        : []
                },
                errors : {
                    timeFrom : '',
                    timeTo   : '',
                    pagesToRead : '',
                    days : ''
                },
                validationFailed : false,
                mode : 'Register'
            };
        },

        methods : {
            /**
             * Order the closure of this modal
             */
            modalClose() {
                this.$emit('modal-close');
            },

            /**
             * Handle the selection of days
             *
             * @param {Object} event
             *
             * @param {String} day
             */
            toggleDay(event, day) {
                day = day.toLowerCase();
                const element = event.target;
                let isSelected = element.classList.contains('selected-day');
                let dayIndex = this.goal.days.indexOf(day);

                if (dayIndex !== -1) {
                    this.goal.days.splice(dayIndex, 1);

                    if (this.goal.days.length === 0) {
                        this.errors.days = 'Choose at least one day';
                        this.validationFailed = true;
                    }
                } else {
                    this.goal.days.push(day);
                    this.validationFailed = false;
                    this.errors.days = '';
                    this.$refs.daysErrorCallout.style.display = 'none';
                }

                this.runErrorCheck();

                element.classList.toggle('selected-day', isSelected === false);
            },

            /**
             * Handle the validation of time
             *
             * @param {String} order
             *
             * @return {Object}
             */
            validateTimeFrame(order) {
                return function (inputValue) {
                    let varName = 'time' + order;

                    Validator.setParams(`time ${order.toLowerCase()} field`, inputValue);
                    Validator.validateFormat(/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/);

                    let {passed, message} = window.ErrorManager.getResult(`time ${order.toLowerCase()} field`);
                    this.errors[varName] = message;

                    if (passed) {
                        this.goal[varName] = inputValue;
                    }

                    this.runErrorCheck();

                    return passed;
                }.bind(this);
            },

            /**
             * Validates if the pages to read input is a valid positive integer
             *
             * @param {String} inputValue
             *
             * @returns {Boolean} passed
             */
            validatePagesToRead(inputValue) {
                this.goal.pagesToRead = inputValue;
                Validator.setParams('pages to read field', this.goal.pagesToRead);
                Validator.validateLength(1, Infinity, 'number');

                let {passed, message} = window.ErrorManager.getResult('pages to read field');
                this.errors.pagesToRead = message;

                if (passed) {
                    this.goal.pagesToRead = parseInt(inputValue);
                }

                this.runErrorCheck();

                return passed;
            },

            /**
             * Checks for errors in other fields
             */
            runErrorCheck() {
                this.validationFailed = false;

                for (let columnName in this.errors) {
                    let error = this.errors[columnName];

                    if (error.length > 0) {
                        this.validationFailed = true;
                        break;
                    }
                }
            },

            /**
             * Determines if the error message displays over the days
             *
             * @param {Object} event
             */
            toggleDaysCalloutMessage(event) {
                if (this.goal.days.length === 0 && event.type === 'mouseover' && this.errors.days.length > 0) {
                    this.$refs.daysErrorCallout.style.display = 'block';
                } else {
                    this.$refs.daysErrorCallout.style.display = 'none';
                }
            },

            /**
             * Processes the goal to be registered
             */
            registerGoal() {
                axios.post(
                    '/reminder/' + this.mode.toLowerCase(),
                    this.goal
                ).then(function ({ data }) {
                    const newGoal = data.data.reminder;
                    this.$emit('goal-created', newGoal);
                }.bind(this)
                ).catch(function ({ response }) {
                    const serverErrors = response.data.errors;

                    if (response.status === 422) {
                        this.errors.days = (typeof serverErrors.days === 'object') ? serverErrors.days[0] : '';
                        this.errors.pagesToRead = (typeof serverErrors.pagesToRead === 'object') ? serverErrors.pagesToRead[0] : '';
                        this.errors.timeFrom = (typeof serverErrors.timeFrom === 'object') ? serverErrors.timeFrom[0] : '';
                        this.errors.timeTo = (typeof serverErrors.timeTo === 'object') ? serverErrors.timeTo[0] : '';
                        this.validationFailed = true;
                    }
                }.bind(this));
            },

            /**
             * Get the reminder info if there's already reminder info
             */
            extractExistingGoal() {
                if (Object.keys(this.existingGoal).length > 0) {
                    this.mode = 'Modify';
                    this.goal.pagesToRead = this.existingGoal.pages_to_read;

                    let [timeFrom, timeTo] = this.existingGoal.time_frame.split('-');
                    this.goal.timeFrom = timeFrom;
                    this.goal.timeTo = timeTo;

                    this.goal.days = this.existingGoal.days.split(',');
                }
            },

            /**
             * Display the existing goal
             */
            displayExistingGoal() {
                if (this.mode === 'Modify') {
                    this.$refs.pagesToReadInput.$refs.input.value = this.goal.pagesToRead;
                    this.$refs.timeFromInput.$refs.input.value = this.goal.timeFrom;
                    this.$refs.timeToInput.$refs.input.value = this.goal.timeTo;
                }
            },

            /**
             * Checks if a given day is already selected to display it accordingly
             *
             * @param {String} day
             *
             * @returns {String}
             */
            checkIfSelected(day) {
                return (this.goal.days.indexOf(day.toLowerCase()) !== -1) ? 'selected-day' : '';
            }
        },

        created() {
            this.extractExistingGoal();
        },

        mounted() {
            this.displayExistingGoal();
        }
    }
</script>

<style scoped>
    .new-goal-info {
        font-size: 13px;
        padding: 10px;
    }

    .new-goal-info p {
        margin-bottom: 5px;
    }

    .pages-to-read-holder{
        margin-bottom: 10px;
    }

    .time-frame-holder {
        display: flex;
        margin-bottom: 10px;
    }

    .time-from-holder {
        width: 50%;
        margin-right: 5px;
    }

    .time-to-holder {
        width: 50%;
        margin-left: 5px;
    }

    .days-holder {
        display: flex;
        margin-bottom: 4px;
    }

    .day {
        border-radius: 50%;
        width: 33px;
        height: 33px;
        border: 1px solid #BF2C1F;
        text-align: center;
        padding: 5px 2px;
        margin: auto;
        color: #BF2C1F;
        cursor: pointer;
    }

    .selected-day {
        background: #BF2C1F;
        border: 1px solid #BF2C1F;
        color: white;
        cursor: pointer;
    }

    .days-error-callout {
        font-size: 12px;
        z-index: 40;
        position: absolute;
        background-color: #BF1B28;
        border-radius: 5px;
        padding: 2px 10px;
        margin-left: 70px;
        margin-top: 45px;
        display: none;
        color: white;
        opacity: 90%;
        white-space: nowrap;
    }

    .days-error-callout:after {
        content: "";
        position: absolute;
        left: 8px;
        top: -20px;
        border: 10px solid transparent;
        border-bottom: 10px solid #BF1B28;
    }
</style>
