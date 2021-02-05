declare global {
  interface Window {
    services: string[]
  }
}

import { QuotaValue, ErrorMessage, ButtonData } from '../declaration/newService'
import Form from '../component/NewServiceForm'

const container = window.registerRender(document.getElementById('form-container'))

const ERRORMESSAGE_DEFAULT = { time: ['', ''], quota: '', day: '', doctor: '', service: '' }

const timeToNumber = (time: string) => {
  const [hours, minutes] = time.split(':').map(time => Number(time))

  return hours * 60 + minutes
}

const calculateQuota = (getSessionTime: boolean) => (
  { number, per, time }: { number?: string, per?: string, time?: string }, index: number
) => {
  const _time = data.timeInputValue[index]
  if (_time.length) {
    const sessionTime = timeToNumber(_time[1]) - timeToNumber(_time[0])
    let perUnit: number

    if (per === 'Menit') perUnit = 1
    else if (per === 'Jam') perUnit = 60
    else perUnit = sessionTime

    const quota = (Number(time) * perUnit) / Number(number)

    return getSessionTime ? [quota, sessionTime] : quota
  }
  throw new Error('Data tidak valid')
}

const searchValid = (index: number) => {
  const timeError: string[] = []
  for (let i = 0; i < 2; i++) {
    if (!data.timeInputValue[index]?.[i]) timeError[i] = 'Harap tentukan waktu praktek'
  }
  const [start, end] = data.timeInputValue[index].map(timeToNumber)
  if (start > end) timeError[0] = 'Waktu mulai lebih besar dari waktu selesai'
  else if (start === end) timeError[0] = 'Waktu mulai tidak bisa sama dengan waktu selesai'

  const dayInvalid = data.dayValue[index]?.filter(value => value !== '')?.length === 0

  let quotaErrorMessage: string
  const quota = data.quotaValue[index]
  if (!quota.number) quotaErrorMessage = 'Harap tentukan kuota'
  else if (!quota.time) quotaErrorMessage = 'Harap tentukan waktu kuota'
  else if (!quota.per) quotaErrorMessage = 'Harap tentukan pembagian waktu kuota'
  else if (quota.per === 'Sesi' && Number(quota.time) !== 1) {
    quotaErrorMessage = 'Tidak bisa mengatur waktu lebih dari 1 sesi'
  }
  else {
    const [calculatedQuota, time] = calculateQuota(true)(quota, index) as number[]
    if (calculatedQuota < 1) quotaErrorMessage = 'Waktu kuota terlalu sedikit'
    else if (time % calculatedQuota !== 0) {
      const fixed = Number((time % calculatedQuota).toFixed(2))
      quotaErrorMessage = 'Waktu kuota tidak bisa dibagi habis' + (fixed ? ` (sisa ${fixed} menit)` : '')
    }
  }

  const validationMessage = {
    doctor: data.doctorName === '' ? 'Harap isi nama dokter' : '',
    service: data.serviceName === '' ? 'Harap isi nama layanan' : '',
    time: timeError.length ? timeError : ['', ''],
    quota: quotaErrorMessage || '',
    day: dayInvalid ? 'Harap pilih hari praktek' : ''
  }

  data.errorMessages[index] = validationMessage

  return Object.values(validationMessage).every(val => (
    typeof val === 'string'
      ? val === ''
      : val.every(val => val === '')
  ))
}

const removeLastPage = () => {
  data.highestIndex--
  data.quotaValue.pop()
  data.timeInputValue.pop()
  data.errorMessages.pop()
  data.dayValue.pop()
}

const isEmpty = (index: number) => {
  const quota = data.quotaValue[index]

  return [
    data.timeInputValue[index].reduce((total, current) => total += current),
    data.dayValue[index].length,
    quota.number,
    quota.time,
    quota.per
  ].every(val => !val)
}

const setInitialValue = (index: number) => {
  data.timeInputValue[index] = ['', '']
  data.quotaValue[index] = { number: '', time: '', per: '' }
  data.dayValue[index] = []
  data.errorMessages[index] = { ...ERRORMESSAGE_DEFAULT }
}

const data = {
  serviceName: '',
  doctorName: '',
  timeInputValue: <string[][]>[],
  quotaValue: <QuotaValue[]>[],
  dayValue: <string[][]>[],
  highestIndex: 0,
  currentIndex: 0,
  errorMessages: <ErrorMessage[]>[],

  getButtonsData(): ButtonData[] {
    const { currentIndex } = data
    return [
      {
        className: `col-12 col-sm-3 ${window.innerWidth >= 576 ? 'ps-0' : ' mb-3'}`,
        color: 'outline-primary',
        text: 'Sebelumnya',
        disabled: currentIndex === 0,
        onclick: () => {
          if (currentIndex > 0) {
            if (isEmpty(currentIndex)) removeLastPage()
            data.currentIndex--
            container.render(Form(data))
          }
        }
      },
      {
        className: 'col-12 col-sm-6',
        color: 'primary',
        text: 'Tambahkan',
        type: 'submit'
      },
      {
        className: `col-12 col-sm-3 ${window.innerWidth >= 576 ? 'pe-0' : ' mt-3'}`,
        color: 'outline-primary',
        text: 'Selanjutnya',
        onclick: () => {
          if (currentIndex !== data.highestIndex || !isEmpty(data.currentIndex)) {
            const currentIndex = ++data.currentIndex
            if (currentIndex > data.highestIndex) {
              data.highestIndex = currentIndex
              setInitialValue(currentIndex)
            }
          }
          else data.errorMessages[currentIndex] = { ...ERRORMESSAGE_DEFAULT }

          container.render(Form(data))
        }
      }
    ]
  },

  handleSubmit(event: Event) {
    event.preventDefault()

    let trimmed = false
    if (data.highestIndex > 0 && isEmpty(data.highestIndex)) {
      trimmed = true
      removeLastPage()
    }

    let isValid = true
    for (let i = data.highestIndex; i >= 0; i--) {
      if (!searchValid(i)) {
        data.currentIndex = i
        isValid = false
      }
    }

    const { serviceName, doctorName, timeInputValue, quotaValue, dayValue } = data

    let ok: boolean
    if (isValid) {
      fetch(window.location.origin + '/api/service', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          serviceName,
          doctorName,
          time: timeInputValue,
          quota: quotaValue.map(calculateQuota(false)),
          day: dayValue
        })
      })
        .then(res => {
          ok = res.ok
          return res.text()
        })
        .then(res => {
          try { return JSON.parse(res) }
          catch { return res }
        })
        .then(res => {
          if (ok) window.location.href = res.redirect
          else {
            alert(res)
            console.error(res)
          }
        })
        .catch(err => {
          alert(err)
          console.error(err)
        })
        .finally(() => {
          container.render(Form(data))
        })
    }

    if (trimmed) {
      data.highestIndex++
      setInitialValue(data.highestIndex)
    }

    //render ulang biar errornya keupdate (di tampilan)
    container.render(Form(data))
    if (isValid) {
      container.DOMchildNodes.querySelectorAll('button').forEach(button => {
        button.disabled = true
        if (button.type === 'submit') {
          button.innerHTML = (`
            <div class="d-flex justify-content-center">
              <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          `)
        }
      })
    }
  }
}
setInitialValue(0)

container.render(Form(data))

let previousWidth = window.innerWidth
window.addEventListener('resize', () => {
  if (previousWidth !== window.innerWidth) {
    previousWidth = window.innerWidth
    container.render(Form(data))
  }
})