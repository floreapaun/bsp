<template>
    <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-6">Create a Post</h2>
      
      <form @submit.prevent="submitPost">
        <!-- Title Input -->
        <div class="mb-4">
          <label for="title" class="block text-gray-700">Title</label>
          <input
            v-model="title"
            type="text"
            id="title"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
            placeholder="Enter post title"
          />
        </div>

        <div class="mb-4">
          <label for="phone-number" class="block text-gray-700">Phone number</label>
          <input
            v-model="phoneNumber"
            type="text"
            id="phone-number"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
            placeholder="Enter contact phone number"
          />
        </div>

        <div class="mb-4">
          <label for="price" class="block text-gray-700">Price</label>
          <input
            v-model="price"
            type="text"
            id="phone-number"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
            placeholder="Euro"
          />
        </div>
  
        <!-- Body Input -->
        <div class="mb-4">
          <label for="body" class="block text-gray-700">Body</label>
          <textarea
            v-model="body"
            id="body"
            rows="5"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
            placeholder="Write your post..."
          ></textarea>
        </div>

        <div class="mb-4">
          <input
            type="file"
            multiple
            @change="handleFiles"
            accept="image/*"
          />
        </div>

        <div class="mb-4">
          <div v-if="previews.length">
            <h3>Image Previews:</h3>
            <div class="inline-block p-2" v-for="(image, index) in previews" :key="index">
              <img :src="image" alt="Preview" width="100" />
            </div>
          </div>
        </div>
  
        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition"
        >
          Submit Post
        </button>
  
        <!-- Success Message -->
        <p v-if="successMessage" class="mt-4 text-green-600">{{ successMessage }}</p>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        title: '',
        body: '',
        phoneNumber: '',
        price: '',
        images: [], // Holds the image files
        previews: [], // Holds the preview URLs for display
        successMessage: ''
      };
    },
    methods: {
      async submitPost() {
        try {
                
          const formData = new FormData();
          this.images.forEach((image, index) => {
            formData.append(`images[${index}]`, image);
          });
          formData.append("title", this.title);
          formData.append("body", this.body);
          formData.append("phoneNumber", this.phoneNumber);
          formData.append("price", this.price);

          //console.log(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
          const response = await axios.post('http://localhost:8000/posts', formData);
          this.successMessage = 'Post created successfully!';
          this.title = '';
          this.body = '';
        } catch (error) {
          console.error('There was an error creating the post:', error);
        }
      },
      handleFiles(event) {
        // Clear existing images and previews
        this.images = [];
        this.previews = [];

        // Loop through each file and add to images array
        Array.from(event.target.files).forEach((file) => {
          this.images.push(file);

          // Generate a preview URL for the image
          const reader = new FileReader();
          reader.onload = (e) => this.previews.push(e.target.result);
          reader.readAsDataURL(file);
      });
    }
    }
  };
  </script>
  
  <style scoped>
  /* You can add scoped styles here if necessary */
  </style>
  