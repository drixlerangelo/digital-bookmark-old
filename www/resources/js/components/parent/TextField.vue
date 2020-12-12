<template>
    <div :class="controlClasses">
        <input
            :class="inputClasses"
            :type="type"
            :placeholder="placeholder"
            :disabled="disabled"
            @keyup="validate"
            @paste="validate"
            ref="input"
        ><div v-if="validationPassed === false" class="input-error">{{ errorMessage }}</div>
    </div>
</template>

<script>
    export default {
        name: "TextField",

        props : {
            inputType      : { type : String, default : 'text' },
            placeholder    : { type : String, default : 'input here...' },
            errorMessage   : { type : String, default : '' },
            validationStep : { type : Function, default() { return true; } },
            loading        : { type : Boolean },
            rounded        : { type : Boolean },
            disabled       : { type : Boolean }
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
             *
             * @param {Object}
             */
            validate({ target }) {
                this.validationPassed = this.validationStep(target.value);
            }
        },

        watch : {
            /**
             * Changes the style accordingly if an error is found
             */
            validationPassed() {
                this.$refs.input.classList.toggle('is-danger', this.validationPassed === false);

                if (this.validationPassed === true) {
                    this.$refs.input.style.marginBottom = '3vh';
                    this.$refs.input.style.borderColor = '#DBDBDB';
                } else {
                    this.$refs.input.style.marginBottom = '0vh';
                    this.$refs.input.style.borderColor = '#BF2C1F';
                }
            },

            /**
             * Displays the loading screen when needed
             */
            loading() {
                (this.loading === true)
                    ? this.controlClasses.push('is-loading')
                    : this.controlClasses.splice(this.controlClasses.indexOf('is-loading'), 1);
            }
        },

        created() {
            const allowedTypes = ['text', 'password', 'number'];
            this.typeData = (allowedTypes.indexOf(this.inputType) === -1) ? 'text' : this.inputType;

            (this.rounded === true)
                ? this.inputClasses.push('is-rounded')
                : this.inputClasses.splice(this.inputClasses.indexOf('is-rounded'), 1);
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
</style>
