<script lang="ts">
  import Sidebar from "$lib/components/Sidebar.svelte";
  import AddServices from "$lib/components/category/addService.svelte";
  import DeleteServices from "$lib/components/category/deleteServices.svelte";
  import { onMount } from "svelte";
  import { writable } from "svelte/store";

  // Type Definitions
  type Service = {
    services_id: number;
    service_name: string;
    num_providers: number;
    category_name: string;
  };

  // Variables with explicit types
  let services: Service[] = [];
  let totalServices: number = 0;
  let currentPage: number = 1;
  let limit: number = 10;
  let totalPages: number = 1;
  let searchQuery: string = "";
  let showAddPopup: boolean = false;
  let showDeletePopup: boolean = false;

  const errorMessage = writable(""); // Store error message
  let debounceTimeout: any; // Store for the timeout function

  // Fetch services data with pagination and search
  async function fetchServices(page: number = 1) {
    const offset = (page - 1) * limit;
    try {
      const response = await fetch(
        `http://localhost/my-php-backend/admin/Services.php?limit=${limit}&offset=${offset}&search=${encodeURIComponent(
          searchQuery
        )}`
      );
      if (response.ok) {
        const data: { total: number; services: Service[] } = await response.json();
        services = data.services;
        totalServices = data.total;
        totalPages = Math.ceil(totalServices / limit);
      } else {
        throw new Error("Failed to load services: " + response.statusText);
      }
    } catch (error) {
      const message = (error as Error).message || "An unknown error occurred.";
      errorMessage.set("Error fetching services: " + message);
    }
  }

  // Fetch services on component mount
  onMount(() => {
    fetchServices(currentPage);
  });

  // Pagination controls
  function nextPage() {
    if (currentPage < totalPages) {
      currentPage++;
      fetchServices(currentPage);
    }
  }

  function prevPage() {
    if (currentPage > 1) {
      currentPage--;
      fetchServices(currentPage);
    }
  }

  // Handle search input with debounce
  function handleSearch() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      currentPage = 1; // Reset to the first page
      fetchServices(currentPage); // Fetch services with the updated search query
    }, 300);
  }

  // Add service logic
  function handleServiceSave(event: CustomEvent<{ SName: string; searchKeys: string[] }>) {
    const { SName } = event.detail;
    console.log("Service saved:", SName);
    showAddPopup = false;
    fetchServices(currentPage); // Refresh services after adding
  }

  // Delete service logic
  function handleServicesDelete() {
    console.log("Service deleted");
    showDeletePopup = false;
    fetchServices(currentPage); // Refresh services after deleting
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
        <h2 class="text-xl font-bold">SERVICES</h2>
      </div>
    
      <!-- Actions and Search Bar -->
      <div class="flex items-center space-x-4 mb-4">
        <!-- Add Service Button -->
        <button
          on:click={() => (showAddPopup = true)}
          aria-label="Add Service"
          class="flex items-center gap-2 min-w-[150px] px-4 py-2 text-base text-white bg-emer ald-600 border border-emerald-200 rounded-lg bg-emerald-700 transition duration-200"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Service
        </button>
        {#if showAddPopup}
          <AddServices on:save={handleServiceSave} on:cancel={() => (showAddPopup = false)} /> 
        {/if}
    
        <!-- Delete Service Button -->
        <button on:click={() => (showDeletePopup = true)}
          aria-label="Delete Service"
          class="flex items-center gap-2 min-w-[150px] px-4 py-2 text-base text-white bg-red-600 border border-red-200 rounded-lg hover:bg-red-700 transition duration-200"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Delete Service
        </button>
        {#if showDeletePopup}
          <DeleteServices on:save={handleServicesDelete} on:cancel={() => (showDeletePopup = false)} />
        {/if}
    
        <!-- Search Bar -->
        <input
          type="text"
          placeholder="Search services..."
          bind:value={searchQuery}
          on:input={handleSearch}
          class="flex-1 min-w-[200px] border rounded-lg px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500"
          aria-label="Search services"
        />
      </div>
    
      <!-- Error Message -->
      {#if $errorMessage}
        <div class="text-red-600 mb-4">
          <p>{$errorMessage}</p>
        </div>
      {/if}
    
      <!-- Services Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="py-3 px-4 text-sm text-left">Service Name</th>
              <th class="py-3 px-4 text-sm text-center">Category Name</th>
              <th class="py-3 px-4 text-sm text-center">Number of Service Providers</th>
            </tr>
          </thead>
          <tbody>
            {#each services as service}
              <tr class="border-b">
                <td class="py-3 px-4 text-sm text-left">{service.service_name}</td>
                <td class="py-3 px-4 text-sm text-center">{service.category_name}</td>
                <td class="py-3 px-4 text-sm text-center">{service.num_providers}</td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    
      <!-- Pagination Section -->
      <div class="flex justify-center items-center mt-4 space-x-4">
        <button
          on:click={prevPage}
          disabled={currentPage === 1}
          class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 disabled:bg-gray-300 disabled:text-gray-500 
                 hover:bg-green-800 hover:text-white active:bg-green-800 disabled:hover:bg-gray-300"
          aria-label="Previous page"
        >
          Previous
        </button>
        <span class="text-sm font-medium text-gray-600">
          Page {currentPage} of {totalPages}
        </span>
        <button
          on:click={nextPage}
          disabled={currentPage === totalPages}
          class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 disabled:bg-gray-300 disabled:text-gray-500 
                 hover:bg-green-800 hover:text-white active:bg-green-800 disabled:hover:bg-gray-300"
          aria-label="Next page"
        >
          Next
        </button>
      </div>
    </div>
  </main>
</div>