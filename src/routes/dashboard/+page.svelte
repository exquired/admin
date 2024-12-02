<script lang="ts">
  import Sidebar from "$lib/components/Sidebar.svelte";
  import { onMount } from "svelte";

  let totalUsers: number = 0;
  let totalClients: number = 0;
  let totalProviders: number = 0;
  let monthlyData: { month: string; users: number }[] = [];
  let maxUsers: number = 0;
  const currentYear: number = new Date().getFullYear();

  const fetchDashboardData = async () => {
    try {
      const response = await fetch('http://localhost/my-php-backend/admin/Dashboard.php');
      const data = await response.json();

      if (response.ok) {
        totalUsers = data.totalUsers;
        totalClients = data.totalClients;
        totalProviders = data.totalProviders;

        const months = [
          "Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
        ];
        monthlyData = months.map((month) => ({
          month,
          users: data.monthlyUsers.find((entry: { month: string }) => entry.month === month)?.users || 0,
        }));

        maxUsers = Math.max(...monthlyData.map((entry) => entry.users));
      } else {
        console.error("Error fetching dashboard data:", data.message || "Unknown error");
      }
    } catch (error) {
      console.error("Error fetching dashboard data:", error);
    }
  };

  onMount(() => {
    fetchDashboardData();
  });
</script>

<div class="flex min-h-screen bg-gray-50">
  <Sidebar />

  <main class="flex-1 p-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-semibold text-gray-800">Welcome back, Van</h1>
    </div>
    <hr class="border-gray-300 mb-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-700">DASHBOARD</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 flex-1">
      <!-- Monthly Users Chart -->
      <div class="col-span-3 bg-white shadow-lg p-6 rounded-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold text-lg text-gray-800">MONTHLY USERS</h3>
          <p class="text-sm text-gray-500">El Galeria</p>
        </div>
        <div class="h-64 flex items-end justify-between relative">
          {#each monthlyData as { month, users }}
            <div class="flex flex-col items-center relative group">
              <div
                class="w-10 bg-gradient-to-b from-emerald-400 to-emerald-600 rounded"
                style="height: {(users / maxUsers) * 100}%"
                aria-label="{month}: {users} users"
              >
                <span class="absolute bottom-full mb-1 text-xs text-gray-700 hidden group-hover:block">{users}</span>
              </div>
              <span class="text-xs mt-2 text-gray-600">{month}</span>
            </div>
          {/each}
          <!-- Y-axis Labels -->
          <div class="absolute left-0 h-full flex flex-col justify-between">
            {#each Array(5) as _, index}
              <span class="text-gray-500 text-xs">{Math.round(maxUsers * (index + 1) / 5)}</span>
            {/each}
          </div>
        </div>
        
        <div class="flex justify-between items-center mt-4">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            <span class="text-sm text-gray-600">USERS</span>
            <span class="text-sm text-gray-500 ml-4">YEAR: {currentYear}</span>
          </div>
          <div class="bg-emerald-100 text-emerald-600 px-4 py-1 rounded-full">
            Total: {totalUsers}
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="space-y-4">
        <div class="bg-white shadow-md p-6 rounded-xl border border-emerald-200">
          <p class="text-sm text-gray-600 mb -1">TOTAL USERS</p>
          <p class="text-3xl font-semibold text-gray-800">{totalUsers}</p>
        </div>
        <div class="bg-white shadow-md p-6 rounded-xl border border-emerald-200">
          <p class="text-sm text-gray-600 mb-1">TOTAL CLIENTS</p>
          <p class="text-3xl font-semibold text-gray-800">{totalClients}</p>
        </div>
        <div class="bg-white shadow-md p-6 rounded-xl border border-emerald-200">
          <p class="text-sm text-gray-600 mb-1">TOTAL SERVICE PROVIDERS</p>
          <p class="text-3xl font-semibold text-gray-800">{totalProviders}</p>
        </div>
        <div class="bg-white shadow-md p-6 rounded-xl border border-emerald-200">
          <p class="text-sm text-gray-600 mb-1">REVENUE</p>
          <p class="text-3xl font-semibold text-gray-800">{totalProviders}</p>
        </div>
        <div class="bg-white shadow-md p-6 rounded-xl border border-emerald-200">
          <p class="text-sm text-gray-600 mb-1">SALES</p>
          <p class="text-3xl font-semibold text-gray-800">{totalProviders}</p>
        </div>
      </div>
    </div>
  </main>
</div>