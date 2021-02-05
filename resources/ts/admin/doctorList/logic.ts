export const update = (
  url: string, element: HTMLElement, data: object = {}, sibling: HTMLElement | null = null,
  callback: (res: any) => any | null = null, fail: () => any | null = null
) => {
  if (sibling) {
    sibling.classList.remove('text-danger')
    sibling.title = ''
    sibling.contentEditable = 'false'
    sibling.classList.add('text-secondary')
  }

  if (url !== 'delete') {
    element.title = ''
    element.contentEditable = 'false'
    element.classList.add('text-secondary')
  }

  let ok: boolean
  let message: string
  fetch(window.location.origin + '/api/' + url + '/' + element.dataset.id, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
    .then(res => {
      ok = res.ok
      message = res.statusText
      return res.text()
    })
    .then(res => {
      if (url !== 'delete') {
        element.contentEditable = 'true'
        if (sibling) sibling.contentEditable = 'true'

        if (ok) {
          element.classList.remove('text-secondary')
          sibling?.classList.remove('text-secondary')
        }
        else {
          element.classList.replace('text-secondary', 'text-danger')
          sibling?.classList.replace('text-secondary', 'text-danger')
        }
      }

      try {
        const json = JSON.parse(res)

        if (url !== 'delete') {
          element.classList.remove('text-secondary')
          sibling?.classList.remove('text-secondary')
        }
        return json
      }
      catch {
        return res
      }
    })
    .then(res => {
      if (ok) {
        if (callback) callback(res)
        if (element.dataset.id !== 'new') return

        element.dataset.id = res.newId
        const previousSibling = element.previousElementSibling
        if (previousSibling) {
          previousSibling.setAttribute('data-id', res.newId)
        }
        else {
          element.nextElementSibling.setAttribute('data-id', res.newId)
        }
      }
      else {
        if (fail) fail()
        element.title = message
        console.error(message)

        if (res.toLowerCase().includes('<!doctype html>') && confirm('Error terdeteksi, ingin menampilkan HTML?'))
          document.querySelector('html').innerHTML = res
        else console.error(res)
      }
    })
    .catch(err => {
      if (fail) fail()
      alert(err)
      console.error(err)
    })
}

export const fetching = {
  name(element: HTMLElement, name: string) {
    update('doctor', element, { name })
  },
  service(element: HTMLElement) {
    const children = element.parentElement.children
    const sibling = children[element === children[0] ? 1 : 0] as HTMLElement

    const [rawTime, rawQuota] = (
      Array.from(children).map((element, index) => (
        (element as HTMLElement).innerText.split(index === 0 ? ' - ' : ' ')
      ))
    )

    // @ts-ignore
    const quota = calculateQuota(...rawQuota, rawTime)[1]
    const [timeStart, timeEnd] = rawTime
    const data = { quota, timeStart, timeEnd }

    if (children[0].getAttribute('data-id') === 'new') {
      const grandParent = element.parentElement.parentElement
      const doctorServiceId = grandParent.parentElement.parentElement.parentElement.parentElement.previousElementSibling.getAttribute('data-id')

      data['doctorServiceId'] = Number(doctorServiceId)
      data['day'] = (grandParent.previousElementSibling as HTMLElement).innerText.split(':')[0]
    }

    update('service', element, data, sibling, ({ status, message }) => {
      if (status !== 'warning') return

      element.style.color = '#FF9000'
      element.title = message

      const conflictButton = document.querySelector('a#conflict-button') as HTMLButtonElement
      conflictButton.classList.remove('d-none')
      conflictButton.focus()
    })
  },
  close(element: HTMLElement) {
    update('close', element)
  }
}


const checkQuotaAndReturnErrorIfInvalid = (quota: string, per: string, times: string[]) => {
  const [sessionTime, calculatedQuota] = calculateQuota(quota, per, times)
  const modulos = sessionTime % calculatedQuota
  if (modulos) {
    return `waktu kuota tidak bisa dibagi habis (sisa ${modulos} menit / kurang ${calculatedQuota - modulos} menit)`
  }
}

const template = (time: string, extra: string = '') => {
  let message = `harap masukkan ${time} yang valid`
  if (extra) message += `. (${extra})`

  return message
}

const validateTime = (variable: number, max: number, name: string) => {
  if (isNaN(variable)) return template(name)
  if (variable >= max) return template(name, name + ' lebih dari ' + max)
  if (variable < 0) return template(name, name + ' kurang dari 0')
}

export const validate = {
  name(value: string) {
    if (value.length < 1) return 'nama terlalu pendek (minimal 1 huruf)'
    if (value.length > 255) return 'nama terlalu panjang (maksimal 255 huruf)'
  },

  time(value: string, element: HTMLElement) {
    const times = value.split('-').map(val => val.trim())
    if (times.length !== 2) return 'harap masukkan waktu selesai praktek'

    if (times[0] > times[1]) return 'waktu mulai lebih besar dari waktu selesai'
    else if (times[0] === times[1]) return 'waktu mulai tidak bisa sama dengan waktu selesai'

    const message = times.map(time => {
      const [hour, minute] = time.split(':').map(val => Number(val))

      const validatedHour = validateTime(hour, 24, 'jam')
      if (validatedHour) return validatedHour

      const validatedMinute = validateTime(minute, 60, 'menit')
      if (validatedMinute) return validatedMinute
    })

    for (let i = 0; i < message.length - 1; i++) {
      if (message[i]) return message[i]
    }

    const [quota, per] = (element.nextElementSibling as HTMLElement).innerText.split(' ')
    const validation = checkQuotaAndReturnErrorIfInvalid(quota, per, times)
    if (validation) return validation
  },

  per(value: string, element: HTMLElement) {
    const [q, p] = value.trim().toLocaleLowerCase().split(' ')
    let quota: string, per: string
    if (!p) {
      quota = '1'
      per = q
      element.innerText = `${quota} ${per}`
    }
    else {
      quota = q
      per = p
    }
    if (!per) return 'kuota tidak memiliki satuan'
    else if (per === 'sesi' && Number(quota) !== 1) return 'tidak bisa mengatur waktu lebih dari 1 sesi'

    const quotaNumber = Number(quota)
    if (isNaN(quotaNumber) && quotaNumber < 1) return 'harap masukkan kuota yang valid'

    const validPer = ['sesi', 'jam', 'menit']
    if (validPer.indexOf(per.toLowerCase()) === -1) return 'harap masukkan satuan yang valid (sesi/jam/menit)'

    const validation = checkQuotaAndReturnErrorIfInvalid(
      quota, per, (element.previousElementSibling as HTMLElement).innerText.split('-')
    )
    if (validation) return validation
  }
}


export const timeToNumber = (time: string) => {
  const [hours, minutes] = time.split(':').map(time => Number(time))

  return hours * 60 + (minutes || 0)
}

export const calculateQuota = (quota: string, per: string, time: string[]) => {
  if (time.length !== 2) {
    alert('Harap isi waktu mulai dan tutup dengan valid')
    throw new Error('Invalid time element')
  }

  const sessionTime = timeToNumber(time[1]) - timeToNumber(time[0])
  let perNumber: number

  if (per === 'menit') perNumber = 1
  else if (per === 'jam') perNumber = 60
  else perNumber = sessionTime

  return [sessionTime, Number(quota) * perNumber]
}