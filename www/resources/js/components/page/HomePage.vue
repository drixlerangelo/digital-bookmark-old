<template>
    <div>
        <navbar :username="username" @reminder-checked="showGoalReminder" @set-goal="prepareGoalCreation" ref="navbar"></navbar>

        <goal-display
            :reminder="reminder"
        ></goal-display>

        <div class="slider even-spacing">
            <div class="slides" ref="slides">
                <books-holder
                    :entries="entries.todo"
                    stage="todo"
                    ref="todo"
                    style="margin-right: 5%;"
                    @prompt-book-registration="prepareBookRegistration"
                >Books To Read</books-holder>
                <books-holder
                    :entries="entries.doing"
                    stage="doing"
                    ref="doing"
                    @prompt-book-registration="prepareBookRegistration"
                >Currently Reading</books-holder>
                <books-holder
                    :entries="entries.done"
                    stage="done"
                    ref="done"
                    style="margin-left: 5%;"
                >Books Completed</books-holder>
            </div>
        </div>

        <div style="text-align: center;">
            <a class="link" ref="todoNavigation" @click="focusOnStage('todo')"></a>
            <a class="link" ref="doingNavigation" @click="focusOnStage('doing')"></a>
            <a class="link" ref="doneNavigation" @click="focusOnStage('done')"></a>
        </div>

        <notification ref="notifDialog"></notification>

        <register-book-modal
            v-if="newBookModal.active"
            :stage="newBookModal.stage"
            @modal-close="closeBookRegistrationModal"
            @book-created="addNewBook"
        ></register-book-modal>

        <set-goal-modal
            v-if="goalModalActive"
            @modal-close="finishGoalCreation"
            @goal-created="setupGoalView"
        ></set-goal-modal>

        <log-book-modal
            v-if="newLogModal.active"
            :book="newLogModal.book"
            @modal-close="finishBookLog"
        ></log-book-modal>
    </div>
</template>

<script>
    import Navbar from '../homepage/Navbar.vue'
    import NotificationDialog from '../homepage/NotificationDialog';
    import BooksHolder from '../homepage/BooksHolder';
    import RegisterBookModal from '../homepage/RegisterBookModal';
    import SetGoalModal from '../homepage/SetGoalModal';
    import GoalDisplay from '../homepage/GoalDisplay';
    import LogBookModal from '../homepage/LogBookModal';

    export default {
        name: "HomePage",

        props : {
            username : { type : String }
        },

        components: {
            'navbar'              : Navbar,
            'notification'        : NotificationDialog,
            'books-holder'        : BooksHolder,
            'register-book-modal' : RegisterBookModal,
            'set-goal-modal'      : SetGoalModal,
            'goal-display'        : GoalDisplay,
            'log-book-modal'      : LogBookModal
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
             *
             * @param {Object}
             */
            showGoalReminder({ hasReminder, reminder }) {
                this.reminder = reminder;

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
                            this.convertLogsData(entry);

                            this.entries[entry.status].push(entry);
                        }
                    }
                }.bind(this)).catch(function ({ response }) {
                    this.$emit('error-found', response.data);
                }.bind(this));
            },

            /**
             * Shows a modal for book registration
             *
             * @param {String} stage
             */
            prepareBookRegistration(stage) {
                this.newBookModal.stage = stage;
                this.newBookModal.active = true;
            },

            /**
             * Closes the book registration modal
             */
            closeBookRegistrationModal() {
                this.newBookModal.active = false;
            },

            /**
             * Process the newly created book
             *
             * @param {Object} book
             */
            addNewBook(book) {
                this.entries[book.status].push(book);
                this.closeBookRegistrationModal();
            },

            /**
             * Show a modal for reminder registration
             */
            prepareGoalCreation() {
                this.goalModalActive = true;
            },

            /**
             * Finishes reminder registration
             */
            finishGoalCreation() {
                this.goalModalActive = false;
            },

            /**
             * Process the newly created reminder
             *
             * @param {Object} reminder
             */
            setupGoalView(reminder) {
                this.goalModalActive = false;
                this.reminder = reminder;
                this.$refs.navbar.changeGoalMenuMessage();
            },

            /**
             * Closes and resets the log creation modal
             */
            finishBookLog() {
                this.newLogModal.active = false;
                this.newLogModal.book = {};
            },

            /**
             * Convert the log data received from the server
             *
             * @param {Object} entry
             */
            convertLogsData(entry) {
                if (entry.status === 'doing') {
                    for (let log of entry.logs) {
                        // Make it an integer to avoid repetitive data type conversion
                        log.status_id = parseInt(log.status_id);
                        log.pages_read = parseInt(log.pages_read);

                        // Convert to a timestamp for ease in the calculation
                        log.start_time = new Date(log.start_time).getTime() / 1000;
                        log.end_time = new Date(log.end_time).getTime() / 1000;
                    }
                }
            }
        },

        data() {
            return {
                initPos : { todo : 0, doing : 0, done : 0 },
                entries : { todo : [], doing : [], done : [] },
                newBookModal : { stage : '', active : false },
                goalModalActive : false,
                reminder : {},
                newLogModal : {active : false, book : {}}
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
        },

        created() {
            window.eventBus.$on('start-log', function (book) {
                this.newLogModal.active = true;
                this.newLogModal.book = book;
            }.bind(this));

            window.eventBus.$on('log-created', function (log) {
                for (let index = 0; index < this.entries.doing.length; index++) {
                    let book = this.entries.doing[index];

                    if (parseInt(book.status_id) === parseInt(log.status_id)) {
                        book.logs.push(log);
                    }
                }
                this.finishBookLog();
            }.bind(this));
        }
    }
</script>

<style scoped>
    @import '../../../css/home.css';
</style>
