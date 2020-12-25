<template>
    <navbar :username="username"></navbar>
</template>

<script>
    import Navbar from '../homepage/Navbar.vue'

    export default {
        name: "HomePage",

        props : {
            username : { type : String }
        },

        components: {
            'navbar' : Navbar
        }
    }
</script>

<style scoped>
    @import '../../../css/home.css';
</style>
