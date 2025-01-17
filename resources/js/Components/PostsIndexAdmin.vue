<template>
    <div class="container mx-auto p-4 sm:p-6">
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

            <!-- Condition Filters -->
            <div class="flex flex-col items-center">
                <label for="condition-filter" class="text-sm font-semibold text-gray-700 mb-1">Condition</label>
                <select
                    id="category-filter"
                    v-model="selectedCondition"
                    @change="filterPosts"
                    class="p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 w-44"
                >
                    <option value="new">New</option>
                    <option value="used">Used</option>
                </select>
            </div>

            <!-- Active/Inactive Filter -->
            <div class="flex flex-col items-center">
                <label for="status-filter" class="text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select
                    id="status-filter"
                    v-model="selectedStatus"
                    @change="filterPosts"
                    class="p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-300 w-44"
                >
                    <option value="-1">All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

        </div>

        <div v-if="loading" class="text-center text-gray-500">Searching...</div>

        <div v-if="!loading && posts.length === 0" class="text-center text-gray-500">
            No results found.
        </div>

        <div v-if="loading" class="text-center text-gray-500">Loading posts...</div>
        <div v-else class="grid gap-4 sm:gap-6 md:gap-8 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
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

                <div class="flex items-center justify-center mt-1">
                    <button 
                        id="condition-button" 
                        class="flex items-center space-x-2 bg-purple-100 text-pink-700 font-bold text-sm sm:text-base px-3 py-2 rounded-lg shadow-md"
                    >
                        <!-- Checkmark Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <!-- Condition Text -->
                        <span>Condition: {{ post.condition }}</span>
                    </button>
                </div>

                <div class="mt-4 flex items-center justify-center space-x-3 group relative">
                    <!-- Checkbox Toggle Switch -->
                    <label class="flex items-center cursor-pointer relative">
                        <input type="checkbox" class="sr-only" v-model="post.is_active" @change="updatePostStatus(post.id, post.is_active)" />
                        <div class="w-10 h-5 bg-gray-200 
                            rounded-full shadow-inner transition-colors duration-300 
                            ease-in-out" :class="{ 'bg-green-500': post.is_active }">
                        </div>

                        <div class="dot absolute w-5 h-5 
                            bg-white rounded-full shadow transition-transform duration-300 ease-in-out transform" 
                            :class="{ 'translate-x-5': post.is_active }">
                        </div>
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

<script>
import axios from 'axios';

export default {
    data() {
        return {
            posts: [],
            categories: [],
            locations: [],
            loading: true,
            isModalOpen: false,
            selectedImage: null,
            searchQuery: '',
            selectedCategory: "",
            selectedLocation: "",
            selectedCondition: "",
            selectedStatus: null,
            priceRange: {
                min: null,
                max: null,
            },
            previews: [],   
        };
    },
    mounted() {
        this.fetchPosts();
        this.fetchCategories();
        this.fetchLocations();
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
        openImageModal(imagePath) {
            this.selectedImage = imagePath;
            this.isModalOpen = true;
        },
        filterPosts() {
            this.loading = true;

            const params = {
                query: this.searchQuery,
                location: this.selectedLocation || null,
                category: this.selectedCategory || null,
                minPrice: this.priceRange.min || null,
                maxPrice: this.priceRange.max || null,
                condition: this.selectedCondition || null,
                status: this.selectedStatus || null,
            };

            axios
                .get("/posts/search/filter/admin", { params })
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

        async updatePostStatus(postId, isActive) {
            try {
                const response = await axios.patch(`/posts/${postId}`, {
                    is_active: isActive ? 1 : 0 
                });

            } catch (error) {
                console.error("Error updating post status:", error);
            }
        },

    },
};
</script>

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
