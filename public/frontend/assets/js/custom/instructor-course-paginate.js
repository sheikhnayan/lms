(function ($) {
    "use strict";
    let loadMorePageCount;
    $(function () {
        loadMorePageCount = 1;
    });

    $(document).on('click', '.loadMore', function () {
        var instructorCoursePaginateRoute = $('.instructorCoursePaginateRoute').val();
        loadMorePageCount++;
        $.ajax({
            type: "POST",
            url: instructorCoursePaginateRoute,
            data: {
                'page': loadMorePageCount,
                '_token': $('meta[name="csrf-token"]').attr('content')
            },
            datatype: "json",
            success: function (res) {
                if (res.courses.current_page == res.courses.last_page) {
                    $("#loadMoreBtn").removeClass('d-block')
                    $("#loadMoreBtn").addClass('d-none')
                }
                $('#appendInstructorCourses').append(res.appendInstructorCourses);
            }
        });
    });
})(jQuery)
