<script lang="ts">
  import { createEventDispatcher } from 'svelte';

  const dispatch = createEventDispatcher();

  let categoryName = '';

  async function handleSave() {
    if (!categoryName.trim()) {
      alert('Category name cannot be empty.');
      return;
    }

    try {
      const response = await fetch('http://localhost/my-php-backend/admin/addcategory.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ categoryName })
      });

      if (!response.ok) {
        throw new Error(`Failed to save category. Status: ${response.status}`);
      }

      const result = await response.json();
      if (result.status === 'success') {
        alert(result.message);
        dispatch('save', { categoryName });
      } else {
        alert(result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while saving the category.');
    }
  }

  function handleCancel() {
    dispatch('cancel');
  }
</script>

<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-gray-200 rounded-lg w-full max-w-md p-6">
    <h2 class="text-lg font-semibold mb-4">Add Category</h2>

    <div class="space-y-4">
      <div>
        <label class="block text-sm mb-1" for="categoryName">Category name:</label>
        <input
          id="categoryName"
          type="text"
          bind:value={categoryName}
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
          aria-label="Category name"
        />
      </div>
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
        aria-label="Save category"
      >
        Save
      </button>
    </div>
  </div>
</div>