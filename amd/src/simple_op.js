/**
 * Created by dtomefer on 12/11/16.
 */
define(['jquery', 'core/ajax', 'jqueryui'], function ($, ajax) {
    return {
        initialise: function () {
            $('#generate-calculation').click(function () {
                var courseid = $('input[name="id"]').val();
                var gradeid = $('input[name="gradeid"]').val();
                var grades = [];
                $('input[name^=grades]:checked').each(function() {
                    var value = {
                        id: $(this).attr('data-id').toString()
                    };
                    grades.push(value);
                });
                var operation = $('input[name=operation]:checked').val();
                window.console.log(courseid + '++' + gradeid + '++' + grades + '++' + operation);
                var promises = ajax.call([
                    {
                        methodname: 'local_gradebook_get_calc',
                        args: {
                            courseid: courseid,
                            gradeid: gradeid,
                            operation: operation,
                            grades: grades
                        }
                    }
                ]);
                promises[0].done(function (response, data) {
                    $('#id_calculation').val(response);
                }).fail(function (ex) {
                    window.console.log(ex);
                });
            });
        }
    };
});