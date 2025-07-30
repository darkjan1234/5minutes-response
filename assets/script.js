// Timer functionality
function updateTimers() {
    const timers = document.querySelectorAll('[id^="timer-"]');
    
    timers.forEach(timer => {
        const expiresAt = new Date(timer.dataset.expires);
        const now = new Date();
        const diff = Math.floor((expiresAt - now) / 1000);
        
        if (diff <= 0) {
            timer.textContent = "EXPIRED";
            timer.parentElement.classList.remove('alert-warning');
            timer.parentElement.classList.add('alert-danger');
        } else {
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;
            timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    });
}

// Update timers every second
setInterval(updateTimers, 1000);

// Geolocation
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    document.getElementById("latitude").value = position.coords.latitude;
    document.getElementById("longitude").value = position.coords.longitude;
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
}

// Auto-refresh dashboard every 30 seconds
if (window.location.search.includes('page=dashboard')) {
    setInterval(() => {
        window.location.reload();
    }, 30000);
}