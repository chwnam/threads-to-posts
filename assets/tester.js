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
            } = data

            const id = $('#ttp-single-id').val()
            if (0 === id.length) {
                alert('Please enter a post ID')
                return
            }

            wp.ajax.send({
                beforeSend: () => {
                    retrieve.prop('disabled', true);
                },
                data: {
                    action,
                    id,
                    type: 'single',
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

    $('#ttp-tester-conversations').ttpTester({
        onRetrieve: (data) => {
            const {
                action,
                nonce,
                rawOutput,
                retrieve,
            } = data

            const id = $('#ttp-conversations-id').val()
            if (0 === id.length) {
                alert('Please enter a post ID')
                return
            }

            wp.ajax.send({
                beforeSend: () => {
                    retrieve.prop('disabled', true);
                },
                data: {
                    action,
                    id,
                    type: 'conversations',
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

    $('#ttp-tester-crawling').ttpTester({
        onRetrieve: (data) => {
            const {
                action,
                nonce,
                rawOutput,
                retrieve,
            } = data

            const url = $('#ttp-crawl-url').val()
            if (0 === url.length) {
                alert('Please enter a URL')
                return
            }

            wp.ajax.send({
                beforeSend: () => {
                    retrieve.prop('disabled', true);
                },
                data: {
                    action,
                    type: 'crawling',
                    url,
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
});
