<template>
    <div class="columns" style="margin-top: 1em;">
        <div class="column action-option left-option" @click="switchMode('left')" :style="leftStyle" ref="leftElement">
            {{ leftName }}
        </div>
        <div class="column action-option right-option" @click="switchMode('right')" :style="rightStyle" ref="rightElement">
            {{ rightName }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "ActionOptions",

        props : {
            options : { type : Array }
        },

        data() {
            return {
                leftName   : '',
                leftStyle  : {},
                rightName  : '',
                rightStyle : {}
            }
        },

        methods : {
            /**
             * Changes the mode from signup to login and vice versa
             *
             * @param {String} mode
             */
            switchMode(mode) {
                let inverseMode = (mode === 'right') ? 'left' : 'right';

                this[mode + 'Style'] = {background : '#FFF', color : '#BF2C1F'};
                this[inverseMode + 'Style'] = {background : '#BF2C1F', color : '#F2F2F2'};

                this.$refs[inverseMode + 'Element'].classList.add(mode + '-option-inactive');
                this.$refs[mode + 'Element'].classList.remove(inverseMode + '-option-inactive');

                this.$emit('mode-change', this[mode + 'Name']);
            }
        },

        created() {
            this.leftName = this.options[0];
            this.rightName = this.options[1];
        },

        mounted() {
            this.switchMode('right');
        }
    }
</script>

<style scoped lang="scss">
    .action-option {
        font-size: 1.1em;
        text-align: center;
        margin-bottom: 2px;
    }

    .left-option {
        border-radius: 15px;
        border-top-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .left-option-inactive {
        border-top-right-radius: 0;
        margin-right: 2px;
    }

    .right-option {
        border-radius: 15px;
        border-bottom-left-radius: 0;
        border-top-right-radius: 0;
    }

    .right-option-inactive {
        border-top-left-radius: 0;
        margin-left: 2px;
    }
</style>
