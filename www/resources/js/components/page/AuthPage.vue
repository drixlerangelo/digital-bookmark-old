<template>
    <div style="height: 100%;">
        <div class="hero-body">
            <div class="columns">
                <div class="column is-10-mobile is-offset-1-mobile">
                    <div class="title">Digital Bookmark</div>

                    <div class="field">
                        <div class="control is-loading">
                            <input class="input is-rounded" style="margin-top: 3em;" type="text" placeholder="Username">
                        </div>
                    </div>


                    <input class="input is-rounded" style="margin-top: 1em;" type="password" placeholder="Password">

                    <button style="display: block; margin-left: auto; margin-right: auto; width: 25vw; margin-top: 1em; background-color: #BF2C1F;" class="button is-danger is-rounded">
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
            }
        },

        created() {
            this.test('login');
        }
    }
</script>

<style scoped>
    .hero-body {
        height: 100%;
        background: #BF2C1F !important;
    }

    .column {
        background: white;
        margin-top: 4em;
        border-radius: 15px;
    }

    .columns {
        display: flex;
    }

    .title {
        font-family: Raleway,serif;
        font-style: normal;
        font-weight: normal;
        line-height: 56px;
        color: #BF1B28;
        text-align: center;
    }

    .input {
        /* width: 25vw; */
        margin-left: auto;
        display: block;
        margin-right: auto;
    }

    @media screen and (min-width: 601px) {
        .title {
            margin-top: 0.5em;
            font-size: 2em;
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
    }

    @media screen and (max-width: 600px) {
        .title {
            margin-top: 35px;
            font-size: 38px;
        }
    }
</style>
