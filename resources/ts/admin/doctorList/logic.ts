import fetch from './fetch'

const clearColor = (element: HTMLElement) => {
  element.classList.remove('text-secondary')
  element.classList.remove('text-warning')
  element.classList.remove('text-danger')
}

const update = (
  url: string, element: HTMLElement, data: object = {}, sibling: HTMLElement = null,
  callback: (res: any) => any = null, fail: () => any = null
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

  fetch({
    endpoint: `${url}/${element.dataset.id}`,
    method: 'POST',
    data,
    callback: res => {
      if (url !== 'delete') {
        element.contentEditable = 'true'
        if (sibling) {
          sibling.contentEditable = 'true'
          clearColor(sibling)
        }
        clearColor(element)

        callback?.(res)
        if (!res.newId) return

        element.dataset.id = res.newId
        const previousSibling = element.previousElementSibling
        if (previousSibling) {
          previousSibling.setAttribute('data-id', res.newId)
        }
        else {
          element.nextElementSibling.setAttribute('data-id', res.newId)
        }
      }
      else callback?.(res)
    },
    fail: message => {
      element.contentEditable = 'true'
      if (sibling) sibling.contentEditable = 'true'

      element.classList.replace('text-secondary', 'text-danger')
      sibling?.classList.replace('text-secondary', 'text-danger')

      fail?.()
      element.title = message
    }
  })
}

const showConflictButton = () => {
  const conflictButton = document.querySelector('a#conflict-button') as HTMLButtonElement
  conflictButton.classList.remove('d-none')
  conflictButton.focus()
}

export const fetching = {
  name(element: HTMLElement, name: string) {
    update('doctor', element, { name })
  },
  service(element: HTMLElement) {
    const parent = element.parentElement
    const grandParent = parent.parentElement

    const children = parent.children
    const sibling = children[element === children[0] ? 1 : 0] as HTMLElement
    const timeElement = children[0] as HTMLElement
    const quotaElement = children[1] as HTMLElement

    const rawTime = (children[0] as HTMLElement).innerText.split('-')
    const rawQuota = (children[1] as HTMLElement).innerText

    const quota = calculateQuota(rawQuota, rawTime)[1]
    const [timeStart, timeEnd] = rawTime
    const data = { quota, timeStart, timeEnd }

    if (timeElement.getAttribute('data-id') === 'new') {
      const doctorServiceId = grandParent.parentElement.parentElement.parentElement
        .parentElement.previousElementSibling.getAttribute('data-id')

      data['doctorServiceId'] = Number(doctorServiceId)
      data['day'] = (grandParent.previousElementSibling as HTMLElement).innerText.split(':')[0]
    }

    update('service', element, data, sibling, ({ status, message, info }) => {
      if (status === 'pending' && info === 'equal') {
        if (confirm('Jadwal ini sama persis dengan aslinya, apakah anda ingin menghapus draft?')) {
          return fetch({
            endpoint: `conflict/${element.dataset.id}`,
            method: 'DELETE',
            callback: res => {
              timeElement.innerText = res.info
              timeElement.classList.remove('text-danger')
            }
          })
        }

        fetch({
          endpoint: `service/${element.dataset.id}`,
          method: 'POST',
          data: { ...data, skipPending: true },
          callback: res => {
            const [start, end] = res.time.split('-')
            const time = timeToNumber(end) - timeToNumber(start)

            timeElement.innerText = res.time
            quotaElement.innerText = formatTime(res.quota, time)

            timeElement.classList.remove('text-warning')
          }
        })
      }

      if (status !== 'success') {
        const textColor = status === 'error' ? 'danger' : status

        element.classList.add('text-' + textColor)
        element.title = message
      }

      if (status === 'warning') showConflictButton()
    })
  },
  close(element: HTMLElement, success?: ({ status, message }) => any) {
    update('close', element, {}, null, ({ status, message }) => {
      if (status === 'warning') {
        element.classList.add('text-warning')
        element.title = message

        showConflictButton()
      }
      success?.({ status, message })
    })
  }
}


export const validateQuota = (per: string, time: string[]) => {
  const [sessionTime, calculatedQuota] = calculateQuota(per, time)
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

export const validateTime = (variable: number, max: number, name: string) => {
  if (isNaN(variable)) return template(name)
  if (variable >= max) return template(name, `${name} lebih dari ${max}`)
  if (variable < 0) return template(name, `${name} kurang dari 0`)
}

export const readFormat = (format: string, time: number) => {
  if (format.indexOf('sesi') > -1 || format.indexOf('s') > -1) return time

  let per = 0
  const timeFormat = format.toLocaleLowerCase().split(format.indexOf('jam') > -1 ? 'jam' : 'j')
  const hourNumber = Number(timeFormat[0] || 1)

  if (isNaN(hourNumber)) {
    const timeF = timeFormat.join('')
    if (timeF === 'menit' || timeF === 'm') per++
    else {
      const [minute, hour] = timeFormat[0].split(format.indexOf('menit') > -1 ? 'menit' : 'm').map(val => {
        const num = Number(val)
        return isNaN(num) ? 0 : num
      })

      per += hour * 60
      per += minute
    }
  }
  else {
    const minute = timeFormat[1]?.split(format.indexOf('menit') > -1 ? 'menit' : 'm')[0] || 0
    per += hourNumber * 60
    per += Number(minute)
  }

  return per || NaN
}

export const formatTime = (per: number, time: number) => {
  if (per === time) return '1 sesi'

  const minutes = per % 60
  const hours = (per - minutes) / 60
  if (!minutes) return `${hours} jam`
  if (!hours) return `${minutes} menit`
  return `${hours} jam ${minutes} menit`
}


export const timeToNumber = (time: string) => {
  const [hours, minutes] = time.split(':').map(Number)

  return hours * 60 + (minutes || 0)
}

export const calculateQuota = (per: string, time: string[]) => {
  if (time.length !== 2) {
    alert('Harap isi waktu mulai dan tutup dengan valid')
    throw new Error('Invalid time element')
  }

  const sessionTime = timeToNumber(time[1]) - timeToNumber(time[0])
  const _per = readFormat(per, sessionTime)

  return [sessionTime, _per]
}
