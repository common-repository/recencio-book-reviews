const AdminCurrentlyReading = {
    template: '#admin-reading-template',
    data () {
        return {
            all_updates: [],
            curr_update: {
                book_cover:       '',
                book_title:       '',
                book_author:      '',
                current_page:     '',
                num_of_pages:     '',
                progress_comment: '',
                last_updated:     '',
                finished_book:    false
            },
            curr_index:  null,
            is_loading:  true,
            message:     '',
            data_source: '',
        };
    },
    created () {
        this.fetchData();
    },
    mounted () {
    },
    computed: {
        disabled: function () {
            return this.curr_update.progress_index > 1;
        },
        percentage: function () {
            const value = Math.round((this.all_updates[this.curr_index].current_page /
                this.all_updates[this.curr_index].num_of_pages) * 100);
            return isNaN(value) ? 0 : value;
        },
        time_ago: function () {
            var time = this.curr_update.last_updated;
            switch (typeof time) {
                case 'number':
                    break;
                case 'string':
                    time = +new Date(time);
                    break;
                case 'object':
                    if (time.constructor === Date) time = time.getTime();
                    break;
                default:
                    time = +new Date();
            }
            var time_formats = [
                [60, 'seconds', 1], // 60
                [120, '1 minute ago', '1 minute from now'], // 60*2
                [3600, 'minutes', 60], // 60*60, 60
                [7200, '1 hour ago', '1 hour from now'], // 60*60*2
                [86400, 'hours', 3600], // 60*60*24, 60*60
                [172800, 'Yesterday', 'Tomorrow'], // 60*60*24*2
                [604800, 'days', 86400], // 60*60*24*7, 60*60*24
                [1209600, 'Last week', 'Next week'], // 60*60*24*7*4*2
                [2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
                [4838400, 'Last month', 'Next month'], // 60*60*24*7*4*2
                [29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
                [58060800, 'Last year', 'Next year'], // 60*60*24*7*4*12*2
                [2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
                [5806080000, 'Last century', 'Next century'], // 60*60*24*7*4*12*100*2
                [58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
            ];
            var seconds = (+new Date() - time) / 1000,
                token = 'ago',
                list_choice = 1;

            if (Math.floor(seconds) === 0) {
                return 'Just now'
            }
            if (seconds < 0) {
                seconds = Math.abs(seconds);
                token = 'from now';
                list_choice = 2;
            }
            var i = 0,
                format;
            while (format = time_formats[i++])
                if (seconds < format[0]) {
                    if (typeof format[2] == 'string')
                        return format[list_choice];
                    else
                        return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
                }
            return time;
        }
    },
    methods: {
        fetchData: function () {

            var _this = this;

            if (localStorage.getItem('rcno_all_updates')) {
                const data       = JSON.parse(localStorage.getItem('rcno_all_updates'));
                this.all_updates = data;
                this.curr_update = data[data.length - 1];
                this.curr_index  = data.length - 1;
                this.is_loading  = false;
                this.data_source = 'local-storage';

                return;
            }

            jQuery.ajax({
                method: 'GET',
                url: currently_reading.api.url,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', currently_reading.api.nonce);
                },
            })
            .then(function (data) {
                _this.all_updates = data;
                _this.curr_update = data[data.length - 1];
                _this.curr_index  = data.length - 1;
                _this.is_loading  = false;
                _this.data_source = 'remote-server';

                localStorage.setItem('rcno_all_updates', JSON.stringify(data));
            })
            .fail(function (res) {
                if (res.status !== 200) {
                    _this.message = currently_reading.strings.error;
                    console.log(res);
                }
            });
        },
        submitData: function () {
            var _this = this;
            _this.curr_update.last_updated = JSON.parse(JSON.stringify(new Date()));

            jQuery.ajax({
                method: 'POST',
                url: currently_reading.api.url,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', currently_reading.api.nonce);
                },
                data: _this.curr_update
            })
            .then(function (res) {
                if (_this.curr_update.finished_book) {
                    _this.curr_update.book_cover       = '';
                    _this.curr_update.book_title       = '';
                    _this.curr_update.book_author      = '';
                    _this.curr_update.current_page     = '';
                    _this.curr_update.num_of_pages     = '';
                    _this.curr_update.progress_comment = '';
                    _this.curr_update.last_updated     = '';
                    _this.curr_update.finished_book    = false;

                    return localStorage.removeItem('rcno_all_updates');
                }
                _this.message = currently_reading.strings.saved;
                //localStorage.removeItem('rcno_all_updates');
                localStorage.setItem('rcno_all_updates', JSON.stringify(res));
            })
            .fail(function (res) {
                if (res.status !== 200) {
                    _this.message = currently_reading.strings.error;
                    console.log(res);
                }
            });

        },
        uploadCover: function () {
            var cover_uploader = wp.media({
                title: 'Currently Reading Book Cover',
                button: {
                    text: 'Use File'
                },
                multiple: false
            })
            .on('select', function () {
                var attachment = cover_uploader.state().get('selection').first().toJSON();
                this.curr_update.book_cover = attachment.url;
            }.bind(this))
            .open();
        }
    }
};

const app = new Vue({
    el: '#admin-currently-reading',
    components: {
        'admin-currently-reading': AdminCurrentlyReading,
    },
});

