<template>
    <div :class="controlClasses">
        <input
            :class="inputClasses"
            :type="typeData"
            :placeholder="placeholder"
            :disabled="disabled"
            @keyup="validate"
            @paste="validate"
            @change="validate"
            @blur="validate"
            @mouseover="showCalloutMessage"
            @mouseleave="showCalloutMessage"
            ref="input"
            :title="tooltip"
        >
        <div
            v-if="validationPassed === false && useCallout === false"
            class="input-error"
            :style="{ 'font-size' : errorFontSize + 'px' }"
        >{{ errorMessage }}</div>
        <div
            class="error-callout"
            :style="{ 'font-size' : errorFontSize + 'px' }"
            ref="errorCallout"
        >{{ errorMessage }}</div>
    </div>
</template>

<script>
    export default {
        name: "TextField",

        props : {
            type           : { type : String, default : 'text' },
            placeholder    : { type : String, default : 'input here...' },
            errorMessage   : { type : String, default : '' },
            tooltip        : { type : String, default : '' },
            validationStep : { type : Function, default() { return true; } },
            loading        : { type : Boolean },
            rounded        : { type : Boolean },
            disabled       : { type : Boolean },
            small          : { type : Boolean },
            useCallout     : { type : Boolean },
            isIntegerOnly  : { type : Boolean },
            errorFontSize  : { type : Number }
        },

        data() {
            return {
                controlClasses   : ['control'],
                inputClasses     : ['input', 'text-field'],
                validationPassed : true,
                typeData         : ''
            };
        },

        methods : {
            /**
             * Gets the value the user entered to be validated
             */
            validate() {
                if (this.isIntegerOnly) {
                    this.$refs.input.value = this.$refs.input.value.replace(/[^0-9]/, '');
                }

                this.validationPassed = this.validationStep(this.$refs.input.value);
            },

            /**
             * Show the callout message when hovered
             */
            showCalloutMessage({ type }) {
                if (this.useCallout === true && this.validationPassed === false) {
                    if (type === 'mouseover') {
                        this.$refs.errorCallout.style.display = 'flex';
                    } else {
                        this.$refs.errorCallout.style.display = 'none';
                    }
                }
            }
        },

        watch : {
            /**
             * Changes the style accordingly if an error is found
             */
            validationPassed() {
                this.$refs.input.classList.toggle('is-danger', this.validationPassed === false);

                if (this.useCallout === false) {
                    if (this.validationPassed === true) {
                        this.$refs.input.style.marginBottom = '3vh';
                        this.$refs.input.style.borderColor = '#DBDBDB';
                    } else {
                        this.$refs.input.style.marginBottom = '0vh';
                        this.$refs.input.style.borderColor = '#BF2C1F';
                    }
                } else {
                    if (this.validationPassed === true) {
                        this.$refs.errorCallout.style.display = 'none';
                    }
                }
            },

            /**
             * Displays the loading screen when needed
             */
            loading() {
                (this.loading === true)
                    ? this.controlClasses.push('is-loading')
                    : this.controlClasses.splice(this.controlClasses.indexOf('is-loading'), 1);
            },

            /**
             * Automatically make the validation fail when an error is found
             */
            errorMessage() {
                if (this.errorMessage.length > 0) {
                    this.validationPassed = false;
                }
            }
        },

        created() {
            const allowedTypes = ['text', 'password', 'number', 'time'];
            this.typeData = (allowedTypes.indexOf(this.type) === -1) ? 'text' : this.type;

            if (this.rounded === true) {
                this.inputClasses.push('is-rounded');
            }

            if (this.small === true) {
                this.inputClasses.push('is-small');
            }
        }
    }
</script>

<style scoped>
    .input-error {
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 3vh;
        color: #BF2C1F;
    }

    .error-callout {
        z-index: 40;
        position: absolute;
        background-color: #BF1B28;
        border-radius: 5px;
        padding: 2px 10px;
        margin-top: 4px;
        display: none;
        color: white;
        opacity: 90%;
        white-space: nowrap;
    }

    .error-callout:after {
        content: "";
        position: absolute;
        left: 8px;
        top: -20px;
        border: 10px solid transparent;
        border-bottom: 10px solid #BF1B28;
    }
</style>
