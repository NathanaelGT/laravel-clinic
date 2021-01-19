import Form from '../component/NewServiceForm'

const container = registerRender(document.getElementById('form-container'))

const searchValid = () => {
  const { currentIndex } = data
  const timeError = []
  for (let i = 0; i < 2; i++) {
    if (!data.timeInputValue[currentIndex]?.[i])
      timeError[i] = 'Harap tentukan waktu praktek'
  }
  const dayInvalid = data.dayValue[currentIndex]?.filter(value => value !== null)?.length === 0

  let quotaErrorMessage
  const quota = data.quotaValue[currentIndex]
  if (!quota.number) quotaErrorMessage = 'Harap tentukan kuota'
  else if (!quota.time) quotaErrorMessage = 'Harap tentukan waktu kuota'
  else if (!quota.per) quotaErrorMessage = 'Harap tentukan pembagian waktu kuota'

  const _data = {}
  if (data.doctorName === '') _data.doctor = 'Harap isi nama dokter'
  if (data.serviceName === '') _data.service = 'Harap isi nama layanan'
  if (timeError.length) _data.time = timeError
  if (quotaErrorMessage) _data.quota = quotaErrorMessage
  if (dayInvalid) _data.day = 'Harap pilih hari praktek'
  data.errorMessages[currentIndex] = Object.keys(_data).length ? { ..._data } : null

  return !Object.keys(_data).length
}

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
            if (data.errorMessages[currentIndex] === null)
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
            if (searchValid()) data.currentIndex++
            if (data.currentIndex > data.highestIndex) {
              data.highestIndex = currentIndex + 1
              data.timeInputValue[currentIndex + 1] = []
              data.quotaValue[currentIndex + 1] = {}
              data.dayValue[currentIndex + 1] = []
              data.errorMessages[currentIndex + 1] = null
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

    // TODO
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