<template>

</template>

<script>
    export default {
        name: "UserForm",

        props : {
            mode   : { type : String },
            inputs : { type : Object },
            submit : { type : Boolean }
        },

        watch : {
            /**
             * Checks if an order to submit is requested to pass the credentials to the backend
             */
            submit() {
                if (this.submit) {
                    axios.post('/user/' + this.mode.toLowerCase(), this.inputs)
                    .then(function ({ data }) {
                        const payload = data.data;

                        this.$emit('submit-done', {
                            granted : payload.wasPassed,
                            message : data.message
                        });
                    }.bind(this))
                    .catch(function ({ response }) {
                        this.$emit('error-found', response.data);
                    }.bind(this));
                }
            }
        }
    }
</script>

<style scoped>

</style>
