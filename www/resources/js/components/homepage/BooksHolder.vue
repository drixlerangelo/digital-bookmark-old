<template>
    <div class="slide">
        <div class="stage-overview">
            <span class="stage-title">
                <slot></slot>
            </span>
            <span class="stage-count">{{ entries.length }}</span>
        </div>
        <div class="book-list">
            <template v-for="(book, index) in entries">
                <book-row :book="book" :pos="index" :key="index"></book-row>
            </template>
        </div>
        <div class="stage-bottom">
            <button v-if="stage !== 'done'" :class="['button', 'show-book-form-button', 'is-fullwidth', stage + '-add-book']" @click="showBookForm">
                + Add New Book
            </button>
        </div>
    </div>
</template>
<script>
    import BookRow from './BookRow';

    export default {
        name: "BooksHolder",

        components : {
            'book-row' : BookRow
        },

        props : {
            entries : { type : Array },
            stage   : { type : String }
        },

        methods : {
            /**
             * Trigger for showing the book registration form
             */
            showBookForm() {
                if (this.stage === 'doing') { // TODO: Remove if changing status is available
                    this.$emit('prompt-book-registration', this.stage);
                }

            }
        }
    }
</script>

<style scoped>
    .slide {
        scroll-snap-align: start;
        flex-shrink: 0;
        border-radius: 10px;
        background: #BF2C1F;
        color: white;
        padding: 1em;
        font-family: 'Roboto';
    }

    .stage-overview {
        padding-bottom: 6px;
        border: 1px;
        border-bottom-style: solid;
        margin-bottom: 1em;
    }

    .stage-count {
        padding: 0px 6px 0px 6px;
        background: #A4A4A4;
        border-radius: 5px;
        float: right;
        min-width: 1.5em;
        width: auto;
        height: 1.5em;
        text-align: center;
    }

    .book-list {
        overflow-y: scroll;
    }

    .book-list::-webkit-scrollbar {
        width: 6px;
    }

    .book-list::-webkit-scrollbar-track {
        background: rgba(150, 150, 150, 0.4);
    }

    .book-list::-webkit-scrollbar-thumb {
        background: rgba(229, 229, 229, 0.4);
        border-radius: 50px;
    }

    .book-list::-webkit-scrollbar-thumb:hover,
    .book-list::-webkit-scrollbar-thumb:active {
        background: rgba(130, 130, 130, 1);
    }

    .stage-bottom {
        height: 4%;
        margin-top: 2%;
    }

    .show-book-form-button {
        background-color: transparent;
        border-color: #BF2C1F;
        color: white;
    }

    .show-book-form-button:hover {
        background-color: white;
        color: #BF2C1F;
    }
</style>
