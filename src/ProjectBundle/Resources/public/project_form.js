function getFormProject(url) {
    $.ajax({
        url: url,
        method: 'GET',
        success: function (html) {
            $('#alert').html(html);
            $('#alert').fadeIn();
        },
        error: function () {
            $('#alert').html('error show form');
        }
    });
}

