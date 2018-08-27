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
                $('.js-speaker-runner-container').html(response.view);
                //refreshRankingData();
            }
        },
        complete: function () {
            //refreshSpeakerData();
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

function refreshFinishedRunner() {
    $.ajax({
        type: 'POST',
        url: 'index.php?route=refreshFinishedRunner',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                $('.js-runner-count').html(response.completeRunnerCount + '/' + response.allRunnerCount);
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

function refreshRunnerFile()
{
    $.ajax({
        type: 'POST',
        url: 'index.php?route=readRunnerFile',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                console.log(response);
            }
        }
    })
}

$(document).ready(function () {
    window.setTimeout(hideNotifications, 5000); // 5 seconds

    if ($('.js-speaker-page').length > 0) {
        refreshSpeakerData();

        // window.setInterval(refreshFinishedRunner, 10000);
        window.setInterval(refreshSpeakerData, 1000);
    }

    if ($('.js-runner-file-page').length > 0) {
        window.setInterval(refreshRunnerFile, 1000);
    }


    if ($('.js-timemeasure-page').length > 0) {
        window.setInterval(generateTimeMeasureData, 1000);
    }

    /*var socket = io.connect('http://localhost:3000', {transports: ['websocket']});
console.log(document.location.host);
    socket.on('newData', function(message) {
        console.log(message);
    });*/
});

$(document).on('click', '.js-notification', function () {
    hideNotifications();
});

$(document).on('click', '.js-no-duplicat', function() {
    var $this = $(this),
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

$(document).on('click', '.js-duplicat', function() {
    var $this = $(this),
        runnerId = $this.data('originalRunnerId'),
        duplicateRunnerId = $this.data('duplicateRunnerId'),
        $runnerContainer = $this.closest('.js-runner-duplicate-container');

    $.ajax({
        type: 'Post',
        url: 'index.php?route=duplicate',
        dataType: 'json',
        data: {
            runnerId: runnerId,
            duplicateRunnerId: duplicateRunnerId
        },
        success: function (response) {
            if (response.status === 'success'){
                $runnerContainer.remove();
            }
        }
    })
});