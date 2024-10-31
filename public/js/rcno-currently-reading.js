const CurrentlyReading = {
    template: '#reading-template',
    data () {
        return {
            all_updates: [],
            curr_index:  null,
            is_loading:  true,
            data_source: ''
        };
    },
    created () {
        this.fetchData();        
    },
    computed: {
        percentage: function () {
            if (this.all_updates.length !== 0) {
                return Math.round((this.all_updates[this.curr_index].current_page /
                    this.all_updates[this.curr_index].num_of_pages) * 100);
            }
        },
        completed: function () {
            if (this.all_updates.length !== 0) {
                return this.percentage + '% ' + rcno_currently_reading.completed;
            }
        }
    },
    methods: {
        fetchData: function () {
            if (localStorage.getItem('rcno_all_updates')) {
                const data       = JSON.parse(localStorage.getItem('rcno_all_updates'));
                this.all_updates = data;
                this.curr_index  = data.length - 1;
                this.is_loading  = false;
                this.data_source = 'local-storage';

                return;
            }
            if ( typeof currently_reading !== 'undefined' ) {
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
            }

        },
        previous: function () {
            if (this.curr_index > 0) {
                this.curr_index--;
            }
        },
        next: function () {
            if (this.curr_index < this.all_updates.length - 1) {
                this.curr_index++;
            }
        }
    }
};

const app = new Vue({
    el: '#currently-reading',
    components: {
        'currently-reading': CurrentlyReading,
    },
});
