/**
 * Created by dtomefer on 12/11/16.
 */
define(['jquery', 'core/ajax', 'jqueryui'], function ($, ajax) {
    return {
        initialise: function () {
            $('#refresh').click(function () {
                var sesskey = $('#local-demo-sesskey').val();
                var courseid = $('#local-demo-courseid').val();
                var pageload = $('#local-demo-timepageload').val();
                var report = $('#local-demo-report').val();
                var page = $('#local-demo-page').val();
                window.console.log(sesskey + '-' + courseid + '-' + pageload + '-' + report + '-' + page);
                var promises = ajax.call([
                    {
                        methodname: 'local_gradebook_get_demo_calc',
                        args: {id: 2}
                    }
                ]);
                promises[0].done(function (response) {
                    $.each(response, function(i, grade) {
                        $('#'+grade.id).val(grade.value);
                    });
                }).fail(function (ex) {
                    window.console.log(ex);
                });
            });
        }
    };
});