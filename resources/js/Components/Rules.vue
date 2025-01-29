<template>
    <div class="flex justify-center items-center p-2">
      <!-- Button to open modal -->
      <button @click="openModal" :class="`${bgColor} text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700`">
        Read Rules
      </button>
  
      <!-- Modal -->
      <div v-if="isModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white w-11/12 md:w-2/3 lg:w-1/2 rounded-lg shadow-lg p-6 relative">
          <!-- Close Button -->
          <button @click="closeModal" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-xl">
            &times;
          </button>
  
          <!-- Modal Title -->
          <h2 class="text-2xl font-semibold text-gray-800 mb-4">Posting Rules</h2>
  
          <!-- Scrollable Content -->
          <div class="max-h-80 overflow-y-auto p-2 border border-gray-200 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700">General Posting Rules</h3>
            <ul class="list-disc list-inside text-gray-600 mb-4">
              <li><strong>Accurate Descriptions</strong> – Provide clear and honest details about the item.</li>
              <li><strong>No Misleading Information</strong> – Do not post false or deceptive claims.</li>
              <li><strong>Real Photos Only</strong> – No stock images or heavily edited pictures.</li>
              <li><strong>Fair Pricing</strong> – Set reasonable prices.</li>
              <li><strong>One Item Per Listing</strong> – No bundling unrelated items.</li>
              <li><strong>No Duplicate Listings</strong> – Avoid reposting the same item.</li>
              <li><strong>Correct Category Selection</strong> – Post in the right category.</li>
              <li><strong>Location Disclosure</strong> – Mention the item's location.</li>
              <li><strong>Contact Information</strong> – Use the platform’s messaging system.</li>
            </ul>
  
            <h3 class="text-lg font-semibold text-gray-700">Prohibited Items</h3>
            <ul class="list-disc list-inside text-gray-600 mb-4">
              <li><strong>Illegal Goods</strong> – No stolen, counterfeit, or prohibited items.</li>
              <li><strong>Restricted Items</strong> – No alcohol, tobacco, or hazardous materials.</li>
              <li><strong>Adult Content</strong> – No explicit or inappropriate materials.</li>
              <li><strong>Services Instead of Items</strong> – Only physical goods are allowed.</li>
            </ul>
  
            <h3 class="text-lg font-semibold text-gray-700">Behavioral Guidelines</h3>
            <ul class="list-disc list-inside text-gray-600">
              <li><strong>No Scams or Fraud</strong> – All listings must be legitimate.</li>
              <li><strong>Respectful Communication</strong> – No harassment or abusive language.</li>
              <li><strong>No External Promotions</strong> – No ads linking to other sites.</li>
              <li><strong>Timely Updates</strong> – Mark items as sold or remove unavailable listings.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
        bgColor: {
            type: String,
            default: "bg-red-600",
        },
    },
    data() {
      return {
        isModalOpen: false
      };
    },
    methods: {
      openModal() {
        this.isModalOpen = true;
      },
      closeModal() {
        this.isModalOpen = false;
      }
    }
  };
  </script>
  
  <style>
  /* Optional: Add smooth scrolling */
  ::-webkit-scrollbar {
    width: 6px;
  }
  
  ::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 10px;
  }
  </style>
  