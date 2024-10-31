// @see  https://preactjs.com/guide/v8/api-reference/#preacth--preactcreateelement
// @see  https://gridjs.io/docs/examples/virtual-dom

document.addEventListener('DOMContentLoaded', function() {
  function link (text, url) {
    return gridjs.h('a', {href: url}, text)
  }

  function toBool(s) {
    const regex = /^\s*(true|1|on)\s*$/i

    return regex.test(s);
  }

  const {sort, limit, search, columns, links} = window.rcno.rcnoTableShortcodeOptions
  const args = {}

  args.sort = toBool(sort)
  args.search = toBool(search)

  args.pagination = {}
  args.pagination.limit = parseInt(limit)
  args.pagination.summary = false

  args.columns = []
  for (const col of columns.split(',')) {
    args.columns.push({
      data: function (row) {
        return row.meta[col]
      },
      name: col[0].toUpperCase() + col.slice(1)
    })
  }

  function getTermLinks(terms, key, link = true) {
    let out = []
    for (const [i, term] of terms.entries()) {
      if(term.hasOwnProperty(key)) {
        const child = link ? gridjs.h('a', {href: term.link}, `${term[key]} `) : gridjs.h('span', {}, `${term[key]} `)
        out.push(child)
      }
    }

    return gridjs.h('span', {}, out)
  }

  args.data = window.rcno.rcnoTableShortcodeData.map(function(i) {
    console.log(i.title)
    return {
      ID: i.ID,
      title: i.title,
      meta: {
        post: i.title,
        isbn: links.includes('isbn') ? link(i.meta.rcno_book_isbn, i.URL) : i.meta.rcno_book_isbn,
        title: links.includes('title') ? link(i.meta.rcno_book_title, i.URL) : i.meta.rcno_book_title,
        link: link(i.URL, i.URL),
        genre: links.includes('genre') ? getTermLinks(i.terms, 'genre') : '',
        author: links.includes('author') ? getTermLinks(i.terms, 'author') : '',
        publisher: links.includes('publisher') ? getTermLinks(i.terms, 'publisher') : '',
        year: i.meta.rcno_book_pub_date,
        rating: gridjs.h('meter-discrete', {value: parseFloat(i.meta.rcno_admin_rating)}),
        category: links.includes('category') ? getTermLinks(i.terms, 'category') : '',
        tag: links.includes('tag') ? getTermLinks(i.terms, 'tag') : '',
      },
    }
  })


  const grid = new gridjs.Grid(args)

  grid.render(document.getElementById('rcno-table'));
});
