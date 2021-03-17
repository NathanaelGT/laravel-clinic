interface Fetch {
  endpoint: string,
  method: 'GET' | 'POST' | 'PUT' | 'DELETE',
  data?: Object,
  callback?: (res: any) => any,
  fail?: (message: string) => any
}

export default ({ endpoint, method, data, callback, fail }: Fetch) => {
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
      if (ok) callback?.(res)
      else {
        if (message === 'Unauthorized') {
          return alert('Tidak dapat mengubah jadwal, harap coba login ulang')
        }

        fail?.(message)
        console.error(message)

        if (
          res.toLowerCase().includes('<!doctype html>') &&
          confirm('Error terdeteksi, ingin menampilkan HTML?')
        ) {
          document.documentElement.innerHTML = res
        }
        else console.error(res)
      }
    })
    .catch(err => {
      if (err.toString() === 'TypeError: Failed to fetch') {
        return alert('Data belum sempat tersimpan')
      }

      fail?.(err.toString())
      alert(err)
      console.error(err)
    })
}
