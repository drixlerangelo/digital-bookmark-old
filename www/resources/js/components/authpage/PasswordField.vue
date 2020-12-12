<template>
    <text-field
        :type="'password'"
        :rounded="true"
        :loading="processing"
        :disabled="processing"
        :placeholder="'Password'"
        :error-message="errorMessage"
        :validation-step="validateString"
        ref="textField"
    ></text-field>
</template>

<script>
    import TextField from "../parent/TextField";

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
                passed       : true
            };
        },

        methods : {
            /**
             * The validation step for user-defined passwords
             *
             * @param {String} password
             *
             * @returns {Boolean}
             */
            validateString(password) {
                if (password.length > 0) {
                    this.passed = true;
                    this.errorMessage = '';
                } else {
                    this.passed = false;
                    this.errorMessage = 'The password field is required.';
                }

                this.$emit('value-changed', {
                    target  : 'password',
                    value   : password,
                    passed  : this.passed,
                    message : this.errorMessage
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
