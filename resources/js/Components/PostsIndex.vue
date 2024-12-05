<template>
    <div class="container mx-auto p-6">
        <input
            type="text"
            v-model="searchQuery"
            @input="handleSearch"
            placeholder="Search posts..."
            class="w-full p-2 mb-4 border rounded-lg"
        />

        <div v-if="loading" class="text-center text-gray-500">Searching...</div>

        <div v-if="!loading && posts.length === 0" class="text-center text-gray-500">
            No results found.
        </div>

        <div v-if="loading" class="text-center text-gray-500">Loading posts...</div>
        <div v-else class="grid gap-8 lg:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="post in posts"
                :key="post.id"
                class="bg-white rounded-lg shadow-md p-6 transition-transform hover:scale-105 overflow-hidden flex flex-col"
            >
                <h2 class="text-2xl font-semibold mb-2 text-gray-800">{{ post.title }}</h2>
                <p class="text-gray-700 mb-4 overflow-hidden text-ellipsis">{{ post.body }}</p>

                <div v-if="post.images && post.images.length > 0" class="flex gap-4 overflow-x-auto">
                    <img
                        v-for="image in post.images"
                        :key="image.id"
                        :src="image.file_path"
                        alt="Post Image"
                        class="w-32 h-32 object-cover rounded-md shadow cursor-pointer"
                        @click="openImageModal(image.file_path)"
                    />
                </div>

                <div class="flex items-center justify-center mt-4 p-3">
                    <button id="price-button" class="bg-purple-100 text-pink-700 font-bold text-sm sm:text-base inline-block px-3 py-2 rounded-lg shadow-md">
                        Price: {{ post.price }} &euro;
                    </button>
                </div>

                <!-- Contact Seller Section -->
                <div class="mt-4 flex flex-col items-center justify-center">
                    <button
                        @click="togglePhoneNumber(post.id)"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600"
                    >
                        Contact Seller
                    </button>
                    <p v-if="visiblePhoneNumbers[post.id]" class="mt-2 text-lg text-gray-800">
                        📞 {{ post.phone_number }}
                    </p>

                </div>
                
                <div class="mt-4 flex flex-col items-center justify-center">
                    <!-- Show chat button -->
                    <button
                        @click="toggleChat(post.id)"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                        {{ isChatVisible(post.id) ? "Hide Chat" : "Show Chat" }}
                    </button>

                    <!-- Chat component, only visible when showChat is true -->
                    <div
                        v-if="isChatVisible(post.id)"
                        class="mt-4 flex flex-col items-center justify-center"
                    >
                        <Chat :postId="post.id" :senderId="authenticatedUser.id" />
                    </div>
                </div>

            </div>
        </div>

        <!-- Image Modal -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50"
            @click.self="isModalOpen = false"
        >
            <img :src="selectedImage" alt="Expanded Image" class="max-w-full max-h-full rounded-lg shadow-lg">
            <button
                class="absolute top-4 right-4 text-white text-2xl font-bold"
                @click="isModalOpen = false"
            >
                &times;
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { debounce } from 'lodash';
import Chat from './Chat.vue';

export default {
    components: {
        Chat
    },
    data() {
        return {
            posts: [],
            authenticatedUser: null,
            loading: true,
            isModalOpen: false,
            selectedImage: null,
            visiblePhoneNumbers: [],
            searchQuery: '',
            visibileChats: [], 
        };
    },
    mounted() {
        this.fetchPosts();
    },
    beforeMount() {
        this.getUser();
    },
    methods: {
        async fetchPosts() {
            try {
                const response = await axios.get('/posts');
                this.posts = response.data;
                console.log(this.posts);
            } catch (error) {
                console.error("Error fetching posts:", error);
            } finally {
                this.loading = false;
            }
        },
        async getUser() {
            axios.get('/user')
                .then(response => {
                    this.authenticatedUser = response.data;
                    console.log(this.authenticatedUser);
                })
                .catch(error => {
                    console.error('Error fetching user:', error);
                });
        },
        openImageModal(imagePath) {
            this.selectedImage = imagePath;
            this.isModalOpen = true;
        },
        togglePhoneNumber(postId) {
            // Initialize the value if it doesn't exist, then toggle it
            if (!this.visiblePhoneNumbers[postId]) {
                this.visiblePhoneNumbers[postId] = true;
            } else {
                this.visiblePhoneNumbers[postId] = !this.visiblePhoneNumbers[postId];
            }
        },
        toggleChat(postId) {
            this.visibleChats[postId] = this.visibleChats[postId] === 1 ? 0 : 1;
        },
        isChatVisible(postId) {
            return this.visibleChats[postId] === 1; // Check visibility
        },
        handleSearch: debounce(function () {
            if (this.searchQuery.trim() === '') {
                
                // Clear results if input is empty
                this.posts = []; 
                
                this.loading = false;
                return;
            }

            this.loading = true;
            axios
                .get('/posts/search', {
                    params: {
                        query: this.searchQuery,
                    },
                })
                .then((response) => {
                    this.posts = response.data;
                })
                .catch((error) => {
                    console.error("Error fetching search results:", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        }, 300), 
    },
};
</script>

<style>
/* Additional styling if needed */
</style>
