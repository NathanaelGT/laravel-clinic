import fetch from './fetch'
import { applyLiveEdit } from '../doctorList'

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

  fetch(`${url}/${element.dataset.id}`, 'POST', data, res => {
    if (url !== 'delete') {
      element.contentEditable = 'true'
      if (sibling) sibling.contentEditable = 'true'

      element.classList.remove('text-secondary')
      sibling?.classList.remove('text-secondary')

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
  }, message => {
    element.contentEditable = 'true'
    if (sibling) sibling.contentEditable = 'true'

    element.classList.replace('text-secondary', 'text-danger')
    sibling?.classList.replace('text-secondary', 'text-danger')

    if (fail) fail()
    element.title = message
  })
}

export const fetching = {
  name(element: HTMLElement, name: string) {
    update('doctor', element, { name })
  },
  service(element: HTMLElement) {
    const children = element.parentElement.children
    const sibling = children[element === children[0] ? 1 : 0] as HTMLElement

    const rawTime = (children[0] as HTMLElement).innerText.split('-')
    const rawQuota = (children[1] as HTMLElement).innerText

    const quota = calculateQuota(rawQuota, rawTime)[1]
    const [timeStart, timeEnd] = rawTime
    const data = { quota, timeStart, timeEnd }

    if (children[0].getAttribute('data-id') === 'new') {
      const grandParent = element.parentElement.parentElement
      const doctorServiceId = grandParent.parentElement.parentElement.parentElement.parentElement.previousElementSibling.getAttribute('data-id')

      data['doctorServiceId'] = Number(doctorServiceId)
      data['day'] = (grandParent.previousElementSibling as HTMLElement).innerText.split(':')[0]
    }

    update('service', element, data, sibling, ({ status, message, twinsId }) => {
      if (twinsId && confirm('Jadwal ini terdeteksi duplikat, apakah anda ingin menghapus salah satunya?')) {
        const twins = element.parentElement.parentElement.querySelector(`span[data-id="${twinsId}"]`) as HTMLElement
        fetch('merge', 'POST', { first: twins.dataset.id, second: element.dataset.id }, res => {
          const deleted = res.deleted_id === twins.dataset.id ? twins.parentElement : element.parentElement
          const notDeleted = res.deleted_id === twins.dataset.id ? element.parentElement : twins.parentElement
          notDeleted.className = 'one-line'
          applyLiveEdit(notDeleted)
          deleted.remove()
        })
      }
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


const validateQuota = (per: string, time: string[]) => {
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

const validateTime = (variable: number, max: number, name: string) => {
  if (isNaN(variable)) return template(name)
  if (variable >= max) return template(name, name + ' lebih dari ' + max)
  if (variable < 0) return template(name, name + ' kurang dari 0')
}

const readFormat = (format: string, time: number) => {
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

const formatTime = (per: number, time: number) => {
  if (per === time) return '1 sesi'

  const minutes = per % 60
  const hours = (per - minutes) / 60
  if (!minutes) return `${hours} jam`
  if (!hours) return `${minutes} menit`
  return `${hours} jam ${minutes} menit`
}

export const validate = {
  name(value: string) {
    if (value.length < 1) return 'nama terlalu pendek (minimal 1 huruf)'
    if (value.length > 255) return 'nama terlalu panjang (maksimal 255 huruf)'
  },

  time(value: string, element: HTMLElement) {
    const time = value.split(value.indexOf('-') > -1 ? '-' : ' ').map(val => val.trim())
    const timeNumber = time.map(timeToNumber)
    if (time.length !== 2) return 'waktu praktek tidak valid'

    if (timeNumber[0] > timeNumber[1]) return 'waktu mulai lebih besar dari waktu selesai'
    else if (timeNumber[0] === timeNumber[1]) return 'waktu mulai tidak bisa sama dengan waktu selesai'

    let timeFormat = ''
    const message = time.map((time, index) => {
      const timeNumber = time.split(time.indexOf(':') > -1 ? ':' : ' ').map(Number)
      const hour = timeNumber[0]
      const minute = timeNumber[1] || 0

      const validatedHour = validateTime(hour, 24, 'jam')
      if (validatedHour) return validatedHour

      const validatedMinute = validateTime(minute, 60, 'menit')
      if (validatedMinute) return validatedMinute

      if (index > 0) timeFormat += ' - '
      timeFormat += (hour > 9 ? hour : '0' + hour) + ':' + (minute > 9 ? minute : '0' + minute)
    })

    for (let i = 0; i < message.length - 1; i++) {
      if (message[i]) return message[i]
    }
    element.innerText = timeFormat

    const per = (element.nextElementSibling as HTMLElement).innerText
    const validation = validateQuota(per, time)
    if (validation) return validation
  },

  per(value: string, element: HTMLElement) {
    const [start, end] = (element.previousElementSibling as HTMLElement).innerText.split('-')
    const time = timeToNumber(end) - timeToNumber(start)
    const per = readFormat(value, time)
    if (isNaN(per)) return 'kuota tidak valid'
    else element.innerText = formatTime(per, time)

    const validation = validateQuota(value, [start, end])
    if (validation) return validation
  }
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
