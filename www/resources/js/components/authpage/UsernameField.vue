<template>
    <text-field
        :rounded="true"
        :loading="processing"
        :disabled="processing"
        :placeholder="'Username'"
        :error-message="errorMessage"
        :validation-step="validate"
        ref="textField"
    ></text-field>
</template>

<script>
    import TextField from "../parent/TextField";

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
                passed       : true
            };
        },

        methods : {
            /**
             * The validation step for user-defined passwords
             *
             * @param {String} username
             *
             * @returns {Boolean}
             */
            validate(username) {
                if (username.length > 0) {
                    this.passed = true;
                    this.errorMessage = '';
                } else {
                    this.passed = false;
                    this.errorMessage = 'The username field is required.';
                }

                this.$emit('value-changed', {
                    target  : 'username',
                    value   : username,
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
