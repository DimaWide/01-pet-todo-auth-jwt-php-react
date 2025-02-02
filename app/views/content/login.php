<div class="sct-1-login sct-section my-10">
    <div class="data-content container mx-auto  p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Login to Your Account</h2>

        <p class="data-subtitle mt-2 text-gray-600">Please enter your credentials to access your tasks.</p>

        <form id="login-form" class="login-form mt-6">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-semibold mb-2">email:</label>
                <input type="text" id="email" name="email" required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-400"
                    placeholder="Enter your email">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-semibold mb-2">Password:</label>
                <input type="password" id="password" name="password" required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-400"
                    placeholder="Enter your password">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Login
            </button>
        </form>

        <div id="message" class="mt-4 text-red-500"></div>

        <div class="data-have-account mt-6 text-gray-700 text-sm">
            <p>Don't have an account? <a href="register" class="text-blue-600 font-600 hover:underline">Register here</a>.</p>
        </div>
    </div>
</div>