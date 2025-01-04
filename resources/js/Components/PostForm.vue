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
        <span v-if="errors.title" class="text-red-600">{{ errors.title }}</span>
      </div>

      <!-- Phone Number Input -->
      <div class="mb-4">
        <label for="phone-number" class="block text-gray-700">Phone number</label>
        <input
          v-model="phoneNumber"
          type="text"
          id="phone-number"
          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
          placeholder="Enter contact phone number"
        />
        <span v-if="errors.phoneNumber" class="text-red-600">{{ errors.phoneNumber }}</span>
      </div>

      <!-- Price Input -->
      <div class="mb-4">
        <label for="price" class="block text-gray-700">Price</label>
        <input
          v-model="price"
          type="text"
          id="price"
          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
          placeholder="Euro"
        />
        <span v-if="errors.price" class="text-red-600">{{ errors.price }}</span>
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
        <span v-if="errors.body" class="text-red-600">{{ errors.body }}</span>
      </div>

      <!-- Category Input -->
      <div :class="{ 'mt-60': isFocusedCategorySelect, 'mb-4': !isFocusedCategorySelect }">
        <Select
          v-model="category_id"
          id="category"
          :options="categories"
          optionLabel="name"
          optionValue="id"
          :virtualScrollerOptions="{ itemSize: 38 }"
          placeholder="What's the post category?"
          class="w-full p-2 border rounded focus:mt-60 focus:outline-none focus:ring-2 focus:ring-blue-500"
          @focus="isFocusedCategorySelect = true"
          @blur="isFocusedCategorySelect = false"
        />
        <span v-if="errors.category_id" class="text-red-600">{{ errors.category_id }}</span>
      </div>

      <!-- Location Input -->
      <div :class="{ 'mt-60': isFocusedLocationSelect, 'mb-4': !isFocusedLocationSelect }">
        <Select
          v-model="location_id"
          id="location"
          :options="locations"
          optionLabel="name"
          optionValue="id"
          :virtualScrollerOptions="{ itemSize: 38 }"
          placeholder="What's your city?"
          class="w-full p-2 border rounded focus:mt-60 focus:outline-none focus:ring-2 focus:ring-blue-500"
          @focus="isFocusedLocationSelect = true"
          @blur="isFocusedLocationSelect = false"
        />
        <span v-if="errors.location_id" class="text-red-600">{{ errors.location_id }}</span>
      </div>

      <!-- File Upload -->
      <div class="mb-4">
        <input type="file" multiple @change="handleFiles" accept="image/*" />
        <span v-if="errors.images" class="text-red-600">{{ errors.images }}</span>
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
      <div class="m-4 text-center">
        <span v-if="successMessage" class="text-green-600">{{ successMessage }}</span>
        <span v-if="errorMessage" class="text-red-600">{{ errorMessage }}</span>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import Select from "primevue/select";

export default {
  components: {
    Select,
  },
  data() {
    return {
      title: "",
      body: "",
      phoneNumber: "",
      price: "",
      location_id: "",
      category_id: "",
      images: [],
      previews: [],
      successMessage: "",
      errorMessage: "",
      locations: [],
      categories: [],
      
      // Holds validation errors
      errors: {}, 
      isFocusedCategorySelect: false,
      isFocusedLocationSelect: false,
    };
  },
  beforeMount() {
    this.fetchLocations();
    this.fetchCategories();
  },
  methods: {
    validateForm() {
      const errors = {};

      if (!this.title.trim()) errors.title = "Title is required.";
      if (!this.body.trim()) errors.body = "Body is required.";
      if (!this.phoneNumber.trim()) errors.phoneNumber = "Phone number is required.";
      else if (!/^\d+$/.test(this.phoneNumber)) errors.phoneNumber = "Phone number must be numeric.";
      if (!this.price.trim()) errors.price = "Price is required.";
      else if (isNaN(this.price)) errors.price = "Price must be a number.";
      if (!this.location_id) errors.location_id = "Location is required.";
      if (!this.category_id) errors.category_id = "Category is required.";
      if (this.images.length === 0) errors.images = "At least one image is required.";

      this.errors = errors;
      return Object.keys(errors).length === 0;
    },
    async submitPost() {
      if (!this.validateForm()) return;

      try {
        const formData = new FormData();
        this.images.forEach((image, index) => {
          formData.append(`images[${index}]`, image);
        });
        formData.append("title", this.title);
        formData.append("body", this.body);
        formData.append("phoneNumber", this.phoneNumber);
        formData.append("price", this.price);
        formData.append("location_id", this.location_id);
        formData.append("category_id", this.category_id);

        const response = await axios.post("http://localhost:8000/posts", formData);
        this.successMessage = "Post created successfully!";
        this.resetForm();
      } catch (err) {
        this.errorMessage = err.response
          ? `Error: ${err.response.data.message}`
          : "An error occurred.";
      }
    },
    handleFiles(event) {
      this.images = [];
      this.previews = [];
      Array.from(event.target.files).forEach((file) => {
        this.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => this.previews.push(e.target.result);
        reader.readAsDataURL(file);
      });
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
    },
    resetForm() {
      this.title = "";
      this.body = "";
      this.phoneNumber = "";
      this.price = "";
      this.location_id = "";
      this.category_id = "";
      this.images = [];
      this.previews = [];
      this.errors = {};
    },
  },
};
</script>

<style scoped>
  .search-input {
    width: 100%;
    padding: 8px;
    margin-bottom: 8px;
    box-sizing: border-box;
  }
  .virtual-list {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #ccc;
  }
  .list-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
  }
  .list-item:hover {
    background-color: #f0f0f0;
  }
</style>
  