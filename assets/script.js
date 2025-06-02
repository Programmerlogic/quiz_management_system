// assets/script.js

// Example of a simple confirmation dialog for navigation (optional)
document.querySelectorAll('.option-btn').forEach(button => {
    button.addEventListener('click', function(event) {
        let confirmAction = confirm("Are you sure you want to proceed?");
        if (!confirmAction) {
            event.preventDefault();
        }
    });
});
