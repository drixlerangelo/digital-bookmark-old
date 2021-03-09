<template>
    <div class="even-spacing">
        <progress class="progress is-small is-danger goal-meter" value="40" max="100"></progress>
        <div class="goal-message">You have no goals yet. Better get started!</div>
    </div>
</template>

<script>
    export default {
        name: "GoalDisplay",
    }
</script>

<style scoped>
    .goal-message, .goal-meter {
        width: 60%;
        margin-left: auto;
        margin-right: auto;
        cursor: pointer;
    }

    .goal-meter {
        margin-bottom: 0px !important;
    }

    .goal-message {
        text-align: center;
        color: #BF1B28;
        font-family: Roboto;
    }

    .goal-meter::-webkit-progress-value {
        background: #BF1B28 !important;
    }

    .goal-meter::-webkit-progress-bar {
        background: #C4C4C4 !important;
    }
</style>
