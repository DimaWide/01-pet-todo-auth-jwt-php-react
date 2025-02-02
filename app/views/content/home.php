<div class="sct-jwt">
    <div class="data-content container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Token will expire in</h2>
        <div class="data-countdown" id="countdown"></div>
    </div>
</div>

<script>
    async function checkTokenValidity() {
        try {
            const response = await fetch('http://dev.todo/api/check-token', {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',  
                },
            });

            if (response.ok) {
                const data = await response.json();
                const expirationTime = data.exp; 
                const currentTime = Math.floor(Date.now() / 1000);
                const timeLeft = expirationTime - currentTime;

                if (timeLeft > 0) {
                    startCountdown(timeLeft);
                } else {
                    console.log('Token has expired');
                    redirectToLogin();
                }
            } else {
                console.log('Token is invalid or expired');
                redirectToLogin();
            }
        } catch (error) {
            console.error('Error checking token:', error);
            redirectToLogin();
        }
    }

    // Function to start the countdown
    function startCountdown(timeLeft) {
        const countdownElement = document.getElementById('countdown');

        const updateCountdown = () => {
            if (timeLeft <= 0) {
                countdownElement.innerHTML = 'Token expired';
                clearInterval(interval);
                redirectToLogin();
                return;
            }

            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;

            countdownElement.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
            timeLeft--;
        };

        const interval = setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial update
    }

    // Redirect to login page
    function redirectToLogin() {
        //window.location.href = '/public/login'; // Redirect to login page
    }

    // Start token validation check
    checkTokenValidity();
</script>
