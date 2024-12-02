<script lang="ts">
    import Sidebar from "$lib/components/Sidebar.svelte";
  
    const reports = [
        { username: "pogi", userType: "SERVICE PROVIDER", reportedby: "kian", report: "FRAUD" },
        { username: "hindi_scammer", userType: "SERVICE PROVIDER", reportedby: "kian", report: "FRAUD" },
        { username: "calvin", userType: "SERVICE PROVIDER", reportedby: "kian", report: "FRAUD" },
        { username: "darren", userType: "CLIENT", reportedby: "kian", report: "FRAUD" },
        { username: "kyle", userType: "SERVICE PROVIDER", reportedby: "kian", report: "FRAUD" }
    ];
  
    let viewAs: 'Dismissed' | 'Pending' | 'Resolved' = 'Pending'; // Default to 'Pending'
    let searchQuery: string = '';
    let showConfirmation: boolean = false;
    let selectedAction: { username: string, action: string } | null = null;
  
    function handleAction(username: string, action: string) {
        selectedAction = { username, action };
        showConfirmation = true;
    }
  
    function confirmAction() {
        if (selectedAction) {
            console.log(`Action "${selectedAction.action}" confirmed for ${selectedAction.username}`);
            // Perform the action here (e.g., API call)
            selectedAction = null;
            showConfirmation = false;
        }
    }
  
    function cancelAction() {
        selectedAction = null;
        showConfirmation = false;
    }
  
    function filteredReports() {
        return reports.filter(report => 
            report.username.toLowerCase().includes(searchQuery.toLowerCase())
        );
    }
  </script>
  
  <div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <Sidebar/>
  
    <!-- Main Content -->
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-medium">Welcome back, Van</h1>
            </div>
        </div>
        <hr class="p-2">
  
        <div class="bg-white rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">REPORTS</h2>
                <button
                    aria-label="Export Services"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </button>
            </div>
  
            <!-- View Items Display -->
            <div class="flex flex-wrap gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">View as:</span>
                    <select bind:value={viewAs} class="border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="Ignore">Ignored</option>
                        <option value="Ban">Banned</option>
                        <option value="Restrict">Restricted</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>
                <div class="flex-1">
                    <input
                        type="text"
                        placeholder="Search here..."
                        bind:value={searchQuery}
                        aria-label="Search Services"
                        class="w-full border rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    />
                </div>
            </div>
  
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">USERNAME</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">USER TYPE</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">REPORTED BY</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">REPORT</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each filteredReports() as report}
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">{report.username}</td>
                                <td class="py-3 px-4">
                                    <span class={`px-3 py-1 rounded-full text-sm ${report.userType === 'CLIENT' ? 'bg-emerald-100 text-emerald-700' : 'bg-custom text-white'}`}>
                                        {report.userType}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class={`text-sm ${report.reportedby === 'CLIENT'}`}>
                                        {report.reportedby}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-red-600">{report.report}</td>
                                <td class="py-3 px-4 text-center">
                                    <button 
                                        on:click={() => handleAction(report.username, "Restrict")}
                                        class="bg-yellow-500 text-white px-5 py-2 rounded"
                                        aria-label={`Restrict ${report.username}`}
                                    >
                                        Restrict
                                    </button>
                                    <button 
                                        on:click={() => handleAction(report.username, "Ban")}
                                        class="bg-red-800 text-white px-5 py-2 rounded"
                                        aria-label={`Ban ${report.username}`}
                                    >
                                        Ban
                                    </button>
                                    <button 
                                        on:click={() => handleAction(report.username, "Ignore")}
                                        class="bg-green-800 text-white px-5 py-2 rounded"
                                        aria-label={`Ignore ${report.username}`}
                                    >
                                        Ignore
                                    </button>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
  
        {#if showConfirmation}
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold">Confirm Action</h3>
                    <p>Are you sure you want to {selectedAction?.action} {selectedAction?.username}?</p>
                    <div class="mt-4 flex justify-end">
                        <button on:click={confirmAction} class="bg-red-500 text-white px-4 py-2 rounded mr-2" aria-label={`Confirm ${selectedAction?.action} for ${selectedAction?.username}`}>Confirm</button>
                        <button on:click={cancelAction} class="bg-gray-300 text-gray-700 px-4 py-2 rounded" aria-label="Cancel action">Cancel</button>
                    </div>
                </div>
            </div>
        {/if}
    </main>
  </div>