// AJAX Form Submission to Update Profile Information
$('#editProfileForm').on('submit', function (e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        url: 'edit_profile.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            alert('Profile updated successfully!');
            location.reload(); 
        },
        error: function () {
            alert('Error updating profile!');
        }
    });
});

$(document).ready(function () {
    const imagesContainer = $('.gallery-container .row');

    // Handle search input
    $("#searchInput").on("input", function () {
        const query = $(this).val().trim();

        if (query.length > 0) {
            $.ajax({
                url: "search_result.php",
                type: "GET",
                data: { search: query },
                success: function (response) {
                    imagesContainer.html(response);  // Update the gallery
                },
                error: function () {
                    console.error("Failed to fetch search results.");
                },
            });
        } else {
            location.reload();
        }
    });
});
