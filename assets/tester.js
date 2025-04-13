jQuery(function ($) {
    $.fn.ttpTester = function (options) {
        const wrap = this,
            rawOutput = $('.ttp-raw_output', wrap),
            retrieve = $('button[type="button"]', wrap),
            action = retrieve.data('action'),
            nonce = retrieve.data('nonce');

        const setup = $.extend({
            onRetrieve: null,
        }, options)

        retrieve.on('click', (e) => {
            console.log(typeof e);
            if (setup.onRetrieve) {
                setup.onRetrieve({
                    action,
                    nonce,
                    rawOutput,
                    retrieve,
                    wrap,
                });
            }
        })

        return this;
    }

    $('#ttp-tester-posts').ttpTester({
        onRetrieve: function (data) {
            const {
                action,
                nonce,
                rawOutput,
                retrieve,
            } = data

            wp.ajax.send({
                beforeSend: () => {
                    retrieve.prop('disabled', true);
                },
                data: {
                    action,
                    type: 'posts',
                    nonce,
                },
                method: 'get',
                url: ajaxurl,
            }).done((response) => {
                rawOutput.html(response.output)
            }).fail((xhr) => {
                console.error(`${xhr.status} ${xhr.statusText} ${xhr.responseText}`)
            }).always(() => {
                retrieve.prop('disabled', false);
            })
        }
    });

    $('#ttp-tester-single').ttpTester({
        onRetrieve: (data) => {
            const {
                action,
                nonce,
                rawOutput,
                retrieve,
                wrap
            } = data
        }
    });

    $('#ttp-tester-conversations').ttpTester({
        onRetrieve: (data) => {
            const {
                action,
                nonce,
                rawOutput,
                retrieve,
                wrap
            } = data
        }
    });
});