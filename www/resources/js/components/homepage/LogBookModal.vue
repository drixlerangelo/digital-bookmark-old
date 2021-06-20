<template>
    <modal title="Log Book" @modal-proceed="addLog" @modal-close="modalClose" :action-disabled="validationFailed">
        <div class="log-modal">
            <div class="log-book">
                <div>{{ book.name }}</div>
                <div>{{ book.author }}</div>
            </div>
            <p>Pages Read</p>
            <div class="pages-read-holder">
                <textfield
                    type="number"
                    placeholder="Pages to read"
                    tooltip="This field should not be less than one (1)"
                    :rounded="true"
                    :small="true"
                    :use-callout="true"
                    :error-font-size="12"
                    :is-integer-only="true"
                    :validation-step="validatePagesRead"
                    :error-message="errors.pagesRead"
                ></textfield>
            </div>

            <p>Date of Log</p>
            <div class="date-holder">
                <textfield
                    type="date"
                    placeholder="Date of Log"
                    tooltip="You can only put dates in the present until five years ago"
                    :rounded="true"
                    :small="true"
                    :use-callout="true"
                    :error-font-size="12"
                    :validation-step="validateDate"
                    :error-message="errors.date"
                    :min="computeDate('min')"
                    :max="computeDate('max')"
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
                    ></textfield>
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
    import Modal from '../parent/Modal';
    import TextField from '../parent/TextField';
    import Validator from '../../lib/validation';

    export default {
        name: "LogBookModal",

        components : {
            'textfield' : TextField,
            'modal'     : Modal
        },

        props : {
            book : { type : Object }
        },

        data() {
            return {
                log : {
                    statusId : 0,
                    pagesRead : 0,
                    timeFrom : '',
                    timeTo : '',
                    date : ''
                },
                errors : {
                    pagesRead : '',
                    timeFrom  : '',
                    timeTo    : '',
                    date      : '',
                    datetimeFrom : '',
                    datetimeTo   : '',
                },
                fiveYearsAgo : undefined,
                validationFailed : false
            };
        },

        methods : {
            /**
             * Informs that the model is to be closed
             */
            modalClose() {
                this.$emit('modal-close');
            },

            /**
             * Submits the log
             */
            addLog() {
                let payload = {
                    statusId  : this.book.status_id,
                    pagesRead : this.log.pagesRead,
                    date      : this.log.date,
                    timeFrom  : this.log.timeFrom,
                    timeTo    : this.log.timeTo
                };

                axios.post(
                    '/log/register',
                    payload
                ).then(function ({ data }) {
                    let newLog = data.data.log;
                    newLog.old_status = this.book.status;

                    window.eventBus.$emit('log-created', newLog);
                }.bind(this)
                ).catch(function ({ response }) {
                    const serverErrors = response.data.errors;
                    const errorStatuses = [403, 422];

                    if (errorStatuses.includes(response.status)) {
                        let columns = ['pagesRead', 'timeFrom', 'timeTo', 'date', 'datetimeFrom', 'datetimeTo'];
                        this.findServerErrors(columns, serverErrors);

                        this.errors.timeFrom = this.errors.timeFrom || this.errors.datetimeFrom;
                        this.errors.timeTo = this.errors.timeTo || this.errors.datetimeTo;

                        this.validationFailed = true;
                    }
                }.bind(this));
            },

            /**
             * Validates the pages read
             *
             * @param {String} inputValue
             *
             * @returns {Boolean} passed
             */
            validatePagesRead(inputValue) {
                this.log.pagesRead = inputValue;

                Validator.setParams('pages read log', this.log.pagesRead);
                Validator.validateLength(1, Infinity, 'number');

                let {passed, message} = window.ErrorManager.getResult('pages read log');
                this.errors.pagesRead = message;

                if (this.log.pagesRead > parseInt(this.book.num_pages)) {
                    passed = false;
                    this.errors.pagesRead = 'This is more than what the book has.';
                }

                if (passed) {
                    this.log.pagesRead = parseInt(inputValue);
                }

                this.runErrorCheck();

                return passed;
            },

            /**
             * Validates the date provided
             *
             * @param {String} inputValue
             *
             * @returns {Boolean} passed
             */
            validateDate(inputValue) {
                Validator.setParams('log date', inputValue);
                Validator.validateFormat(/^[0-9]{4}(-[0-9]{2}){2}$/);

                let dateNow = new Date();
                let convertedDate = new Date(inputValue);

                Validator.setParams('log date', convertedDate);
                Validator.validateLength(this.fiveYearsAgo, dateNow, 'date');

                let {passed, message} = window.ErrorManager.getResult('log date');
                this.errors.date = message;

                if (passed) {
                    this.log.date = inputValue;
                }

                return passed;
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

                    Validator.setParams(`log time ${order.toLowerCase()} field`, inputValue);
                    Validator.validateFormat(/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/);

                    let {passed, message} = window.ErrorManager.getResult(`log time ${order.toLowerCase()} field`);
                    this.errors[varName] = message;

                    if (passed) {
                        this.log[varName] = inputValue;

                        let datetimeFrom = new Date();
                        datetimeFrom.setHours(...this.log.timeFrom.split(':'));

                        let datetimeTo = new Date();
                        datetimeTo.setHours(...this.log.timeTo.split(':'));

                        if (datetimeFrom >= datetimeTo) {
                            passed = false;
                            this.errors[varName] = 'The time from should come before time to.';
                        } else {
                            this.errors.timeFrom = '';
                            this.errors.timeTo = '';

                            this.errors.datetimeFrom = '';
                            this.errors.datetimeTo = '';
                        }
                    }

                    this.runErrorCheck();

                    return passed;
                }.bind(this);
            },

            /**
             * Sets the date limits
             *
             * @param {String} mode
             *
             * @returns {String}
             */
            computeDate(mode) {
                let dateNow = new Date();

                if (mode === 'max') {
                    return dateNow.toJSON().substr(0, 10);
                }

                return this.fiveYearsAgo.toJSON().substr(0, 10);
            },

            /**
             * Computes the past date relative to the current date
             */
            computePast() {
                let dateNow = new Date();
                let fiveYearsAgo = dateNow.setUTCFullYear(dateNow.getUTCFullYear() - 5);
                this.fiveYearsAgo = new Date(fiveYearsAgo);
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
             * Appropriately assign the detected errors by the server
             */
            findServerErrors(columns, serverErrors) {
                for (let column of columns) {
                    this.errors[column] = (typeof serverErrors[column] === 'object') ? serverErrors[column][0] : '';
                }
            }
        },

        created() {
            this.computePast();
        }
    }
</script>

<style scoped>
    .log-modal {
        font-size: 13px;
        padding: 10px;
    }

    .time-frame-holder {
        display: flex;
    }

    .time-from-holder {
        width: 50%;
        margin-right: 5px;
    }

    .time-to-holder {
        width: 50%;
        margin-left: 5px;
    }

    .log-book {
        text-align: center;
        color: #BF1B28;
    }
</style>
