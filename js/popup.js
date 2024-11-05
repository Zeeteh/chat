function openPopup(page) {
    fetch(`content/${page}.html`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('popupText').innerHTML = data;
            document.getElementById('popupOverlay').style.display = 'flex';
        })
        .catch(error => console.error('Error loading content:', error));
}

function closePopup() {
    document.getElementById('popupOverlay').style.display = 'none';
}

// Event Listeners für Links und Menüeinträge
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.menu a, .top-nav a').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const pageName = this.getAttribute('data-page');
            openPopup(pageName);
        });
    });
});
