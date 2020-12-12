<template>
    <button
        class="button action-button is-rounded"
        :disabled="disabled"
        @click="submitStart"
    >{{ currentMode }}</button>
</template>

<script>
    export default {
        name: "ActionButton",

        props : {
            currentMode : { type : String },
            disabled    : { type : Boolean }
        },

        methods : {
            /**
             * Initiates the event of submitting the form
             */
            submitStart() {
                this.$emit('submit-start');
            }
        }
    }
</script>

<style scoped>
    .action-button {
        color: white;
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-top: 1em;
        background-color: #BF2C1F;
    }

    .action-button:hover {
        background-color: #de3121
    }
</style>
