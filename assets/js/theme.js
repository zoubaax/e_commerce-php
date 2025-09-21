http://localhost/php/e%20commerce/get_image.php?id=1// Function to set theme
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    updateTextColor(theme);
    
    // Update toggle switch
    const toggleSwitch = document.getElementById('theme-toggle');
    if (toggleSwitch) {
        toggleSwitch.checked = theme === 'dark';
    }
}

// Function to update text color based on theme
function updateTextColor(theme) {
    const categoryTable = document.querySelector('table.table'); 
    if (categoryTable) {
        const cells = categoryTable.querySelectorAll('td, th');
        cells.forEach(cell => {
            cell.style.color = theme === 'dark' ? '#ffffff' : '#333333';
        });
    }
}

// Initialize theme
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
    updateTextColor(savedTheme);
    
    // Add event listener to toggle switch
    const toggleSwitch = document.getElementById('theme-toggle');
    if (toggleSwitch) {
        toggleSwitch.addEventListener('change', () => {
            const theme = toggleSwitch.checked ? 'dark' : 'light';
            setTheme(theme);
        });
    }
});
