<template>
  <div>
      
      <!-- Check if conversations array is empty -->
      <div v-if="!conversations || conversations.length === 0" class="text-center text-gray-500 p-4">
        No conversations so far.
      </div>

    <!-- Display conversations if available -->
    <div 
      class="mb-6 p-6 bg-white shadow-md rounded-lg border border-gray-200" 
      v-for="c in conversations" 
      :key="c.id"
    >
      <!-- Conversation Title -->
      <h3 class="text-xl font-bold text-gray-800 mb-4">
        {{ c.post.title }}
      </h3>

      <!-- Messages List -->
      <ul class="space-y-3 mb-4">
        <li 
          v-for="message in messages.filter(msg => msg.conversation_id === c.id)" 
          :key="message.id" 
          class="p-4 bg-gray-100 rounded-md shadow-sm"
        >
          <strong class="block text-sm font-medium text-gray-700">
            {{ message.sender_name }}:
          </strong>
          
          <div class="flex items-center justify-between space-x-2">
            <!-- Message Content -->
            <span class="text-gray-900 text-base font-medium">
              {{ message.content }}
            </span>

            <!-- Read Status -->
            <span
              class="text-xs font-semibold px-2 py-1 rounded-full"
              :class="message.is_read ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
            >
              {{ message.is_read ? 'Read' : 'Unread' }}
            </span>
          </div>

        </li>
      </ul>

      <!-- Message Input Form -->
      <form @submit.prevent="sendMessage(c.id)" class="flex flex-col space-y-3">
        <input 
          v-model="newMessages[c.id]" 
          placeholder="Type a message..." 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />

        <!-- Error Message -->
        <div class="mt-4 flex flex-col items-center justify-center">
          <p v-if="errors[c.id]" class="text-red-500 text-sm mt-1">{{ errors[c.id] }}</p>
        </div>

        <div class="mt-4 flex flex-col items-center justify-center">
          <button 
            type="submit" 
            class="w-24 py-2 bg-blue-600 text-white font-semibold rounded-lg 
              shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Send
          </button>
        </div>
      </form>
    </div>

  </div>
</template>
  
<script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        user: null,
        messages: [],
        conversations: [],
        newMessages: {},
        errors: {},
      };
    },
    async created() {
      this.fetchConversations();
    },
    async mounted() {
      try {
        const response = await axios.get('/user'); 
        this.user = response.data; 
      } catch (error) {
        console.error('Error fetching authenticated user:', error);
      }
    },
    methods: {
      validateForm() {
        const errors = {};
        if (!this.category_id) 
          errors.category_id = "Category is required.";
        this.errors = errors;
        return Object.keys(errors).length === 0;
      },
      
      async fetchConversation(conversationId) {
        const response = await axios.get(`/messages/${conversationId}`);
        return response.data;
      },

      async fetchMessages() {
          for (const element of this.conversations) {
            let database_messages = await this.fetchConversation(element.id);            
            database_messages.forEach(e => {
              this.messages.push({ 
                conversation_id: element.id,
                id: e.id, 
                sender_name: e.sender.name, 
                receiver_name: e.receiver.name, 
                is_read: e.is_read,
                content: e.content
              });
            });
          }

          // Extract unread message IDs
          let unreadMessageIds = this.messages
          .filter(message => message.is_read === 0 && message.sender_name != this.user.name)
          .map(message => message.id);  

          if (unreadMessageIds.length > 0) {
            await this.markMessagesAsRead(unreadMessageIds);
            this.messages = [];
            this.fetchMessages();
          }
      },

      async fetchConversations() {
        const response = await axios.get(`/auth_conversations/`);
        this.conversations = response.data;
        this.fetchMessages();
      },
  
      async sendMessage (conversationId) {

        if (!this.newMessages[conversationId] || this.newMessages[conversationId].trim() === "") {
          // If the message is empty or only whitespace, show an error
          this.errors[conversationId] = "Message cannot be empty.";
          return;
        }

        if (this.newMessages[conversationId].length > 500) {
          // If the message exceeds 500 characters, show an error
          this.errors[conversationId] = "Message cannot exceed 500 characters.";
          return;
        }

        // Clear errors if validation passes
        this.errors[conversationId] = null;

        const response = await axios.get(`/last_message/${conversationId}`);
        let last_message = response.data;

        let receiverId = last_message.sender_id === this.user.id 
          ? last_message.receiver_id 
          : last_message.sender_id;

        await axios.post('/messages', {
          conversation_id: conversationId,
          sender_id: this.user.id,
          receiver_id: receiverId,
          content: this.newMessages[conversationId],
        });
        this.newMessages[conversationId] = '';
        this.messages = [];
        this.fetchConversations();
      },

      async markMessagesAsRead(messageIds) {
        await axios.post('/messages/mark-as-read', {
          message_ids: messageIds,
        });
      },
    },
  };
  </script>
  