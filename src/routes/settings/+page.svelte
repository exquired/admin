<script lang="ts">
    import Sidebar from "$lib/components/Sidebar.svelte";
  </script>
  
  <main class="bg-gray-100 flex flex-col lg:flex-row min-h-screen">
    <!-- main content -->
    <Sidebar />
    <div class="flex-1 p-8">
      <!-- header -->
      <header class="">
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-2xl font-medium">Welcome back, Van</h1>
          </div>
          
        </div>
        <hr class="p-2">

      </header>
  
      <!-- main content -->
      <section class="p-4 bg-white rounded-xl">
        <h2 class="text-xl font-bold mb-6">ACCOUNT</h2>
        <!-- profile pic -->
        <div class="flex items-center mb-6">
         
          <div>
            <h1 class="text-lg font-semibold mb-1 text-custom">Profile Picture</h1>
            <p class="text-sm text-gray-600 mb-2">PNG, JPG, up to 30MB</p>
            <button class="text-green-800 border-b border-green-800 hover:underline">Edit</button>
          </div>
        </div>
  
        <!-- details -->
        <div class="mb-6">
          <h3 class="text-lg font-semibold mb-2 text-custom">Details</h3>
          <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:space-x-4">

              <div class="flex flex-col flex-grow">
                <label for="first-name" class="text-sm font-medium">First Name</label>
                <input type="text" id="first-name" class="border rounded-full p-3 w-full" placeholder="Van" />
              </div>
              <div class="flex flex-col flex-grow">
                <label for="last-name" class="text-sm font-medium">Last Name</label>
                <input type="text" id="last-name" class="border rounded-full p-3 w-full" placeholder="Pogi" />
              </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:space-x-4">
              <div class="flex flex-col flex-grow">
                <label for="email" class="text-sm font-medium">Email Address</label>
                <input type="email" id="email" class="border rounded-full p-3 w-full" placeholder="vanpogi@gmail.com" />
              </div>
              <div class="flex flex-col flex-grow">
                <label for="mobile-number" class="text-sm font-medium">Mobile Number</label>
                <input type="text" id="mobile-number" class="border rounded-full p-3 w-full" placeholder="09696969" />
              </div>
            </div>
            <div>
              <button class="bg-custom text-white py-2 px-4 rounded-full hover:bg-green-800">Save</button>
            </div>
          </div>
        </div>
  
        <!-- change pass -->
        <div>
          <h3 class="text-lg font-semibold mb-2 text-custom">Change Password</h3>
          <div class="space-y-4">
            <div class="flex flex-col">
              <label for="current-password" class="text-sm font-medium">Current Password</label>
              <input type="password" id="current-password" class="border rounded-full p-3 w-full max-w-lg" placeholder="Current Password" />
            </div>
            <div class="flex flex-col">
              <label for="new-password" class="text-sm font-medium">New Password</label>
              <input type="password" id="new-password" class="border rounded-full p-3 w-full max-w-lg" placeholder="New Password" />
            </div>
            <div>
              <button class="bg-custom text-white py-2 px-4 rounded-full hover:bg-green-800">Save</button>
            </div>
          </div>
        </div>
      </section>

      <!-- section for choices -->
      <section class="p-4 bg-white rounded-xl mt-5 grid grid-cols-1 gap-6">
        <h2 class="text-xl font-bold">Account Deletion</h2>
            <!-- deletion -->
            <div class="bg-white p-6 rounded-lg border max-w-full lg:max-w-screen-lg">
              <div class="flex items-center mb-4">
                  <input type="radio" id="deletion" name="account-action" class="form-radio h-5 w-5 text-red-500" />
                  <label for="deletion" class="ml-2 text-lg font-medium">DELETION</label>
              </div>
              <p class="text-gray-600">
                  Before deleting your account, note that this action is irreversible. All your data, including profile information, listings,
                   and interactions, will be permanently removed from El Galeria's system. Once deleted, you won't be able to recover your account
                    or any associated data.
              </p>
            </div>

            <!-- buttons -->
            <div class="flex justify-start space-x-4">
                <button class="bg-custom text-white py-2 px-4 rounded-full hover:bg-green-800">Cancel</button>
                <button class="bg-custom text-white py-2 px-4 rounded-full hover:bg-green-800">Continue</button>
            </div>


      </section>
    </div>

  </main>

  
  