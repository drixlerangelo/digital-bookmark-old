<template>
    <div class="modal-cover">
        <div class="modal-holder">
            <div class="modal-title" v-if="hasTitle">{{ title }}</div>
            <div class="modal-contents">
                <slot></slot>
                <div class="buttons is-centered">
                    <button :disabled="actionDisabled" class="button proceed-button is-rounded" @click="takeAction">{{ actionName }}</button>
                    <button class="button close-button is-rounded" @click="quitModal">{{ cancelName }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Modal",

        computed : {
            /**
             * Displays the title if there's one
             *
             * @returns {String}
             */
            hasTitle() {
                return this.title.length > 0;
            }
        },

        props : {
            title          : { type : String, default : '' },
            actionName     : { type : String, default : 'Register' },
            cancelName     : { type : String, default : 'Cancel' },
            actionDisabled : { type : Boolean, default : false }
        },

        methods : {
            /**
             * Make the content of the modal do something
             */
            takeAction() {
                this.$emit('modal-proceed');
            },

            /**
             * Notifies the modal to be closed
             */
            quitModal() {
                this.$emit('modal-close');
            }
        }
    }
</script>

<style scoped>
    .modal-cover {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 31;
    }

    .modal-holder {
        width: 20em;
        margin-left: auto;
        margin-right: auto;
        margin-top: 7em;
        z-index: 32;
    }

    .modal-title {
        background: #400707;
        color: #F2F2F2;
        text-align: center;
        font-size: 20px;
        padding: 1em 2em 1em 2em;
    }

    .modal-contents {
        background: #F2F2F2;
        min-height: 7em;
        height: 260px;
    }

    .proceed-button {
        color: white;
        background-color: #BF2C1F;
    }

    .proceed-button:hover {
        color: white;
        background-color: #DE3121;
    }

    .close-button {
        color: white;
        background-color: #999;
    }

    .close-button:hover {
        color: white;
        background-color: #888;
    }
</style>
