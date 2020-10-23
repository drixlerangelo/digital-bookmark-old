<template>
    <div style="height: 100%;">
        <div class="hero-body">
            <div class="columns">
                <div class="column is-10-mobile is-offset-1-mobile is-4-desktop is-offset-4-desktop">
                    <div class="title">Digital Bookmark</div>
                    <input class="input" type="text" placeholder="Username">
                    <input class="input" type="password" placeholder="Password">

                    <button style="display: block; margin-left: auto; margin-right: auto; width: 40%;" class="button is-danger is-rounded">Login</button>

                    <div class="buttons has-addons">
                        <button style="width: 50%" class="button">Signup</button>
                        <button style="width: 50%" class="button">Login</button>
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
    }

    .title {
        font-family: Raleway,serif;
        font-style: normal;
        font-weight: normal;
        line-height: 56px;
        color: #BF1B28;
        text-align: center;
    }

    @media screen and (min-width: 601px) {
        .title {
            margin-top: 45px;
            font-size: 48px;
        }

        .column {
            margin-top: 4em;
            box-shadow: 0 0 0 2em white;
            border-radius: 15px;
        }
    }

    @media screen and (max-width: 600px) {
        .title {
            margin-top: 35px;
            font-size: 38px;
        }
    }
</style>
