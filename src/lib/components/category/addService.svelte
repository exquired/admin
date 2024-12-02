<script lang="ts">
  import { createEventDispatcher } from 'svelte';
  import { onMount } from 'svelte';

  const dispatch = createEventDispatcher();

  let serviceName = '';
  let selectedCategoryId: number | string = '';  
  let categories: { id: number, name: string }[] = [];
  let errorMessage = '';
  let successMessage = '';
  let showPopup = false;  // To toggle the success popup visibility

  // Fetch available categories
  onMount(async () => {
    try {
      const categoryResponse = await fetch('http://localhost/my-php-backend/admin/getCategory.php', { method: 'GET' });
      if (!categoryResponse.ok) throw new Error('Failed to fetch categories');
      
      const categoryData = await categoryResponse.json();

      console.log("Category Data:", categoryData);


      if (categoryData.status === 'success' && Array.isArray(categoryData.data)) {
        categories = categoryData.data;
      } else {
        throw new Error('Invalid categories data');
      }

      if (categories.length === 0) {
        errorMessage = 'No categories available.';
      }
    } catch (error) {
      console.error('Error:', error);
      errorMessage = 'Failed to load categories.';
    }
  });

  // Validate inputs
  function validateInputs() {
    if (!serviceName.trim() || !selectedCategoryId) {
      return 'All fields are required.';
    }
    return '';
  }

  async function handleSave() {
    errorMessage = '';
    successMessage = '';
    showPopup = false; // Hide popup initially

    const validationError = validateInputs();
    if (validationError) {
      errorMessage = validationError;
      return;
    }

    try {
      const response = await fetch('http://localhost/my-php-backend/admin/addservice.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          serviceName,
          selectedCategoryId: Number(selectedCategoryId),  // Ensure it's a number
        }),
      });

      if (!response.ok) {
        throw new Error(`Failed to save service. Status: ${response.status}`);
      }

      const result = await response.json();
      if (result.status === 'success') {
        successMessage = result.message;
        showPopup = true; // Show the success popup
        // Optional: Clear input fields after success
        serviceName = '';
        selectedCategoryId = '';
        dispatch('save', { serviceName, selectedCategoryId });
      } else {
        errorMessage = result.message;
      }
    } catch (error) {
      console.error('Error:', error);
      errorMessage = 'An error occurred while saving the service.';
    }
  }

  function handleCancel() {
    dispatch('cancel');
  }
</script>

<!-- Popup Overlay for Success Message -->
{#if showPopup}
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
      <h2 class="text-lg font-semibold mb-4 text-center text-green-600">Success!</h2>
      <p class="text-sm text-center text-green-600">{successMessage}</p>
      <div class="flex justify-center gap-3 mt-4">
        <button
          on:click={() => showPopup = false}
          class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
          aria-label="Close"
        >
          Close
        </button>
      </div>
    </div>
  </div>
{/if}

<!-- Add Service Form -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-gray-200 rounded-lg w-full max-w-md p-6">
    <h2 class="text-lg font-semibold mb-4">Add Service</h2>

    <div class="space-y-4">
      <div>
        <label class="block text-sm mb-1" for="serviceName">Service name:</label>
        <input
          id="serviceName"
          type="text"
          bind:value={serviceName}
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
          aria-label="Service name"
        />
      </div>

      <div>
        <label class="block text-sm mb-1" for="category">Category:</label>
        <select
          id="category"
          bind:value={selectedCategoryId}
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
          aria-label="Service category"
        >
          <option value="">Select a category</option>
          {#each categories as { id, name }}
            <option value={id}>{name}</option>
          {/each}
        </select>
      </div>

      {#if errorMessage}
        <p class="text-red-600 text-sm">{errorMessage}</p>
      {/if}
      {#if successMessage}
        <p class="text-green-600 text-sm">{successMessage}</p>
      {/if}
    </div>

    <div class="flex justify-end gap-3 mt-6">
      <button
        on:click={handleCancel}
        class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
        aria-label="Cancel"
      >
        Cancel
      </button>
      <button
        on:click={handleSave}
        class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
        aria-label="Save service"
      >
        Save
      </button>
    </div>
  </div>
</div>
