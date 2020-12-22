<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <div class="navbar-item brand">
                Digital Bookmark
            </div>

            <a role="button" class="navbar-burger" ref="navbarBurger" @click="toggleBurger" aria-label="menu" aria-expanded="false">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div class="navbar-menu" ref="navbarMenu">
            <div class="navbar-end">
                <!-- TODO: Add other elements -->

                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        {{ username }}
                    </a>

                    <div class="navbar-dropdown is-right">
                        <a class="navbar-item">
                            Set the goal
                        </a>
                        <hr class="navbar-divider">
                        <a class="navbar-item">
                            Settings
                        </a>
                        <hr class="navbar-divider">
                        <a class="navbar-item" href="/user/logout">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        name: "HomePage",

        props : {
            username : { type : String }
        },

        methods : {
            /**
             * Toggle the display of the navbar burger
             */
            toggleBurger() {
                this.$refs.navbarBurger.classList.toggle('is-active');
                this.$refs.navbarMenu.classList.toggle('is-active');
            }
        }
    }
</script>

<style scoped>
    @import '../../../css/home.css';
</style>
