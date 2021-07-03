<template>
    <text-field
        :type="'password'"
        :rounded="true"
        :loading="processing"
        :disabled="processing"
        :placeholder="'Password'"
        :error-message="errorMessage"
        :validation-step="validateString"
        :tooltip="tooltip"
        ref="textField"
    ></text-field>
</template>

<script>
    import TextField from "../parent/TextField";
    import Validator from "../../lib/validation";

    export default {
        name: "PasswordField",

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
                tooltip      : 'Only alphanumeric and these characters are allowed:\n` ~ ! @ # $ % ^ & * ( ) - _ + = [ \] { } \\ | : ; \' " , < . > \/ ? '
            };
        },

        methods : {
            /**
             * The validation step for user-defined passwords
             *
             * @param {String} password
             *
             * @param {Object} event
             *
             * @returns {Boolean}
             */
            validateString(password, event) {
                const field = 'password';

                Validator.setParams(field, password);
                Validator.validateLength(8, 255);
                Validator.validateFormat(/^[\w`~!@#$%^&*()-_+=[\]{}\\|:;\'",<.>\/?]*$/);

                let {passed, message} = window.ErrorManager.getResult(field);
                this.passed = passed;
                this.errorMessage = message;

                this.$emit('value-changed', {
                    target  : field,
                    value   : password,
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
