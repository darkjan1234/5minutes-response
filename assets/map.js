let map, selectionMap, previewMap;
let selectedLat, selectedLng;
let currentMarker;

// Initialize maps when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize different maps based on current page
    if (document.getElementById('reports-map')) {
        initReportsMap();
    }
    if (document.getElementById('my-reports-map')) {
        initMyReportsMap();
    }
    if (document.getElementById('preview-map')) {
        initPreviewMap();
    }
});

// Initialize LGU Reports Map
function initReportsMap() {
    map = L.map('reports-map').setView([6.2442, 124.2539], 13); // Tupi coordinates
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add markers for all reports
    if (typeof reportsData !== 'undefined') {
        reportsData.forEach(report => {
            if (report.latitude && report.longitude) {
                addReportMarker(report);
            }
        });
    }
}

// Initialize My Reports Map
function initMyReportsMap() {
    const myMap = L.map('my-reports-map').setView([6.2442, 124.2539], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(myMap);
    
    if (typeof myReportsData !== 'undefined') {
        myReportsData.forEach(report => {
            if (report.latitude && report.longitude) {
                const color = getStatusColor(report.status);
                const marker = L.circleMarker([report.latitude, report.longitude], {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.7,
                    radius: 8
                }).addTo(myMap);
                
                marker.bindPopup(`
                    <strong>${report.issue_type}</strong><br>
                    Status: ${report.status}<br>
                    Date: ${report.submitted_at}
                `);
            }
        });
    }
}

// Initialize Preview Map
function initPreviewMap() {
    previewMap = L.map('preview-map').setView([6.2442, 124.2539], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(previewMap);
}

// Add report marker to map
function addReportMarker(report) {
    const color = getStatusColor(report.status);
    const marker = L.circleMarker([report.latitude, report.longitude], {
        color: color,
        fillColor: color,
        fillOpacity: 0.7,
        radius: 10,
        className: `marker-${report.status}`
    }).addTo(map);
    
    marker.on('click', function() {
        showReportDetails(report);
    });
    
    marker.bindTooltip(`${report.issue_type} - ${report.status}`);
}

// Get color based on status
function getStatusColor(status) {
    switch(status) {
        case 'pending': return '#dc3545';
        case 'acknowledged': return '#ffc107';
        case 'in_progress': return '#007bff';
        case 'resolved': return '#28a745';
        default: return '#6c757d';
    }
}

// Show report details in sidebar
function showReportDetails(report) {
    const detailsDiv = document.getElementById('report-details');
    detailsDiv.innerHTML = `
        <h6>${report.issue_type}</h6>
        <p><strong>Reporter:</strong> ${report.username}</p>
        <p><strong>Status:</strong> <span class="badge bg-${getStatusBadge(report.status)}">${report.status}</span></p>
        <p><strong>Description:</strong> ${report.description}</p>
        <p><strong>Submitted:</strong> ${report.submitted_at}</p>
        ${report.photo_path ? `<img src="${report.photo_path}" class="img-fluid mb-2" style="max-height: 150px;">` : ''}
        ${report.escalated ? '<p class="text-danger"><strong>ESCALATED</strong></p>' : ''}
    `;
}

// Get Bootstrap badge class for status
function getStatusBadge(status) {
    switch(status) {
        case 'pending': return 'warning';
        case 'acknowledged': return 'info';
        case 'in_progress': return 'primary';
        case 'resolved': return 'success';
        default: return 'secondary';
    }
}

// Filter reports on map
function filterReports(status) {
    // Remove all markers
    map.eachLayer(layer => {
        if (layer instanceof L.CircleMarker) {
            map.removeLayer(layer);
        }
    });
    
    // Add filtered markers
    if (typeof reportsData !== 'undefined') {
        reportsData.forEach(report => {
            if (report.latitude && report.longitude) {
                if (status === 'all' || report.status === status) {
                    addReportMarker(report);
                }
            }
        });
    }
}

// Show map modal for location selection
function showMapModal() {
    const modal = new bootstrap.Modal(document.getElementById('mapModal'));
    modal.show();
    
    // Initialize selection map
    setTimeout(() => {
        if (!selectionMap) {
            selectionMap = L.map('selection-map').setView([6.2442, 124.2539], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(selectionMap);
            
            selectionMap.on('click', function(e) {
                selectedLat = e.latlng.lat;
                selectedLng = e.latlng.lng;
                
                if (currentMarker) {
                    selectionMap.removeLayer(currentMarker);
                }
                
                currentMarker = L.marker([selectedLat, selectedLng]).addTo(selectionMap);
            });
        }
        selectionMap.invalidateSize();
    }, 300);
}

// Confirm selected location
function confirmLocation() {
    if (selectedLat && selectedLng) {
        document.getElementById('latitude').value = selectedLat;
        document.getElementById('longitude').value = selectedLng;
        updatePreviewMap(selectedLat, selectedLng);
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
        modal.hide();
    }
}

// Update preview map
function updatePreviewMap(lat, lng) {
    if (previewMap) {
        previewMap.setView([lat, lng], 15);
        
        // Remove existing markers
        previewMap.eachLayer(layer => {
            if (layer instanceof L.Marker) {
                previewMap.removeLayer(layer);
            }
        });
        
        // Add new marker
        L.marker([lat, lng]).addTo(previewMap);
    }
}

// Toggle between list and map view
function toggleView(view) {
    const listView = document.getElementById('list-view');
    const mapView = document.getElementById('map-view');
    const listBtn = document.getElementById('list-view-btn');
    const mapBtn = document.getElementById('map-view-btn');
    
    if (view === 'map') {
        listView.style.display = 'none';
        mapView.style.display = 'block';
        listBtn.classList.remove('btn-primary');
        listBtn.classList.add('btn-outline-primary');
        mapBtn.classList.remove('btn-outline-info');
        mapBtn.classList.add('btn-info');
        
        // Refresh map
        setTimeout(() => {
            if (document.getElementById('my-reports-map')) {
                initMyReportsMap();
            }
        }, 100);
    } else {
        listView.style.display = 'block';
        mapView.style.display = 'none';
        listBtn.classList.remove('btn-outline-primary');
        listBtn.classList.add('btn-primary');
        mapBtn.classList.remove('btn-info');
        mapBtn.classList.add('btn-outline-info');
    }
}

// Enhanced geolocation function
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
            
            updatePreviewMap(lat, lng);
        }, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}