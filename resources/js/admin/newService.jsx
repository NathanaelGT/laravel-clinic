const container = registerRender(document.getElementById('form-container'))

const Input = ({
  placeholder, name, type = 'text', oninput = null, value = '',
  onkeypress = null, showPage = false, errorMessage = null, list = null
}) => {
  const input = (
    <input
      id={name}
      name={name}
      type={type}
      placeholder={`Masukkan ${placeholder.toLowerCase()}`}
      oninput={oninput}
      onkeypress={onkeypress}
      value={value}
      className="form-control"
      autocomplete="off"
      required
    />
  )
  if (list) input.setAttribute('list', list)
  if (type === 'number') input.min = 1

  return (
    <div>
      <label htmlFor={name}>{placeholder}</label>
      {showPage && <label className="float-end">Halaman {currentIndex + 1}</label>}
      {input}

      <div className={`form-text text-danger ${!errorMessage && 'input-valid-text'}`}>
        {errorMessage || '\xA0'}
      </div>
    </div>
  )
}

const InlineCheckbox = value => (
  <div className="form-check form-check-inline">
    <input
      className="form-check-input"
      type="checkbox"
      id={value}
      value={value}
      checked={dayValue[currentIndex].indexOf(value) !== -1}
      onchange={event => {
        if (event.target.checked)
          dayValue[currentIndex].push(value)
        else
          dayValue[currentIndex] = dayValue[currentIndex].filter(day => day !== value)
      }}
    />
    <label className="form-check-label" htmlFor={value}>{value}</label>
  </div>
)

let serviceName = ''
let doctorName = ''
const timeInputValue = [[]]
const quotaValue = ['']
const dayValue = [[]]
let highestIndex = 0
let currentIndex = 0

const errorMessages = [[]]

const searchValid = () => {
  const timeError = []
  for (let i = 0; i < 2; i++) {
    if (!timeInputValue[currentIndex]?.[i])
      timeError[i] = 'Harap tentukan waktu praktek'
  }
  const dayInvalid = dayValue[currentIndex]?.filter(value => value !== null)?.length === 0

  const data = {}
  if (doctorName === '') data.doctor = 'Harap isi nama dokter'
  if (serviceName === '') data.service = 'Harap isi nama layanan'
  if (timeError.length) data.time = timeError
  if (quotaValue[currentIndex]?.length === 0) data.quota = 'Harap tentukan kuota'
  if (dayInvalid) data.day = 'Harap pilih hari praktek'
  errorMessages[currentIndex] = Object.keys(data).length ? { ...data } : null

  return !Object.keys(data).length
}

const getButtonsData = () => (
  [
    {
      className: `col-12 col-sm-3 ${window.innerWidth >= 576 ? 'ps-0' : ' mb-3'}`,
      color: 'outline-primary',
      text: 'Sebelumnya',
      disabled: currentIndex === 0,
      onclick: () => {
        if (currentIndex > 0) {
          if (errorMessages[currentIndex] === null)
            currentIndex--
          container.render(<Form />)
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
        if (
          timeInputValue[currentIndex].length !== 0 ||
          quotaValue[currentIndex] !== '' ||
          dayValue[currentIndex].length !== 0
        ) {
          if (searchValid()) currentIndex++
          if (currentIndex > highestIndex) {
            highestIndex = currentIndex
            timeInputValue[currentIndex] = []
            quotaValue[currentIndex] = ''
            dayValue[currentIndex] = []
            errorMessages[currentIndex] = null
          }
        }
        else
          errorMessages[currentIndex] = null

        container.render(<Form />)
      }
    }
  ]
)

const handleSubmit = event => {
  event.preventDefault()

  // TODO
}

const Form = () => (
  <form className="d-flex flex-column justify-content-between align-items-center h-100" onsubmit={handleSubmit}>
    <div className="w-100">
      <h3 className="text-center pb-4">Buat Layanan Baru</h3>

      <div className="py-1">
        <Input
          placeholder="Nama Dokter"
          name="service"
          value={doctorName}
          oninput={event => {
            doctorName = event.target.value
          }}
          showPage
          errorMessage={errorMessages[currentIndex]?.doctor}
        />
      </div>

      <div className="py-1">
        <Input
          placeholder="Nama Layanan"
          name="service"
          value={serviceName}
          oninput={event => {
            serviceName = event.target.value
          }}
          errorMessage={errorMessages[currentIndex]?.service}
          list="services"
        />

        <datalist id="services">
          {window.services.map(service => (
            <option value={service} />
          ))}
        </datalist>
      </div>

      <div className="row py-1">
        {[
          { placeholder: 'Jam Mulai Praktek', name: 'time time-start' },
          { placeholder: 'Jam Selesai Praktek', name: 'time time-end' }
        ].map((data, index) => (
          <div className={`col-md-6 col-sm-12 p${(window.innerWidth >= 768) && (index === 0 ? 's' : 'e')}-0`}>
            <Input
              {...data}
              type="time"
              oninput={event => {
                timeInputValue[currentIndex][index] = event.target.value
              }}
              value={timeInputValue[currentIndex]?.[index]}
              errorMessage={errorMessages[currentIndex]?.time?.[index]}
            />
          </div>
        ))}
      </div>

      <Input
        placeholder="Kuota"
        name="quota"
        type="number"
        onkeypress={event => {
          if (event.which != 8 && event.which != 0 && event.which < 48 || event.which > 57) {
            event.preventDefault()
          }
        }}
        oninput={event => {
          quotaValue[currentIndex] = event.target.value
        }}
        value={quotaValue[currentIndex]}
        errorMessage={errorMessages[currentIndex]?.quota}
      />
    </div>

    <div className="w-100">
      <div className="d-flex justify-content-center pb-1">
        Hari Praktek
      </div>

      <div className="d-flex flex-column flex-sm-row justify-content-center">
        {[['Senin', 'Selasa', 'Rabu'], ['Kamis', 'Jumat', 'Sabtu']].map(days => (
          <div className="d-flex justify-content-center py-1">
            {days.map(InlineCheckbox)}
          </div>
        ))}
      </div>
      <div className="d-flex justify-content-center">
        <div className={`form-text text-danger ${!errorMessages[currentIndex]?.day && 'input-valid-text'}`}>
          {errorMessages[currentIndex]?.day || '\xA0'}
        </div>
      </div>

      <div className="row my-4">
        {getButtonsData().map(({ className, color, text, type, disabled = false, onclick = null }) => (
          <div className={className}>
            <div className="d-grid">
              <button
                type={type || 'button'}
                className={`btn btn-${color}`}
                disabled={disabled}
                onclick={onclick}
              >
                {text}
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  </form>
)

container.render(<Form />)

let previousWidth = window.innerWidth
window.addEventListener('resize', () => {
  if (previousWidth !== window.innerWidth) {
    previousWidth = window.innerWidth
    container.render(<Form />)
  }
})