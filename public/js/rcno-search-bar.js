function debounce(fn, delay) {
  var timeoutID = null;
  return function () {
    clearTimeout(timeoutID);
    var args = arguments;
    var that = this;
    timeoutID = setTimeout(function () {
      fn.apply(that, args);
    }, delay)
  }
}

const RcnoSearchBar = {
  template: '#search-bar-template',
  data () {
    return {
      search: '',
      results: []
    };
  },
  created () {

  },
  watch: {
    search: debounce( function () {
      if (this.search.length > 3) {
        this.fetchData();
      }
    }, 500 )
  },
  computed: {

  },
  methods: {
    fetchData: function () {
      jQuery.ajax({
          method: 'POST',
          url: rcno_search_bar_options.ajax_url,
          data: {
            action: 'send_results',
            search: this.search,
            nonce: rcno_search_bar_options.search_bar_nonce,
          },
        })
        .then(function (data) {
        console.log(data);
        })
        .fail(function (res) {
          if (res.status !== 200) {
            console.log(res);
          }
        });
      },
  }
};

const app2 = new Vue({
  el: '#reviews-search-bar',
  components: {
    'rcno-search-bar': RcnoSearchBar,
  },
});
