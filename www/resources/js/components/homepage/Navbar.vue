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
                        <a class="navbar-item" ref="goalSetup">
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
        name: "Navbar",

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
            },

            /**
             * Looks for goals if the user has prepared one
             */
            checkForReminders() {
                axios.get('/reminder/check')
                .then(function ({ data }) {
                    const payload = data.data;
                    this.$refs.goalSetup.classList.toggle('menu-reminder', payload.hasReminder === false);
                }.bind(this))
                .catch(function ({ response }) {
                    this.$emit('error-found', response.data);
                }.bind(this));
            }
        },

        created() {
            this.checkForReminders();
        }
    }
</script>

<style scoped>

</style>
