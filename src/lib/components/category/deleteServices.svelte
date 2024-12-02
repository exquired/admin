<script lang="ts">
  import { createEventDispatcher } from 'svelte';
  import { writable } from 'svelte/store';

  // Event dispatcher for parent components
  const dispatch = createEventDispatcher();

  // State variables
  let serviceName: string | null = null;
  let confirmationVisible: boolean = false;
  let isDeleting: boolean = false; 
  const deletionMessage = writable<string | null>(null);

  const API_URL = 'http://localhost/my-php-backend/admin/deleteServices.php';

  // Show confirmation prompt
  function showConfirmation() {
    if (!serviceName || serviceName.trim() === '') {
      alert('Please enter a valid service name.');
      return;
    }
    confirmationVisible = true; // Show the modal
  }

  // Handle delete request
  async function handleDelete() {
    if (!serviceName || serviceName.trim() === '') {
      alert('No service selected for deletion.');
      return;
    }

    isDeleting = true;

    try {
      const response = await fetch(API_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ serviceName: serviceName.trim() }),
      });

      const result: { success: boolean; message: string } = await response.json();

      if (response.ok) {
        deletionMessage.set(result.message || 'Service deleted successfully.');
        dispatch('delete', { serviceName });
        serviceName = null; // Reset serviceName after successful deletion
      } else {
        deletionMessage.set(result.message || 'An error occurred while deleting the service.');
      }
    } catch (error) {
      deletionMessage.set('Failed to connect to the server. Please try again later.');
    } finally {
      isDeleting = false;
      confirmationVisible = false; // Close modal after deletion attempt
    }
  }

  // Cancel confirmation and close the modal
  function handleCancel() {
    confirmationVisible = false; // Hide the modal
  }

  // Close notification message
  function closeMessage() {
    deletionMessage.set(null);
  }
</script>

<style>
  /* General modal styling */
  .modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
  }
  .modal-content {
    background: white;
    border-radius: 8px;
    width: 100%;
    max-width: 400px;
    padding: 16px;
  }
  .button {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    color: white;
    cursor: pointer;
  }
  .button-primary {
    background: #d9534f;
  }
  .button-secondary {
    background: #5bc0de;
  }
  .button:disabled {
    background: #ccc;
    cursor: not-allowed;
  }
</style>

<!-- Modal Container -->
{#if confirmationVisible}
  <div class="modal" role="dialog" aria-labelledby="modal-title">
    <div class="modal-content">
      <h2 id="modal-title" class="text-lg font-semibold text-red-600">Delete Service</h2>
      <p class="text-sm text-gray-700">Are you sure you want to delete the service <strong>{serviceName}</strong>?</p>

      <div class="flex justify-end gap-2 mt-4">
        <button
          on:click={handleCancel}
          class="button button-secondary"
        >
          Cancel
        </button>
        <button
          on:click={handleDelete}
          class="button button-primary"
          disabled={isDeleting}
        >
          {isDeleting ? 'Deleting...' : 'Delete'}
        </button>
      </div>
    </div>
  </div>
{/if}

<!-- Notification -->
{#if $deletionMessage}
  <div
    class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md"
    aria-live="polite"
  >
    <p>{$deletionMessage}</p>
    <button
      on:click={closeMessage}
      class="mt-2 text-sm underline hover:text-gray-200"
    >
      Close
    </button>
  </div>
{/if}

