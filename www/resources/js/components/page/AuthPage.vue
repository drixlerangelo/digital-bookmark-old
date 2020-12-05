<template>
    <div style="height: 100%;">
        <div class="hero-body">
            <div class="columns">
                <div class="auth-form column is-4-desktop is-offset-4-desktop is-12-touch">
                    <div class="title">Digital Bookmark</div>

                    <div class="field">
                        <div class="control is-loading">
                            <input class="input is-rounded" type="text" placeholder="Username" ref="username">
                            <div v-if="credentialsPassed === false" class="username-error">Email does not exist</div>
                        </div>

                        <div class="control is-loading">
                            <input class="input is-rounded" type="password" placeholder="Password" ref="password">
                            <div v-if="credentialsPassed === false" class="username-error">Password incorrect</div>
                        </div>
                    </div>

                    <button class="button action-button is-rounded" @click="submit">
                        {{ currentMode }}
                    </button>

                    <div class="columns" style="margin-top: 1em;">
                        <div class="signup-button column" @click="test('signup')" :style="signupStyle">
                            SIGNUP
                        </div>
                        <div class="login-button column" @click="test('login')" :style="loginStyle">
                            LOGIN
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ActionOptions from "../authpage/ActionOptions";
    import ActionButton from "../authpage/ActionButton";
    import PasswordField from "../authpage/PasswordField";
    import UserForm from "../authpage/UserForm";
    import UsernameField from "../authpage/UsernameField";

    export default {
        name: "AuthPage",

        components : {
            'action-options' : ActionOptions,
            'action-button'  : ActionButton,
            'password-field' : PasswordField,
            'user-form'      : UserForm,
            'username-field' : UsernameField
        },

        data() {
            return {
                loginStyle  : {},
                signupStyle : {},
                currentMode : 'LOGIN',
                credentialsPassed : true,

                //TODO: Pass this to CSS later
                actionStyles : {
                    login : {
                        inactive: {
                            'background': '#BF2C1F',
                            'border-top-right-radius': 0,
                            'margin-bottom': '2px',
                            'margin-right': '2px',
                            'color': '#F2F2F2'
                        },
                        active: {
                            'background': '#FFF',
                            'color': '#BF2C1F'
                        }
                    },
                    signup : {
                        inactive: {
                            'background': '#BF2C1F',
                            'border-top-left-radius': 0,
                            'margin-bottom': '2px',
                            'margin-left': '2px',
                            'color': '#F2F2F2'
                        },
                        active: {
                            'background': '#FFF',
                            'color': '#BF2C1F'
                        }
                    }
                }
            };
        },

        methods : {
            test(mode) {
                this[mode + 'Style'] = this.actionStyles[mode].active;
                this.currentMode = mode.toUpperCase();

                if (mode === 'login') {
                    this.signupStyle = this.actionStyles['signup'].inactive;
                } else {
                    this.loginStyle = this.actionStyles['login'].inactive;
                }
            },

            submit() {
                this.credentialsPassed = this.credentialsPassed === false; // TODO: create validation
                this.$refs.username.classList.toggle('is-danger', this.credentialsPassed === false);

                if (this.credentialsPassed === true) {
                    this.$refs.username.style.marginBottom = '3vh';
                    this.$refs.username.style.borderColor = '#dbdbdb';

                    this.$refs.password.style.marginBottom = '3vh';
                    this.$refs.password.style.borderColor = '#dbdbdb';
                } else {
                    this.$refs.username.style.marginBottom = '0vh';
                    this.$refs.username.style.borderColor = '#BF2C1F';

                    this.$refs.password.style.marginBottom = '0vh';
                    this.$refs.password.style.borderColor = '#BF2C1F';
                }
            }
        },

        created() {
            this.test('login');
        }
    }
</script>

<style scoped lang="scss">
    @import '../../../css/authpage.scss';

    .hero-body {
        height: 100%;
        background: #BF2C1F !important;
    }

    .action-button {
        color: white;
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-top: 1em;
        background-color: #BF2C1F;
    }

    .action-button:hover {
        background-color: #de3121
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

    .input {
        margin-left: auto;
        display: block;
        margin-right: auto;
        margin-bottom: 3vh;
    }

    .signup-button {
        border-top-left-radius: 0;
        border-bottom-right-radius: 0;
        font-size: 1.1em;
        text-align: center;
    }

    .login-button {
        border-bottom-left-radius: 0;
        border-top-right-radius: 0;
        font-size: 1.1em;
        text-align: center;
    }

    .username-error, .password-error {
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 3vh;
        color: #BF2C1F;
    }
</style>
