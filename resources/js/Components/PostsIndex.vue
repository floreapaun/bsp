<template>
    <div class="container mx-auto p-6">
        <input
            type="text"
            v-model="searchQuery"
            @input="filterPosts"
            placeholder="Search posts..."
            class="w-full p-2 mb-4 border rounded-lg"
        />

        <!-- Filters -->
        <div class="flex flex-wrap items-center justify-center gap-4 m-4 p-4 bg-gray-50 rounded-lg shadow-md">
            <!-- Location Filter -->
            <div class="flex flex-col items-center">
                <label for="location-filter" class="text-sm font-semibold text-gray-700 mb-1">Location</label>
                <select
                    id="location-filter"
                    v-model="selectedLocation"
                    @change="filterPosts"
                    class="p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 w-44"
                >
                    <option value="">All Locations</option>
                    <option
                        v-for="location in locations"
                        :key="location.id"
                        :value="location.id"
                    >
                        {{ location.name }}
                    </option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-col items-center">
                <label for="category-filter" class="text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select
                    id="category-filter"
                    v-model="selectedCategory"
                    @change="filterPosts"
                    class="p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 w-44"
                >
                    <option value="">All Categories</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <!-- Price Range Filters -->
            <div class="flex flex-col items-center">
                <label class="text-sm font-semibold text-gray-700 mb-1">Price Range</label>
                <div class="flex items-center gap-2">
                    <input
                        type="number"
                        v-model.number="priceRange.min"
                        @input="filterPosts"
                        placeholder="Min"
                        class="p-2 border border-gray-300 rounded-lg shadow-sm w-20 focus:ring focus:ring-blue-300 w-24"
                    />
                    <span class="text-gray-500">-</span>
                    <input
                        type="number"
                        v-model.number="priceRange.max"
                        @input="filterPosts"
                        placeholder="Max"
                        class="p-2 border border-gray-300 rounded-lg shadow-sm w-20 focus:ring focus:ring-blue-300 w-24"
                    />
                </div>
            </div>
        </div>

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

                <div class="m-2 flex flex-col items-center justify-center">
                    <!-- Location Text with Hover Effect -->
                    <span class="text-xl font-semibold text-indigo-600 bg-indigo-100 rounded-lg px-4 py-2 shadow-lg hover:bg-indigo-200 transition duration-300 ease-in-out transform hover:scale-105">
                        Category: {{ post.category.name }}
                    </span>
                </div>

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

                <div 
                    id="location-display" 
                    class="flex items-center space-x-4 bg-gradient-to-r from-blue-50 to-blue-200 text-blue-900 font-semibold text-sm sm:text-base px-4 py-3 rounded-lg shadow-lg"
                >
                    <!-- Location Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path 
                        d="M10 20a1 1 0 01-.832-.445C7.27 16.843 4 12.713 4 9a6 6 0 1112 0c0 3.713-3.27 7.843-5.168 10.555A1 1 0 0110 20zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        />
                    </svg>

                    <!-- Location Text -->
                    <span>{{ post.location.name}}</span>
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

                    <div v-if="visiblePhoneNumbers[post.id]" class="mt-4 p-4 bg-gray-50 rounded-lg shadow-md">
                        <div class="flex flex-col items-center space-y-4">
                            <!-- Phone Number Display -->
                            <p class="text-lg font-semibold text-gray-800">
                            📞 {{ post.phone_number }}
                            </p>

                            <!-- Input Field -->
                            <input
                                v-model="newMessages[post.id]"
                                placeholder="Type a message for the seller"
                                class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />

                            <!-- Error Message -->
                            <div class="mt-4 flex flex-col items-center justify-center">
                                <p v-if="errors[post.id]" class="text-red-500 text-sm mt-1">{{ errors[post.id] }}</p>
                            </div>

                            <!-- Send Button -->
                            <button
                                @click="sendMessage(post.id, post.user_id)"
                                class="px-6 py-2 text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                            Send
                            </button>
                            <div v-if="countdown > 0" class="text-lg text-gray-800">
                                Redirecting in {{ countdown }} seconds...
                            </div>
                        </div>
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
import Chat from './Chat.vue';

export default {
    components: {
        Chat
    },
    data() {
        return {
            posts: [],
            categories: [],
            locations: [],
            authenticatedUser: null,
            loading: true,
            isModalOpen: false,
            selectedImage: null,
            visiblePhoneNumbers: [],
            searchQuery: '',
            newMessages: {},
            countdown: 0,
            errors: {},
            selectedCategory: "",
            selectedLocation: "",
            priceRange: {
                min: null,
                max: null,
            },
        };
    },
    mounted() {
        this.fetchPosts();
        this.fetchCategories();
        this.fetchLocations();
        this.getAuthenticatedUser();
    },
    methods: {
        async fetchPosts() {
            try {
                const response = await axios.get('/posts');
                this.posts = response.data;
            } catch (error) {
                console.error("Error fetching posts:", error);
            } finally {
                this.loading = false;
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get("/categories");
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchLocations() {
            try {
                const response = await axios.get("/locations");
                this.locations = response.data;
            } catch (error) {
                console.error("Error fetching locations:", error);
            }
        },
        async getAuthenticatedUser() {
            axios.get('/user')
                .then(response => {
                    this.authenticatedUser = response.data;
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
        async sendMessage(postId, receiverUserId) {

            if (!this.newMessages[postId] || this.newMessages[postId].trim() === "") {
                // If the message is empty or only whitespace, show an error
                this.errors[postId] = "Message cannot be empty.";
                return;
            }

            if (this.newMessages[postId].length > 500) {
                // If the message exceeds 500 characters, show an error
                this.errors[postId] = "Message cannot exceed 500 characters.";
                return;
            }

            // Clear errors if validation passes
            this.errors[postId] = null;
            
            let conversation = await axios.post('/conversations', { 
                post_id: postId,
                user_two_id: receiverUserId,
            });
            await axios.post('/messages', {
                conversation_id: conversation.data.id,
                receiver_id: receiverUserId,
                content: this.newMessages[postId],
            });
            this.newMessages[postId] = '';
            this.startCountdown();
        },
        startCountdown() {
            this.countdown = 5;

            const interval = setInterval(() => {
                if (this.countdown > 1) {
                    this.countdown -= 1; 
                } else {
                    clearInterval(interval); 
                    this.redirectToPage(); 
                }
            }, 1000);
        },
        redirectToPage() {
            window.location.href = "/messenger";
        },
        filterPosts() {
            this.loading = true;

            const params = {
                query: this.searchQuery,
                location: this.selectedLocation || null,
                category: this.selectedCategory || null,
                minPrice: this.priceRange.min || null,
                maxPrice: this.priceRange.max || null,
            };

            axios
                .get("/posts/search/filter", { params })
                .then((response) => {
                    this.posts = response.data;
                })
                .catch((error) => {
                    console.error("Error filtering posts:", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        }, 
    },
};
</script>

<style>
</style>
