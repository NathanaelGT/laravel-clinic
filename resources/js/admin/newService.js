import Form from '../component/NewServiceForm'

const container = registerRender(document.getElementById('form-container'))

const timeToNumber = time => {
  const [hours, minutes] = time.split(':')

  return hours * 60 + Number(minutes)
}

const calculateQuota = getSessionTime => ({ number, per, time }, index) => {
  const _time = data.timeInputValue[index]
  if (_time) {
    const sessionTime = timeToNumber(_time[1]) - timeToNumber(_time[0])

    if (per === 'Menit') per = 1
    else if (per === 'Jam') per = 60
    else per = sessionTime

    const quota = (time * per) / number

    return getSessionTime ? [quota, sessionTime] : quota
  }
  return 1
}

const searchValid = index => {
  const timeError = []
  for (let i = 0; i < 2; i++) {
    if (!data.timeInputValue[index]?.[i])
      timeError[i] = 'Harap tentukan waktu praktek'
  }
  const dayInvalid = data.dayValue[index]?.filter(value => value !== null)?.length === 0

  let quotaErrorMessage
  const quota = data.quotaValue[index]
  if (!quota.number) quotaErrorMessage = 'Harap tentukan kuota'
  else if (!quota.time) quotaErrorMessage = 'Harap tentukan waktu kuota'
  else if (!quota.per) quotaErrorMessage = 'Harap tentukan pembagian waktu kuota'
  else {
    const [calculatedQuota, time] = calculateQuota(true)(quota, index)
    if (calculatedQuota < 1) quotaErrorMessage = 'Waktu kuota terlalu sedikit'
    else if (time % calculatedQuota !== 0) {
      const fixed = Number((time % calculatedQuota).toFixed(2))
      quotaErrorMessage = 'Waktu kuota tidak bisa dibagi habis' + (fixed ? ` (sisa ${fixed} menit)` : '')
    }
  }

  const universal = {}
  const _data = {}
  if (data.doctorName === '') universal.doctor = 'Harap isi nama dokter'
  if (data.serviceName === '') universal.service = 'Harap isi nama layanan'
  if (timeError.length) _data.time = timeError
  if (quotaErrorMessage) _data.quota = quotaErrorMessage
  if (dayInvalid) _data.day = 'Harap pilih hari praktek'

  //jumlah input yang divalidasi 3, kalo semuanya kosong engga perlu ditampilin error kecuali halaman pertama
  const showError = Object.keys(_data).length !== 3 || index === 0
  data.errorMessages[index] = showError ? { ..._data, ...universal } : null

  return !Object.keys(_data).length
}

let fetching = false
const data = {
  serviceName: '',
  doctorName: '',
  timeInputValue: [[]],
  quotaValue: [{}],
  dayValue: [[]],
  highestIndex: 0,
  currentIndex: 0,
  errorMessages: [[]],

  getButtonsData() {
    const { currentIndex } = data
    return [
      {
        className: `col-12 col-sm-3 ${window.innerWidth >= 576 ? 'ps-0' : ' mb-3'}`,
        color: 'outline-primary',
        text: 'Sebelumnya',
        disabled: currentIndex === 0,
        onclick: () => {
          if (currentIndex > 0) {
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
          const quota = data.quotaValue[currentIndex]
          if (
            data.timeInputValue[currentIndex].length !== 0 ||
            data.dayValue[currentIndex].length !== 0 ||
            quota.number ||
            quota.time ||
            quota.per
          ) {
            const currentIndex = ++data.currentIndex
            if (currentIndex > data.highestIndex) {
              data.highestIndex = currentIndex
              data.timeInputValue[currentIndex] = []
              data.quotaValue[currentIndex] = {}
              data.dayValue[currentIndex] = []
              data.errorMessages[currentIndex] = null
            }
          }
          else
            data.errorMessages[currentIndex] = null

          container.render(Form(data))
        }
      }
    ]
  },

  handleSubmit(event) {
    event.preventDefault()

    if (!fetching) {
      fetching = true
      let trimmed = false
      const quota = data.quotaValue[data.highestIndex]
      if (
        data.timeInputValue[data.highestIndex].length === 0 &&
        data.dayValue[data.highestIndex].length === 0 &&
        !quota.number &&
        !quota.time &&
        !quota.per
      ) {
        trimmed = true
        data.highestIndex--
        data.quotaValue.pop()
        data.timeInputValue.pop()
        data.errorMessages.pop()
        data.dayValue.pop()
      }

      let isValid = true
      for (let i = data.highestIndex; i >= 0; i--) {
        if (!searchValid(i)) {
          data.currentIndex = i
          isValid = false
        }
      }

      const { serviceName, doctorName, quotaValue, dayValue } = data

      if (isValid) {
        fetch(window.location.origin + '/api/service', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            serviceName,
            doctorName,
            quota: quotaValue.map(calculateQuota(false)),
            day: dayValue
          })
        }).then(res => res.json())
          .then(res => console.log(res))
          .catch(err => {
            alert(err)
            console.error(err)
          })
          .finally(() => {
            fetching = false
            container.render(Form(data))
          })
      }

      if (trimmed) {
        data.highestIndex++
        data.timeInputValue[data.highestIndex] = []
        data.quotaValue[data.highestIndex] = {}
        data.dayValue[data.highestIndex] = []
        data.errorMessages[data.highestIndex] = null
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
}

container.render(Form(data))

let previousWidth = window.innerWidth
window.addEventListener('resize', () => {
  if (previousWidth !== window.innerWidth) {
    previousWidth = window.innerWidth
    container.render(Form(data))
  }
})