<template>
    <div class="container mx-auto p-4 sm:p-6">
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
        <div v-else class="grid gap-4 sm:gap-6 md:gap-8 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="post in posts"
                :key="post.id"
                class="bg-white rounded-lg shadow-md p-4 sm:p-6 transition-transform hover:scale-105 overflow-hidden flex flex-col"
            >
                <h2 class="text-lg sm:text-xl md:text-2xl font-semibold mb-2 text-gray-800">{{ post.title }}</h2>
                <p class="text-gray-700 mb-2 sm:mb-4 text-sm sm:text-base">{{ post.body }}</p>

                <div v-if="post.images && post.images.length > 0" class="flex gap-2 sm:gap-4 overflow-x-auto mt-2">
                    <img
                        v-for="image in post.images"
                        :key="image.id"
                        :src="image.file_path"
                        alt="Post Image"
                        class="w-24 sm:w-32 h-24 sm:h-32 object-cover rounded-md shadow cursor-pointer"
                        @click="openImageModal(image.file_path)"
                    />
                </div>

                <div class="mt-4 flex items-center justify-center space-x-3 group relative">
                    <!-- Checkbox Toggle Switch -->
                    <label class="flex items-center cursor-pointer relative">
                        <input type="checkbox" class="sr-only" v-model="post.is_active" @change="updatePostStatus(post.id, post.is_active)" />
                        <div class="w-10 h-5 bg-gray-200 rounded-full shadow-inner transition-colors duration-300 ease-in-out" :class="{ 'bg-green-500': post.is_active }"></div>
                        <div class="dot absolute w-5 h-5 bg-white rounded-full shadow transition-transform duration-300 ease-in-out transform" :class="{ 'translate-x-5': post.is_active }"></div>
                    </label>

                    <!-- Text Display for Active/Inactive -->
                    <span class="text-lg font-semibold" :class="{ 'text-green-600': post.is_active, 'text-gray-500': !post.is_active }">
                        {{ post.is_active ? 'Active' : 'Inactive' }}
                    </span>

                        <!-- Tooltip Text on Hover -->
                    <span v-if="post.is_active" class="absolute bottom-full mb-2 p-1 text-xs text-white bg-black rounded opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        Deactivate this post!
                    </span>
                    <span v-else class="absolute bottom-full mb-2 p-1 text-xs text-white bg-black rounded opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        Activate this post!
                    </span>
                </div>

                <div class="flex items-center justify-center mt-4 p-3">
                    <button id="price-button" class="bg-purple-100 text-pink-700 font-bold text-sm sm:text-base inline-block px-3 py-2 rounded-lg shadow-md">
                        Price: {{ post.price }} &euro;
                    </button>
                </div>

            </div>
        </div>

        <!-- Image Modal -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50 p-4"
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

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: white;
    border-radius: 8px;
    padding: 20px;
    max-width: 500px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
}
</style>


<script>
import axios from 'axios';
import { debounce } from 'lodash';

export default {
    data() {
        return {
            posts: [],
            loading: true,
            isModalOpen: false,
            selectedImage: null,
            searchQuery: '',
            isEditModalOpen: false, 
            editedPost: {
                id: null,
                title: '',
                body: '',
                price: '',
                phone_number: '',
                images: [],         
                newImages: [],
            },

            // Holds the preview URLs for display
            previews: [],   
        };
    },
    mounted() {
        this.fetchPosts();
    },
    methods: {
        async fetchPosts() {
            try {
                const response = await axios.get('/posts/all');
                this.posts = response.data;
            } catch (error) {
                console.error("Error fetching posts:", error);
            } finally {
                this.loading = false;
            }
        },
        openImageModal(imagePath) {
            this.selectedImage = imagePath;
            this.isModalOpen = true;
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
                .get('/posts/search/user', {
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

        async updatePostStatus(postId, isActive) {
            try {
                const response = await axios.patch(`/posts/${postId}`, {
                    is_active: isActive ? 1 : 0
                });
            console.log("Post updated successfully:", response.data);
            } catch (error) {
            console.error("Error updating post:", error);
            }
        }

    },
};
</script>