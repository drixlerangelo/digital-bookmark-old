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
                    <a class="navbar-link" ref="displayName">
                        {{ username }}
                    </a>

                    <div class="navbar-dropdown is-right">
                        <a class="navbar-item coming-soon" ref="goalSetup" @click="showGoalModal">
                            Edit the goal
                        </a>
                        <hr class="navbar-divider">
                        <a class="navbar-item coming-soon">
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
                    this.hasReminder = payload.hasReminder;

                    if (this.hasReminder) {
                        this.changeGoalMenuMessage();
                    } else {
                        this.$refs.goalSetup.classList.toggle('coming-soon', false);
                        this.$refs.goalSetup.innerText = 'Set the goal';
                    }

                    this.changeTarget();
                    this.$emit('reminder-checked', payload);
                }.bind(this))
                .catch(function ({ response }) {
                    this.$emit('error-found', response.data);
                }.bind(this));
            },

            /**
             * Changes the position of the indicator depending on the window width
             */
            changeTarget() {
                const deviceWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
                this.$refs.goalSetup.classList.toggle('menu-reminder', this.hasReminder === false);

                let displayTarget = (deviceWidth >= 1023) ? 'displayName' : 'navbarBurger';

                this.$refs.displayName.classList.remove('navbar-reminder');
                this.$refs.navbarBurger.classList.remove('navbar-reminder');

                this.$refs[displayTarget].classList.toggle('navbar-reminder', this.hasReminder === false);
            },

            /**
             * Trigger to start creating a goal
             */
            showGoalModal() {
                if (this.hasReminder === false) {
                    this.$emit('set-goal');
                }
            },

            /**
             * Change the menu message if there is already a goal
             */
            changeGoalMenuMessage() {
                this.hasReminder = true;
                this.$refs.goalSetup.classList.toggle('coming-soon', true);
                this.changeTarget();
            }
        },

        data() {
            return {
                hasReminder : false
            };
        },

        created() {
            this.checkForReminders();
            window.addEventListener('resize', this.changeTarget);
        }
    }
</script>

<style scoped>

</style>
