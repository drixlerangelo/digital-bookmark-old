<template>
    <div class="notification notif-dialog is-danger" ref="dialog">
        <button class="delete" @click="closeNotif"></button>
        <div class="notif-title">{{ title }}</div>
        <div class="notif-text">{{ shortenedText }}</div>
    </div>
</template>

<script>
    export default {
        name: "NotificationDialog",

        data() {
            return {
                title : '',
                text  : ''
            };
        },

        methods : {
            /**
             * Shows the notifications
             *
             * @param {String} title
             *
             * @param {String} text
             */
            openNotif(title, text) {
                this.title = title;
                this.text = text || '';

                if (this.text.length > 0) {
                    this.$refs.dialog.classList.remove('slide-right');
                    this.$refs.dialog.classList.add('slide-left');

                    setTimeout(function () {
                        this.closeNotif();
                    }.bind(this), 5000); // 5 seconds
                }
            },

            /**
             * Hides the notification
             */
            closeNotif() {
                this.title = '';
                this.text = '';
                this.$refs.dialog.classList.add('slide-right');
                this.$refs.dialog.classList.remove('slide-left');
            }
        },

        computed : {
            /**
             * Fits the text inside the notification
             *
             * @returns {String}
             */
            shortenedText() {
                let text = this.text || '';

                if (text.length >= 40) {
                    return text.slice(0, 37).trim() + '...';
                }

                return text;
            }
        }
    }
</script>

<style scoped>
    .notif-dialog {
        /* style */
        width: 300px;
        height: 80px;
        bottom: 15px;
        right: 10px;
        position: absolute;
        font-style: normal;
        font-weight: normal;
        background-color: #BF1B28 !important;
        color: white;
        opacity: 0%;

        /* animation */
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    .notif-title {
        top: 10px;
        left: 20px;
        font-size: 24px;
        line-height: 28px;
        position: inherit;
    }

    .notif-text {
        top: 45px;
        left: 20px;
        font-size: 14px;
        line-height: 16px;
        position: inherit;
    }

    @-webkit-keyframes fade-out-right {
        0% {
            opacity: 0.95;
            -webkit-transform: translateX(0);
        }
        100% {
            opacity: 0;
            -webkit-transform: translateX(100px);
        }
    }

    @keyframes fade-out-right {
        0% {
            opacity: 0.95;
            transform: translateX(0);
        }
        100% {
            opacity: 0;
            transform: translateX(100px);
        }
    }

    @-webkit-keyframes fade-in-left {
        0% {
            opacity: 0;
            -webkit-transform: translateX(100px);
        }
        100% {
            opacity: 0.95;
            -webkit-transform: translateX(0px);
        }
    }

    @keyframes fade-in-left {
        0% {
            opacity: 0;
            transform: translateX(100px);
        }
        100% {
            opacity: 0.95;
            transform: translateX(0px);
        }
    }

    .slide-right {
        -webkit-animation-name: fade-out-right;
        animation-name: fade-out-right;
    }

    .slide-left {
        -webkit-animation-name: fade-in-left;
        animation-name: fade-in-left;
    }
</style>
