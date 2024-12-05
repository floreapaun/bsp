<template>
    <form @submit.prevent="sendMessage">
        <div class="flex items-center space-x-3 p-4 bg-gray-100 rounded-lg shadow-md">
            <input
                v-model="content"
                type="text"
                placeholder="Type your message"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <button
                type="submit"
                class="px-6 py-2 text-white bg-blue-500 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2"
            >
                Send
            </button>
        </div>
    </form>
</template>

<script>
import axios from "axios";

export default {
    props: ['postId', 'senderId'],
    data() {
        return {
            content: '',
        };
    },
    methods: {
        async sendMessage() {
            await axios.post('/messages', {
                sender_id: this.senderId,
                post_id: this.postId,
                content: this.content,
            });
            this.content = '';
            this.$emit('message-sent');
        },
    },
};
</script>
