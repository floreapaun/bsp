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

                <div class="m-2 flex flex-col items-center justify-center">
                    <!-- Location Text with Hover Effect -->
                    <span class="text-xl font-semibold text-indigo-600 bg-indigo-100 rounded-lg px-4 py-2 shadow-lg hover:bg-indigo-200 transition duration-300 ease-in-out transform hover:scale-105">
                        Category: {{ post.category.name }}
                    </span>
                </div>
                
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

                <div 
                    id="location-display" 
                    class="flex items-center space-x-4 bg-gradient-to-r from-blue-50 to-blue-200 text-blue-900 font-semibold text-sm sm:text-base px-4 py-3 rounded-lg shadow-lg"
                >
                    <!-- Beautiful Location Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path 
                        d="M10 20a1 1 0 01-.832-.445C7.27 16.843 4 12.713 4 9a6 6 0 1112 0c0 3.713-3.27 7.843-5.168 10.555A1 1 0 0110 20zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        />
                    </svg>

                    <!-- Location Text -->
                    <span>{{ post.location.name}}</span>
                </div>

                <div class="mt-4 flex items-center justify-center space-x-3 group relative">
                    <!-- Checkbox Toggle Switch -->
                    <label class="flex items-center cursor-pointer relative">
                        <input type="checkbox" class="sr-only" v-model="post.is_active" disabled/>
                        <div class="w-10 h-5 bg-gray-200 rounded-full shadow-inner transition-colors duration-300 ease-in-out" :class="{ 'bg-green-500': post.is_active }"></div>
                        <div class="dot absolute w-5 h-5 bg-white rounded-full shadow transition-transform duration-300 ease-in-out transform" :class="{ 'translate-x-5': post.is_active }"></div>
                    </label>

                    <!-- Text Display for Active/Inactive -->
                    <span class="text-lg font-semibold" :class="{ 'text-green-600': post.is_active, 'text-gray-500': !post.is_active }">
                        {{ post.is_active ? 'Active' : 'Inactive' }}
                    </span>

                        <!-- Tooltip Text on Hover -->
                    <span v-if="post.is_active" class="absolute bottom-full mb-2 p-1 text-xs text-white bg-black rounded opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        Post has been activated by administrator!
                    </span>
                    <span v-else class="absolute bottom-full mb-2 p-1 text-xs text-white bg-black rounded opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                        Post has not been activated by administrator yet!
                    </span>
                </div>

                <div class="flex items-center justify-center mt-4 p-3">
                    <button id="price-button" class="bg-purple-100 text-pink-700 font-bold text-sm sm:text-base inline-block px-3 py-2 rounded-lg shadow-md">
                        Price: {{ post.price }} &euro;
                    </button>
                </div>

                <div class="mt-4 flex flex-col items-center justify-center">
                    <button
                        @click="editPost(post.id)"
                        class="bg-blue-500 text-white text-sm sm:text-base px-3 sm:px-4 py-2 rounded-md shadow hover:bg-blue-600"
                    >
                        Edit
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

        <!-- Edit Post Modal -->
        <div v-if="isEditModalOpen" class="modal-overlay fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50 p-4">
            <div class="modal-content bg-white p-4 sm:p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-xl sm:text-2xl font-semibold mb-4">Edit Post</h2>
                
                <!-- Post Title -->
                <label class="block mb-2 text-sm font-medium text-gray-700">Title</label>
                <input type="text" v-model="editedPost.title" class="w-full p-2 mb-4 border rounded-lg" />
                <span v-if="errors.title" class="text-red-600">{{ errors.title }}</span>

                <!-- Post Body -->
                <label class="block mb-2 text-sm font-medium text-gray-700">Body</label>
                <textarea v-model="editedPost.body" class="w-full p-2 mb-4 border rounded-lg" rows="4"></textarea>
                <span v-if="errors.body" class="text-red-600">{{ errors.body }}</span>

                <!-- Post Phone Number -->
                <label class="block mb-2 text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" v-model="editedPost.phone_number" class="w-full p-2 mb-4 border rounded-lg" />
                <span v-if="errors.phoneNumber" class="text-red-600">{{ errors.phoneNumber }}</span>

                <!-- Post Price -->
                <label class="block mb-2 text-sm font-medium text-gray-700">Price (&euro;)</label>
                <input type="number" v-model="editedPost.price" class="w-full p-2 mb-4 border rounded-lg" />
                <span v-if="errors.price" class="text-red-600">{{ errors.price }}</span>

                <div :class="{ 'mb-60': isFocused, 'mb-4': !isFocused }">
                    <label for="category" class="block text-gray-700">Category</label>
                    <Select
                        v-model="editedPost.category_id"
                        id="category"
                        :options="categories"
                        optionLabel="name"
                        optionValue="id"
                        :virtualScrollerOptions="{ itemSize: 38 }"
                        placeholder="What's your city?"
                        class="w-full bg-blue-50 border border-blue-300 text-blue-700 rounded-lg 
                        shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500 z-100"
                        @focus="isFocused = true"
                        @blur="isFocused = false"
                    />
                    <span v-if="errors.category_id" class="text-red-600">{{ errors.category_id }}</span>
                </div>

                <div :class="{ 'mb-60': isFocused, 'mb-4': !isFocused }">
                    <label for="location" class="block text-gray-700">Location</label>
                    <Select
                        v-model="editedPost.location_id"
                        id="location"
                        :options="locations"
                        optionLabel="name"
                        editable
                        optionValue="id"
                        :virtualScrollerOptions="{ itemSize: 38 }"
                        placeholder="What's your city?"
                        class="w-full bg-blue-50 border border-blue-300 text-blue-700 rounded-lg 
                        shadow-sm focus:ring focus:ring-blue-300 focus:border-blue-500 z-100"
                        @focus="isFocused = true"
                        @blur="isFocused = false"
                    />
                    <span v-if="errors.location_id" class="text-red-600">{{ errors.location_id }}</span>
                </div>

                <!-- Existing Images -->
                <div v-if="editedPost.images.length > 0" class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Existing Images</h3>
                    <div class="flex gap-2 overflow-x-auto">
                        <div v-for="(image, index) in editedPost.images" :key="image.id" class="relative">
                            <img :src="image.file_path" alt="Post Image" class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-md shadow" />
                            <button
                                @click="removeImage(index, image.id)"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center"
                            >
                                &times;
                            </button>
                        </div>
                    </div>
                </div>

                <!-- New Image Upload -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Add new images</h3>
                    <input type="file" multiple @change="handleImageUpload" class="block w-full text-sm text-gray-900 border rounded-lg cursor-pointer" />
                </div>

                <div class="mb-4">
                    <div v-if="previews && Array.isArray(previews) && previews.length">
                        <h3>Image Previews:</h3>
                        <div class="inline-block p-2" v-for="(image, index) in previews" :key="index">
                            <img :src="image" alt="Preview" width="80" class="rounded-md" />
                        </div>
                    </div>
                </div>
                <span v-if="errors.images" class="text-red-600">{{ errors.images }}</span>

                <!-- Save and Cancel Buttons -->
                <div class="flex justify-end">
                    <button @click="deletePost" class="bg-red-500 text-white px-4 py-2 rounded-md shadow hover:bg-red-600 mr-2">
                        Delete
                    </button>
                    <button @click="isEditModalOpen = false" class="bg-gray-500 text-white px-4 py-2 rounded-md shadow hover:bg-gray-600 mr-2">
                        Cancel
                    </button>
                    <button @click="savePost" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600">
                        Save
                    </button>
                </div>
            </div>
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
import Select from "primevue/select";

