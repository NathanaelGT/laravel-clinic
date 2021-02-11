type method = 'GET' | 'POST' | 'PUT' | 'DELETE'

export default (
  endpoint: string, method: method, data: object = {},
  callback: (res: any) => any = null, fail: (message: string) => any = null
) => {
  let ok: boolean
  let message: string
  fetch(window.location.origin + '/api/' + endpoint, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
    .then(res => {
      ok = res.ok
      message = res.statusText
      return res.text()
    })
    .then(res => {
      try {
        return JSON.parse(res)
      }
      catch {
        return res
      }
    })
    .then(res => {
      if (ok && callback) {
        callback(res)
      }
      else {
        if (fail) fail(message)
        console.error(message)

        if (res.toLowerCase().includes('<!doctype html>') && confirm('Error terdeteksi, ingin menampilkan HTML?'))
          document.querySelector('html').innerHTML = res
        else console.error(res)
      }
    })
    .catch(err => {
      if (err.toString() === 'TypeError: Failed to fetch') return alert('Data belum sempat tersimpan')

      if (fail) fail(err.toString())
      alert(err)
      console.error(err)
    })
}
