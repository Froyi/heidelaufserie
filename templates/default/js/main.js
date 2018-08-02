function hideNotifications() {
    $('.js-notification-container').hide();
}

function refreshSpeakerData() {
    $.ajax({
        type: 'POST',
        url: 'index.php?route=refreshSpeakerData',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                $('.new-runner').remove();
                $(response.view).prependTo('.js-speaker-runner-container').hide().slideDown();
                // refreshRankingData();
            }
        },
        complete: function () {
            refreshSpeakerData();
        }
    })
}

function refreshRankingData() {
    var $rankingContainer = $('.js-speaker-competition-status');
    $.ajax({
        type: 'POST',
        url: 'index.php?route=refreshRankingData',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                $rankingContainer.html(response.view);
            }
        }
    })
}

function generateTimeMeasureData() {
    $.ajax({
        type: 'POST',
        url: 'index.php?route=generateTimeMeasureData',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                console.log(response.timeMeasure);
            }
        }
    })
}

$(document).ready(function () {
    window.setTimeout(hideNotifications, 5000); // 5 seconds

    if ($('.js-speaker-page').length > 0) {
        refreshSpeakerData();
    }


    if ($('.js-timemeasure-page').length > 0) {
        window.setInterval(generateTimeMeasureData, 1000);
    }

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