<template>
    <div class="book-item slide-down"
        :style="setAnimationDelay()"
        @click="startLog"
        draggable="true"
        @dragstart="carryBook"
        @dragend="dropBook">
        <div class="book-cover" :style="setCoverBg()"></div>
        <div class="book-info">
            <div class="book-details">
                <div class="book-title" :title="book.name">{{ book.name }}</div>
                <div class="book-author" :title="book.author">by {{ book.author }}</div>
            </div>
            <div class="book-count">
                <div :title="displayPages()">{{ displayShortenedPages() }}</div>
                <div :title="displayWords()">{{ displayShortenedWords() }}</div>
            </div>

            <book-progress v-if="book.status === 'doing'"
                :total-pages="parseInt(book.num_pages)"
                :read-logs="book.logs"
                :total-words="parseInt(book.num_words)"
                @totalReadUpdated="updateTotalPagesRead"
            ></book-progress>
        </div>
    </div>
</template>
<script>
    import BookProgress from './BookProgress';

    /**
     * Sets the delay in animation for these rows to smoothly when unfolded
     *
     * @var {Number}
     */
    const LOAD_DELAY = 0.3; // in seconds

    /**
     * To avoid elements with many characters overflowing the another
     *
     * @var {Number}
     */
    const DISPLAY_CEIL = 100000;

    export default {
        name: "BookRow",

        props : {
            book  : { type : Object },
            pos   : { type : Number },
        },

        components : {
            'book-progress' : BookProgress
        },

        data() {
            return {
                total_pages_read : 0
            };
        },

        methods : {
            /**
             * Assign the animation-delay depending on its position in the list
             *
             * @returns {Object}
             */
            setAnimationDelay() {
                return { animationDelay : `${ LOAD_DELAY * this.pos }s` };
            },

            /**
             * Sets the book's cover image
             *
             * @returns {Object}
             */
            setCoverBg() {
                let background = (this.book.cover_photo_path === '') ? '#C4C4C4' : `url(${ this.book.cover_photo_path })`;

                return { background : background, backgroundSize : 'cover' };
            },

            /**
             * Add commas to numbers to make it more readable
             *
             * @param {Number} noCommaNum
             *
             * @param {Number} decimalPlaces
             *
             * @returns {String}
             */
            addNumericCommas(noCommaNum, decimalPlaces = 2) {
                return parseFloat(noCommaNum).toLocaleString('en-US', { minimumFractionDigits: decimalPlaces });
            },

            /**
             * Displays fully how many pages there are
             * and what has already been read.
             *
             * @returns {String}
             */
            displayPages() {
                let withCommas = {
                    total_pages_read : this.addNumericCommas(this.total_pages_read, 0),
                    total_pages      : this.addNumericCommas(this.book.num_pages, 0)
                };

                if (this.book.status === 'doing') {
                    return `${ withCommas.total_pages_read } of ${ withCommas.total_pages } pages had been read`;
                }

                return `${ withCommas.total_pages } pages`;
            },

            /**
             * Display the shortened version for pages information
             */
            displayShortenedPages() {
                return this.limitNumDisplayed(this.book.num_pages, DISPLAY_CEIL) + ' pages';
            },

            /**
             * Limits how many digits are displayed
             *
             * @returns {String}
             */
            limitNumDisplayed(number, ceiling) {
                return (number > ceiling) ? this.addNumericCommas(ceiling, 0) + '+' : this.addNumericCommas(number, 0);
            },

            /**
             * Displays fully how many words there are
             * and what has already been read.
             *
             * @returns {String}
             */
            displayWords() {
                // Estimate how many words are read based on how many pages are read
                let total_words_read = (this.total_pages_read / this.book.num_pages) * this.book.num_words;
                total_words_read = Math.floor(total_words_read);

                let withCommas = {
                    total_words_read : this.addNumericCommas(total_words_read, 0),
                    total_words      : this.addNumericCommas(this.book.num_words, 0)
                };

                if (this.book.status === 'doing') {
                    return `about ${ withCommas.total_words_read } of ${ withCommas.total_words } words had been read`;
                }

                return `${ withCommas.total_words } words`;
            },

            /**
             * Display the shortened version for words information
             */
            displayShortenedWords() {
                return this.limitNumDisplayed(this.book.num_words, DISPLAY_CEIL) + ' words';
            },

            /**
             * Sends a signal to open the create log modal
             */
            startLog() {
                window.eventBus.$emit('start-log', this.book);
            },

            /**
             * Broadcast that the book is being carried
             */
            carryBook() {
                window.eventBus.$emit('carry-item', this.book);
            },

            /**
             * Broadcast that the book was dropped
             */
            dropBook() {
                window.eventBus.$emit('drop-item');
            },

            /**
             * Updates the total pages read based on the calculation of logs
             */
            updateTotalPagesRead(totalPagesRead) {
                this.total_pages_read = totalPagesRead;
            }
        },

        watch : {
            total_pages_read() {
                this.$mount();
            }
        }
    }
</script>

<style scoped>
    .book-item {
        background: #F2F2F2;
        width: 97%;
        height: 7em;
        margin-top: 0.35em;
        margin-bottom: 0.35em;
        border: 1px;
        box-sizing: border-box;
        border-radius: 10px;
        display: flex;
        cursor: pointer;

        /* animation */
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(-100%);
            height: 0%;
        }

        75% {
            opacity: 0.5;
            transform: translateY(0%);
        }

        100% {
            opacity: 1;
        }
    }

    .slide-down {
        animation: fadeIn 0.5s linear;
        animation-fill-mode: both;
    }

    .book-cover {
        min-width: 4em;
        height: auto;
        margin-left: 0.5em;
        margin-right: 0.5em;
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }

    .book-info {
        display: block;
        position: relative;
    }

    .book-details {
        color: #BF1B28;
        margin-top: 0.5em;
    }

    .book-title {
        font-size: 14px;
    }

    .book-author {
        font-size: 11px;
    }

    .book-title, .book-author {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .book-count {
        left: 0;
    }
</style>
