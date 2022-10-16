(function () {
    'use strict'
    $(document).keyup('.searchBar',function() {
        var title = $('.searchCourse').val()
        console.log(title)
        var search_route = $('.search_route').val()

        if (title) {
            $('.searchBox').removeClass('d-none')
            $('.searchBox').addClass('d-block')
        } else {
            $('.searchBox').removeClass('d-block')
            $('.searchBox').addClass('d-none')
        }

        $.ajax({
            type: "GET",
            url: search_route,
            data: {'title': title},
            success: function (response) {
                $('.results').html(response);
            }
        });
    });
})()
