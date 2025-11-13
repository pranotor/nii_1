<template>
    <vs-prompt title="Compose Email" :active.sync="active" accept-text="Send">
       <div class="con-exemple-prompt">
        <vs-input  size="large" label-placeholder="To" v-model="to" class="w-100 mb-4" />
        <vs-input size="large" label-placeholder="Subject" v-model="subject" class="w-100 mb-4" />
        <vs-input size="large" type="cc" label-placeholder="cc" v-model="cc" class="w-100 mb-4" />
        <vs-textarea placeholder="Type message" v-model="message" class="w-100 mb-4" counter="200" :counter-danger.sync="counterDanger" />
        <vs-upload action="https://jsonplaceholder.typicode.com/posts/" text="Attachments" :show-upload-button="false" v-model="img" />
        
       </div>
    </vs-prompt>   
</template>
<script>
export default {
    data: () => ({
        to: "",
        subject: "",
        cc: "",
        message: "",
        img: "",
        counterDanger: false
    }),
    props:{
        composePrompt :{
            type: Boolean,
            default: false
        },
        close:{
            type: String
        },
        acceptAlert: {
            type: String
        }
    },
    computed:{
        active: {
            get () { return this.composePrompt },
            set (value) { this.$emit('update:composePrompt', value) },
        },
    }
}
</script>