export default {
    components: {
        Select,
    },
    
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
                price: 0,
                phone_number: '',
                location_id: '',
                category_id: '',
                images: [],         
                newImages: [],
            },
            locations: [],
            categories: [],

            // Holds the preview URLs for display
            previews: [],   
            
            // Holds validation errors
            errors: {}, 
            isFocused: false,
        };
    },
    beforeMount() {
        this.fetchLocations();
        this.fetchCategories();
    },
    mounted() {
        this.fetchPosts();
    },
    methods: {
        validateForm() {
            const errors = {};
            console.log(this.editedPost.price);

            if (!this.editedPost.title.trim()) errors.title = "Title is required.";
            if (!this.editedPost.body.trim()) errors.body = "Body is required.";
            if (!String(this.editedPost.phone_number).trim()) errors.phoneNumber = "Phone number is required.";
            else if (!/^\d+$/.test(this.editedPost.phone_number)) errors.phoneNumber = "Phone number must be numeric.";
            if (!String(this.editedPost.price).trim()) errors.price = "Price is required.";
            else if (isNaN(this.editedPost.price)) errors.price = "Price must be a number.";
            if (!this.editedPost.location_id) errors.location_id = "Location is required.";

            this.errors = errors;
            return Object.keys(errors).length === 0;
        },
        async fetchPosts() {
            try {
                const response = await axios.get('/posts/user');
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
                this.posts = []; // Clear results if input is empty
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
        }, 300), // Adjust debounce time as needed
        editPost(postId) {
             const post = this.posts.find((p) => p.id === postId);
             console.log(post);
            if (post) {
                this.editedPost = { ...post, newImages: [] }; // Add newImages array for uploading new images
                this.isEditModalOpen = true;
             }
        },
        handleImageUpload(event) {
            // Add selected files to `newImages`
            Array.from(event.target.files).forEach(file => { 
                
                this.editedPost.newImages.push(file);

                // Generate a preview URL for the image
                const reader = new FileReader();
                if (this.previews && Array.isArray(this.previews)) {
                    reader.onload = (e) => this.previews.push(e.target.result);
                    reader.readAsDataURL(file);
                }
            });
        },
        async deleteImage(imageId) {
            try {
                await axios.delete(`/images/${imageId}`);
                this.editedPost.images = this.editedPost.images.filter(image => image.id !== imageId); // Remove the image from the local array
                console.log('Image deleted successfully');
            } catch (error) {
                console.error('Error deleting image:', error);
            }
        },
        removeImage(index, imageId) {
            // Remove image from the existing images array
            this.editedPost.images.splice(index, 1);
            console.log(imageId);
            this.deleteImage(imageId);
        },
        async savePost() {
            if (!this.validateForm()) return;
            const formData = new FormData();
            formData.append('title', this.editedPost.title);
            formData.append('body', this.editedPost.body);
            formData.append('price', this.editedPost.price);
            formData.append('phone_number', this.editedPost.phone_number);
            formData.append('location_id', this.editedPost.location_id);
            formData.append('category_id', this.editedPost.category_id);

            // Append new images to the FormData object
            this.editedPost.newImages.forEach((file, index) => {
                formData.append(`newImages[${index}]`, file);
            });

            try {
                const response = await axios.post(`/posts/${this.editedPost.id}`, formData);

                const updatedPost = response.data;

                // Update the post in the `posts` array
                const index = this.posts.findIndex((p) => p.id === updatedPost.id);
                if (index !== -1) {
                    this.posts.splice(index, 1, updatedPost);
                }

                this.isEditModalOpen = false; // Close the modal
                this.fetchPosts();
            } catch (error) {
                console.error("Error saving post:", error);
            }
        },
        async deletePost() {
            try {
                await axios.delete(`/posts/${this.editedPost.id}`);
                this.posts = this.posts.filter(post => post.id !== this.editedPost.id); // Remove post from the local array
                this.isEditModalOpen = false; // Close the modal
                alert('Post deleted successfully');
            } catch (error) {
                console.error('Error deleting post:', error);
                alert('Error deleting post');
            }
        },
        fetchLocations() {
            axios.get("/locations").then((response) => {
                 this.locations = response.data;
            });
        },
        fetchCategories() {
            axios.get("/categories").then((response) => {
                 this.categories = response.data;
            });
        }
    },
};
</script>