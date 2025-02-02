<div class="sct-3-register sct-section my-10">
    <div class="data-content container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Create Your Account</h2>
        <p class="mt-2 text-gray-600">Fill in the details below to register for a new account.</p>

        <form id="register-form" class="data-form mt-6">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">email:</label>
                <input type="text" id="email" name="email" required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-400"
                    placeholder="Choose a email">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password:</label>
                <input type="password" id="password" name="password" required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-400"
                    placeholder="Create a password">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Register
            </button>
        </form>

        <div id="message" class="mt-4 text-red-500"></div>

        <div class="data-have-account mt-6 text-gray-700 text-sm">
            <p>Already have an account? <a href="login" class="text-blue-600 hover:underline">Login here</a>.</p>
        </div>
    </div>

</div>