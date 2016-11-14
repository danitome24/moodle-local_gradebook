/**
 * Created by dtomefer on 12/11/16.
 */
define(['jquery', 'core/ajax','jqueryui'], function($, ajax) {
    return {
        initialise: function() {
            $('#refresh').click(function() {
                var promises = ajax.call([
                    { methodname: 'local_gradebook_get_demo_calc',
                      args: {id: 2}
                    }
                ]);

                promises[0].done(function(response) {
                    window.console.log(response);
                }).fail(function(ex) {
                    window.console.log(ex);
                });
            });
        }
    };
});