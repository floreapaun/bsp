<template>
    <div>
        <MessageList
            :postId="postId"
            ref="messageList"
        />
        <MessageForm :postId="postId" :senderId="senderId" @message-sent="refreshMessages" />
    </div>
</template>

<script>
import MessageList from './MessageList.vue';
import MessageForm from './MessageForm.vue';

export default {
    components: { MessageList, MessageForm },
    props: ['postId', 'senderId'],
    methods: {
        refreshMessages() {
            this.$refs.messageList.refresh();
        },
    },
};
</script>
