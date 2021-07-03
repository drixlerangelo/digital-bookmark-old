<template>
    <text-field
        :rounded="true"
        :loading="processing"
        :disabled="processing"
        :placeholder="'Username'"
        :error-message="errorMessage"
        :validation-step="validate"
        :tooltip="tooltip"
        ref="textField"
    ></text-field>
</template>

<script>
    import TextField from "../parent/TextField";
    import Validator from "../../lib/validation";

    export default {
        name: "UsernameField",

        components : {
            'text-field' : TextField
        },

        props : {
            inputStyle     : { type : Object },
            loading        : { type : Boolean },
            rounded        : { type : Boolean },
            externalErrors : { type : Array },
            processing     : { type : Boolean },
        },

        data() {
            return {
                errorMessage : '',
                passed       : true,
                tooltip      : 'Only alphanumeric characters are allowed.'
            };
        },

        methods : {
            /**
             * The validation step for user-defined passwords
             *
             * @param {String} username
             *
             * @param {Object} event
             *
             * @returns {Boolean}
             */
            validate(username, event) {
                const field = 'username';

                Validator.setParams(field, username);
                Validator.validateLength(8, 255);
                Validator.validateFormat(/^[a-z\d]*$/);

                let {passed, message} = window.ErrorManager.getResult(field);
                this.passed = passed;
                this.errorMessage = message;

                this.$emit('value-changed', {
                    target  : field,
                    value   : username,
                    event
                });

                return this.passed;
            }
        },

        watch : {
            /**
             * Checks for errors found from the backend
             */
            externalErrors() {
                if (this.externalErrors.length > 0) {
                    this.passed = false;
                    this.$refs.textField.validationPassed = false;
                    this.errorMessage = this.externalErrors[0];
                }
            }
        }
    }
</script>

<style scoped>

</style>
