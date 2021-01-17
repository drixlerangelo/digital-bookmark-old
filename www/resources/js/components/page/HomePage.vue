<template>
    <div>
        <navbar :username="username" @reminder-checked="showGoalReminder"></navbar>

        <div class="slider">
            <div class="slides" ref="slides">
                <books-holder :entries="entries.todo" ref="todo" style="margin-right: 5%;">Books To Read</books-holder>
                <books-holder :entries="entries.doing" ref="doing">Currently Reading</books-holder>
                <books-holder :entries="entries.done" ref="done" style="margin-left: 5%;">Books Completed</books-holder>
            </div>
        </div>

        <div style="text-align: center;">
            <a class="link" ref="todoNavigation" @click="focusOnStage('todo')"></a>
            <a class="link" ref="doingNavigation" @click="focusOnStage('doing')"></a>
            <a class="link" ref="doneNavigation" @click="focusOnStage('done')"></a>
        </div>

        <notification ref="notifDialog"></notification>
    </div>
</template>

<script>
    import Navbar from '../homepage/Navbar.vue'
    import NotificationDialog from '../homepage/NotificationDialog';
    import BooksHolder from '../homepage/BooksHolder';

    export default {
        name: "HomePage",

        props : {
            username : { type : String }
        },

        components: {
            'navbar'       : Navbar,
            'notification' : NotificationDialog,
            'books-holder' : BooksHolder
        },

        methods : {
            /**
             * Makes the position fixed so it appears evenly horizontally
             *
             * @param {String} stage
             */
            focusOnStage(stage) {
                this.$refs.slides.scrollLeft = this.initPos[stage];
                this.setActive(stage);
            },

            /**
             * Gets the current position of the stages concerning the window
             */
            getStagePositions() {
                Object.keys(this.initPos).map(function (stage) {
                    this.initPos[stage] = this.$refs[stage].$el.offsetLeft;
                }.bind(this));
            },

            /**
             * Change the currently selected stage
             */
            changeNavigation() {
                this.getStagePositions();

                // After dragging the stage slides, get the closest stage displayed
                const findClosest = function (slides, position) {
                    let closest = Object.values(slides).reduce(function(prev, curr) {
                        return (Math.abs(curr - position) < Math.abs(prev - position)) ? curr : prev;
                    });

                    let keys = Object.keys(slides);
                    let values = Object.values(slides);
                    let indexOfClosest = values.indexOf(closest);

                    return keys[indexOfClosest];
                };

                this.setActive(findClosest(this.initPos, this.$refs.slides.scrollLeft));
                // Add a timeout so that it gets the closest slide near the end of the animation of drag
                setTimeout(function () {
                    this.setActive(findClosest(this.initPos, this.$refs.slides.scrollLeft));
                }.bind(this), 500);
            },

            /**
             * Indicate the currently displayed stage
             *
             * @param {String} target
             */
            setActive(target) {
                Object.keys(this.initPos).forEach(function (stage) {
                    this.$refs[stage + 'Navigation'].classList.toggle('active-stage', false);
                }.bind(this));
                this.$refs[target + 'Navigation'].classList.toggle('active-stage', true);
            },

            /**
             * Shows a notification if no goal was set
             */
            showGoalReminder(hasReminder) {
                if (hasReminder === false) {
                    this.$refs.notifDialog.openNotif('No goal set', 'You have not yet set a goal.');
                }
            },

            /**
             * Get the books with their corresponding status
             */
            loadEntries() {
                axios.get('/status/all').then(function ({ data }) {
                    const entries = data.data.entries;

                    if (entries.length > 0) {
                        for (let index = 0; index < entries.length; index ++) {
                            let entry = entries[index];
                            entry.logs = []; // Add this for the meantime while there's no log functionality

                            this.entries[entry.status].push(entry);
                        }
                    }
                }.bind(this)).catch(function ({ response }) {
                    this.$emit('error-found', response.data);
                }.bind(this));
            }
        },

        data() {
            return {
                initPos : { todo : 0, doing : 0, done : 0 },
                entries : { todo : [], doing : [], done : [] }
            };
        },

        mounted() {
            this.getStagePositions();
            this.focusOnStage('todo');

            // Add an event so mobile users can interact with the stages
            document.querySelectorAll('.slide').forEach(function (slide) {
                slide.addEventListener('touchmove', this.changeNavigation);
                slide.addEventListener('touchend', this.changeNavigation);
            }.bind(this));

            this.loadEntries();
        }
    }
</script>

<style scoped>
    @import '../../../css/home.css';
</style>
