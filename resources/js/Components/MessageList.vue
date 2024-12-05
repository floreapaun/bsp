<template>
    <div>
        <div class="space-y-4">
            <div
                v-for="message in messages"
                :key="message.id"
                class="flex items-start space-x-3 p-4 bg-white rounded-lg shadow-md"
            >
                <div class="flex-shrink-0 bg-blue-500 text-white font-bold rounded-full w-10 h-10 flex items-center justify-center">
                    {{ message.sender.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">
                        <span class="sender-name">{{ message.sender.name }}</span>
                    </p>
                    <p class="mt-1 text-gray-600">{{ message.content }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            messages: [],
        };
    },
    props: ['postId'],
    methods: {
        fetchMessages() {
            axios.get(`/messages/${this.postId}`).then((response) => {
                this.messages = response.data;
            });
        },
        refresh() {
            this.fetchMessages();
        },
    },
    mounted() {
        // Fetch messages when the component is mounted
        this.fetchMessages();
    },
};
</script>

<style>
.sender-name {
    background-color: yellow;
}
</style>
