<script lang="ts">
  import Sidebar from "$lib/components/Sidebar.svelte";
  import AddCategory from "$lib/components/category/addCategory.svelte";

  // Type Definitions
  type SaveEventDetail = {
    categoryName: string;
    CategoryID: number; // Assuming the backend returns the new ID
    num_services: number;
    num_professionals: number;
  };

  type Category = {
    CategoryID: number;
    name: string;
    num_services: number;
    num_professionals: number;
  };

  // Variables with explicit types
  let showAddPopup: boolean = false;
  let searchQuery: string = '';
  let loading: boolean = false; // Loading state
  let errorMessage: string | null = null; // Error message state

  // Pagination variables
  const itemsPerPage: number = 5;
  let currentPage: number = 1;
  let categories: Category[] = [];

  // Fetch categories data from PHP backend
  import { onMount } from "svelte";

  onMount(async () => {
    await fetchCategories();
  });

  async function fetchCategories() {
    loading = true; // Start loading
    errorMessage = null; // Clear previous error
    try {
      const response = await fetch('http://localhost/my-php-backend/admin/Category.php');
      if (response.ok) {
        const data = await response.json();
        console.log("Categories data:", data); // Log the data for debugging
        categories = data;
      } else {
        throw new Error("Failed to load categories");
      }
    } catch (error) {
      console.error("Error fetching categories:", error);
      errorMessage = "Failed to load categories. Please try again later.";
    } finally {
      loading = false; // End loading
    }
  }

  // Add category logic
  function handleCategorySave(event: CustomEvent<SaveEventDetail>) {
    const { categoryName, CategoryID, num_services, num_professionals } = event.detail;
    console.log("Category saved:", categoryName);

    // Create a new category object and add it to the categories array
    const newCategory: Category = {
      CategoryID,
      name: categoryName,
      num_services,
      num_professionals
    };

    categories = [...categories, newCategory]; // Update the categories array
    showAddPopup = false; // Close the popup
  }

  // Reactive pagination logic
  $: totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
  $: paginatedCategories = filteredCategories.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  // Reactive filtering logic
  $: filteredCategories = categories.filter(category => {
    return category.name.toLowerCase().includes(searchQuery.toLowerCase());
  });

  // Change page
  function changePage(page: number) {
    if (page < 1 || page > totalPages) return; // Prevent going out of bounds
    currentPage = page;
  }

  // Cancel logic
  function handleCancel() {
    showAddPopup = false;
  }
</script>

<div class="flex min-h-screen bg-gray-100">
  <!-- Sidebar -->
  <Sidebar />

  <!-- Main Content -->
  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-medium">Welcome back, Van</h1>
    </div>

    <hr class="p-2">

    <div class="bg-white rounded-xl p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">CATEGORIES</h2>
      </div>

      <!-- Action Buttons and Search Input -->
      <div class="flex items-center space-x-4 mb-6">
        <!-- Add Category Button -->
        <button
        on:click={() => showAddPopup = true}
        aria-label="Add Category"
        class="flex items-center gap-2 px-4 py-3 text-sm text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition duration-200"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Category
      </button>
        {#if showAddPopup}
 <AddCategory on:save={handleCategorySave} on:cancel={handleCancel} />
        {/if}

        <!-- Search Input -->
        <div class="flex-1">
          <input
            type="text"
            placeholder="Search here..."
            bind:value={searchQuery}
            aria-label="Search Categories"
            class="w-full border rounded-lg px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
        </div>
      </div>

      <!-- Error Message -->
      {#if errorMessage}
        <div class="mb-4 text-red-600">{errorMessage}</div>
      {/if}

      <!-- Categories Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">CATEGORY NAME</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 text-center">NO. OF SERVICES</th>
              <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 text-center">NO. OF PROFESSIONALS</th>
            </tr>
          </thead>
          <tbody>
            {#each paginatedCategories as category}
              <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">{category.name}</td>
                <td class="py-3 px-4 text-center">{category.num_services}</td>
                <td class="py-3 px-4 text-center">{category.num_professionals}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <div class="flex justify-between items-center mt-4">
        <button on:click={() => changePage(currentPage - 1)} disabled={currentPage === 1} class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Previous</button>
        <span>Page {currentPage} of {totalPages}</span>
        <button on:click={() => changePage(currentPage + 1)} disabled={currentPage === totalPages} class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Next</button>
      </div>
    </div>
  </main>
</div>