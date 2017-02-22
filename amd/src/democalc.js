/**
 * Created by dtomefer on 12/11/16.
 */
define(['jquery', 'core/ajax', 'jqueryui'], function ($, ajax) {
    return {
        initialise: function () {
            $('#local-gradebook-demo-calculate').click(function () {
                var sesskey = $('#local-demo-sesskey').val();
                var courseid = $('#local-demo-courseid').val();
                var pageload = $('#local-demo-timepageload').val();
                var report = $('#local-demo-report').val();
                var page = $('#local-demo-page').val();
                var values = [];
                $.each($('.local-demo-grades'), function () {
                    var value = {
                        id: parseInt($(this).attr('name')),
                        value: parseInt($(this).val()),
                        type: String($(this).attr('data-type'))
                    };

                    window.console.log(value);
                    values.push(value);
                });

                window.console.log(sesskey + '-' + courseid + '-' + pageload + '-' + report + '-' + page + '-'
                    + JSON.stringify(values));
                var promises = ajax.call([
                    {
                        methodname: 'local_gradebook_get_demo_calc',
                        args: {
                            sesskey: sesskey,
                            id: courseid,
                            timepageload: pageload,
                            report: report,
                            page: page,
                            grades: values
                        }
                    }
                ]);
                promises[0].done(function (response) {
                    window.console.log(response);
                    $.each(response, function (i, grade) {
                        window.console.log(grade.id);
                        $('#grade-' + grade.id).val(grade.value);
                    });
                }).fail(function (ex) {
                    window.console.log(ex);
                });
            });
        }
    };
});