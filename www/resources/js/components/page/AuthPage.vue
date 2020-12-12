<template>
    <div style="height: 100%;">
        <user-form
            :mode="currentMode"
            :inputs="userData"
            :submit="formSubmit"
            @submit-done="resetForm"
            @error-found="updateErrors"
        ></user-form>

        <div class="hero-body">
            <div class="columns">
                <div class="auth-form column is-4-desktop is-12-touch">
                    <div class="title">Digital Bookmark</div>

                    <div class="field">
                        <username-field
                            :external-errors="errors.username"
                            :processing="inputsDisabled"
                            @value-changed="handleInput"
                        ></username-field>
                        <password-field
                            :external-errors="errors.password"
                            :processing="inputsDisabled"
                            @value-changed="handleInput"
                        ></password-field>
                    </div>

                    <action-button
                        :current-mode="currentMode"
                        :disabled="submitDisabled"
                        @submit-start="submitForm"
                    ></action-button>

                    <action-options
                        :options="['SIGNUP', 'LOGIN']"
                        @mode-change="switchMode"
                    ></action-options>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ActionOptions from "../authpage/ActionOptions";
    import ActionButton from "../authpage/ActionButton";
    import PasswordField from "../authpage/PasswordField";
    import UsernameField from "../authpage/UsernameField";
    import UserForm from "../authpage/UserForm";

    export default {
        name: "AuthPage",

        components : {
            'action-options' : ActionOptions,
            'action-button'  : ActionButton,
            'password-field' : PasswordField,
            'username-field' : UsernameField,
            'user-form'      : UserForm
        },

        data() {
            return {
                currentMode    : '',
                userData       : { username : '', password : '' },
                formSubmit     : false,
                submitDisabled : false,
                inputsDisabled : false,
                errors         : { username : [], password : [] },
                inputPassed    : { username : true, password : true }
            };
        },

        methods : {
            /**
             * Changes the mode based on what the user clicked
             *
             * @param {String} mode
             */
            switchMode(mode) {
                this.currentMode = mode;
            },

            /**
             * Gets the validation result from the inputs and
             * determines if the inputs are allowed to be submitted
             *
             * @param {Object} data
             */
            handleInput(data) {
                this.inputPassed[data.target] = data.passed;
                this.userData[data.target] = data.value;
                this.submitDisabled = !this.inputPassed.username || !this.inputPassed.password;
            },

            /**
             * Submits the form and prevents the user from interacting with it
             */
            submitForm() {
                this.formSubmit = true;
                this.submitDisabled = true;
                this.inputsDisabled = true;
            },

            /**
             * When an error is found from the input,
             * submitting is disabled and errors are shown
             *
             * @param {Object}
             */
            updateErrors({ errors, message }) {
                this.formSubmit = false;
                this.submitDisabled = false;
                this.inputsDisabled = false;

                if (message.length > 0) {
                    this.errors.username = errors.username || [];
                    this.errors.password = errors.password || [];
                    this.submitDisabled = true;
                }
            },

            /**
             * Resets the form when login is successful,
             * this also redirects the user to the home page
             *
             * @param {Object} authorization
             */
            resetForm(authorization) {
                this.formSubmit = false;
                this.userData = { username : '', password : '' };
                this.errors = { username : [], password : [] };

                if (authorization.granted) {
                    alert(authorization.message);
                    location.href = '/';
                }
            }
        }
    }
</script>

<style scoped>
    @import '../../../css/authpage.css';

    .hero-body {
        height: 100%;
        background: #BF2C1F !important;
    }

    .column {
        background: white;
        border-radius: 15px;
    }

    .title {
        font-family: Raleway,serif;
        font-style: normal;
        font-weight: normal;
        line-height: 56px;
        color: #BF1B28;
        text-align: center;
        margin-top: 0.5em;
        font-size: 220%;
    }

    .columns {
        display: flex;
    }
</style>
