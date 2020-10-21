<template>

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

</style>
