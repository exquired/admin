<script lang="ts">
  import logo from '$lib/assets/logo/LOGO.png';
  import { onMount } from 'svelte';

  let currentRoute = window.location.pathname; 
  let user = { AFName: 'Loading...' }; 

  function navigateTo(route: string) {
      currentRoute = route; // Update active route
      // Optionally, change the window location if you want to navigate
      window.history.pushState({}, '', route);
  }

  onMount(async () => {
      // Update the currentRoute if the user navigates using the browser's back/forward buttons
      window.addEventListener('popstate', () => {
          currentRoute = window.location.pathname;
      });

      // Fetch user data
      try {
          const response = await fetch('http://localhost/my-php-backend/admin/Sidebar.php'); 
          if (response.ok) {
              user = await response.json(); 
          } else {
              console.error('Failed to fetch user data');
          }
      } catch (error) {
          console.error('Error fetching user data:', error);
      }
  });
</script>

<!-- Sidebar -->
<aside class="w-64 bg-gray-200 shadow-lg">
  <div class="p-6">
      <img src="{logo}" alt="EL_GALERIA" class="h-15 mb-8" />
      
      <div class="mb-8">
          <h3 class="font-medium">{user.AFName}</h3>
          <p class="text-sm text-gray-500">Admin</p>
      </div>

      <nav class="space-y-1">
        <a 
            href="/dashboard"
            class={`flex items-center gap-3 px-4 py-2 rounded-lg ${currentRoute === '/dashboard' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100'}`}
            on:click={() => navigateTo('/dashboard')}
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5zM14 5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V5zM4 15a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4zM14 15a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-4z" stroke="currentColor" stroke-width="2"/>
            </svg>
            Dashboard
        </a>
        <a 
            href="/category" 
            class={`flex items-center gap-3 px-4 py-2 rounded-lg ${currentRoute === '/category' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100'}`}
            on:click={() => navigateTo('/category')}
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 6v12M6 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Category
        </a>

        <a 
            href="/services" 
            class={`flex items-center gap-3 px-4 py-2 rounded-lg ${currentRoute === '/services' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100'}`}
            on:click={() => navigateTo('/services')}
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 6v12M6 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Services
        </a>

        <a 
            href="/reports" 
            class={`flex items-center gap-3 px-4 py-2 rounded-lg ${currentRoute === '/reports' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100'}`}
            on:click={() => navigateTo('/reports')}
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Reports
        </a>
        <a 
            href="/settings" 
            class={`flex items-center gap-3 px-4 py-2 rounded-lg ${currentRoute === '/settings' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100'}`}
            on:click={() => navigateTo('/settings')}
        >
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Settings
        </a>
        <a 
            href="../"
            class={`flex items-center gap-3 px-4 py-2 rounded-lg ${currentRoute === '/logout' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-100'}`}
            on:click={() => navigateTo('/logout')}
        >
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Logout
        </a>
    </nav>
  </div>
</aside>