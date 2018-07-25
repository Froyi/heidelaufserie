function hideNotifications() {
    $('.js-notification-container').hide();
}

$(document).ready(function () {
    window.setTimeout(hideNotifications, 5000); // 5 seconds
});

$(document).on('click', '.js-notification', function () {
    hideNotifications();
});

$(document).on('click', '.js-no-duplicat', function() {
    let $this = $(this),
        runnerId = $this.data('originalRunnerId'),
        $runnerContainer = $this.closest('.js-runner-duplicate-container');

    // noinspection JSUnresolvedVariable
    $.ajax({
        type: 'POST',
        url: 'index.php?route=noDuplicate',
        dataType: 'json',
        data: {
            runnerId: runnerId
        },
        success: function (response) {
            if (response.status === 'success') {
                $runnerContainer.remove();
            }
        }
    })
});