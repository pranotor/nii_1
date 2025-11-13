<template>
    <div class="mt-4 border-top">
        <div class="d-flex pt-4">
            <img
            :src="require('@/assets/images/users/'+ currentEmail.img)"
            class="rounded-circle mr-2 flex-shrink-0"
            alt="user"
            width="40"
            />
            <div>
                <h4 class="font-weight-bold">{{currentEmail.from_name}}</h4>
                <span>to: {{currentEmail.from_email}}</span>
            </div>
            <span class="ml-auto">{{currentEmail.time}}</span>
        </div> 
        <p class="mt-4 mb-4">
           {{currentEmail.message}}  
        </p>
        <h4 v-if="currentEmail.attachments" class="border-top pt-3">Attachment : <span class="label bg-primary"> {{currentEmail.attachments}}</span></h4>
        <vs-card class="mt-3 p-3">Click here to <router-link to="">Reply</router-link> or <router-link to="">Forward</router-link></vs-card>    
    </div>  
</template>
<script>
export default{
    data:()=>({
        
    }),
    props: {
        openCurrentId: {
            required: true
        }
    },
    computed: {
        currentEmail() {
            return this.$store.getters['getCurrentMail'](this.openCurrentId)
        },
    }
}
</script> 
