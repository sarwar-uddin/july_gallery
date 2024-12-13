$(document).ready(function () {
    let currentImageIndex = -1;
    const images = $('.gallery-image');

    function updateModalContent(index) {
        const image = $(images[index]);
        const imageId = image.data('id');

        // Fetch image details
        $.getJSON('image_data.php', { action: 'details', image_id: imageId }, function (data) {
            if (data.error) {
                console.error(data.error);
                return;
            }
            $('#imageModalLabel').text(data.title || 'Untitled');
            $('#modalImage').attr('src', `uploads/${data.filename}`);
            $('#modalDescription').text(data.description || 'No description available.');
            $('#modalImage').data('image-id', imageId);

            // Set event date and uploaded by (username as hyperlink)
            $('#modalEventDate').text(data.event_date || 'No event date');
            $('#modalUploadedBy').html(`<a href="profile.php?user_id=${data.user_id}" target="_blank">${data.username}</a>`);

        });

        // Fetch image stats (views and likes)
        $.getJSON('image_data.php', { action: 'stats', image_id: imageId }, function (data) {
            $('#modalLikes').text(data.likes);
            $('#likeButton').attr('data-liked', data.userLiked ? 'true' : 'false');
            $('#likeIcon')
                .toggleClass('bi-heart-fill', data.userLiked)
                .toggleClass('bi-heart', !data.userLiked);
        });


        // Load comments for the current image
        loadComments(imageId);

        // Update hidden input for comment form
        $('#imageIdInput').val(imageId);


        currentImageIndex = index;

        // Prevent modal size change based on image size
        $('#imageModal .modal-dialog').css('max-height', '90vh');

    }

    $('.gallery-image').on('click', function () {
        const index = images.index(this);
        updateModalContent(index);
        $('#imageModal').modal('show');
    });

    $('.modal-nav.left').on('click', function () {
        if (currentImageIndex > 0) {
            updateModalContent(currentImageIndex - 1);
        }
    });

    $('.modal-nav.right').on('click', function () {
        if (currentImageIndex < images.length - 1) {
            updateModalContent(currentImageIndex + 1);
        }
    });

    $(document).on('keydown', function (e) {
        if ($('#imageModal').is(':visible')) {
            if (e.key === 'ArrowLeft' && currentImageIndex > 0) {
                updateModalContent(currentImageIndex - 1);
            } else if (e.key === 'ArrowRight' && currentImageIndex < images.length - 1) {
                updateModalContent(currentImageIndex + 1);
            }
        }
    });

    function showNotification(message, bgColor = 'bg-danger') {
        const notification = $('#notification');
        notification.find('.toast-body').text(message);
        notification.removeClass('bg-danger bg-success bg-warning').addClass(bgColor);
        notification.fadeIn().delay(3000).fadeOut(); // Show for 3 seconds
    }

    $('#likeButton').on('click', function () {
        const imageId = $(images[currentImageIndex]).data('id');
        const action = $(this).attr('data-liked') === 'true' ? 'unlike' : 'like';

        if (!isUserLoggedIn) {
            showNotification('You need to log in to like images.');
            $(this).blur(); // Remove focus
            return;
        }

        $.post('update_image_stats.php', {
            image_id: imageId,
            action: action
        }, function (response) {
            const data = JSON.parse(response);
            $('#modalLikes').text(data.likes);
            $('#likeButton')
                .attr('data-liked', action === 'like' ? 'true' : 'false')
                .blur();
            $('#likeIcon')
                .toggleClass('bi-heart-fill', action === 'like')
                .toggleClass('bi-heart', action === 'unlike');
        });
    });


    function loadComments(imageId) {
        $.get('get_comments.php', {
            image_id: imageId
        }, function (data) {
            $('#commentsList').html(data);
        });
    }

    $('#commentForm').on('submit', function (e) {
        e.preventDefault();
        const imageId = $('#imageIdInput').val();
        const comment = $('#commentInput').val();
        $.post('post_comment.php', {
            image_id: imageId,
            comment: comment
        }, function (response) {
            $('#commentInput').val(''); // Clear the input field
            loadComments(imageId); // Reload comments
        });
    });

    $('.gallery-image').on('click', function () {
        const imageId = $(this).data('id');
        $('#imageIdInput').val(imageId); // Set image_id for the comment form
        loadComments(imageId); // Load comments for the image
    });

});

$(document).ready(function () {
    // Handle the share button dropdown
    $('#shareButton').click(function (e) {
        e.stopPropagation(); // Prevent the modal from interfering
        $(this).blur(); // Remove focus from the share button
    });

    // Function to get the image ID from the modal
    const getImageId = () => $('#modalImage').data('image-id');

    // Function to generate the URL to the `image.php` page
    const getShareURL = () => {
        const imageId = getImageId();
        return `${window.location.origin}/image.php?id=${imageId}`;
    };

    // Set up sharing functionality
    $('#shareFacebook').click(function () {
        const url = getShareURL();
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
        $(this).blur(); // Remove focus from the share option
    });

    $('#shareTwitter').click(function () {
        const url = getShareURL();
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}`, '_blank');
        $(this).blur(); // Remove focus from the share option
    });

    $('#shareWhatsapp').click(function () {
        const url = getShareURL();
        window.open(`https://wa.me/?text=${encodeURIComponent(url)}`, '_blank');
        $(this).blur(); // Remove focus from the share option
    });

    // Copy Link functionality
    $('#copyLink').click(function () {
        const url = getShareURL();
        navigator.clipboard.writeText(url).then(() => {
            const copyButton = $('#copyLink');
            copyButton.text('Copied!');
            setTimeout(() => copyButton.text('Copy Link'), 2000);
            copyButton.blur(); // Remove focus from the copy link button
        });
    });
});


$(document).ready(function () {
    // Toggle comment section
    $('#commentButton').click(function () {
        $(this).blur(); // Remove focus from the comment button
        const commentSection = $('.modal-comments');
        const modalContent = $('.modal-content'); // Modal's scrollable area

        if (commentSection.is(':visible')) {
            // If the comment section is already visible, just hide it
            commentSection.slideUp(300);
        } else {
            // If it's hidden, show it and scroll after it is fully visible
            commentSection.slideDown(300, function () {
                const scrollToPosition = commentSection.offset().top - modalContent.offset().top + modalContent.scrollTop();
                modalContent.animate({ scrollTop: scrollToPosition }, 500);
            });
        }
    });
});
