interface Window {
  schedules: {
    Senin?: string[]
    Selasa?: string[]
    Rabu?: string[]
    Kamis?: string[]
    Sabtu?: string[]
    Jumat?: string[]
  }[]
  selected?: {
    doctor: string
    date: string
    time: string
  }
}

const isNotEmpty = (val: string) => Boolean(val)
const isNumber = (val: string) => !isNaN(Number(val)) && !/\./.test(val)
const isPhoneNumber = (val: string) => /^[0-9]{0,5}[-\s\.]?[0-9]{0,5}[-\s\.]?[0-9]{0,5}$/.test(val)

const cacheInputValue = [
  { validation: isNotEmpty, element: document.querySelector<HTMLInputElement>('#name') },
  { validation: isNumber, element: document.querySelector<HTMLInputElement>('#nik') },
  { validation: isPhoneNumber, element: document.querySelector<HTMLInputElement>('#phone-number') },
  { validation: isNotEmpty, element: document.querySelector<HTMLInputElement>('#address') }
]

const doctorInput = document.querySelector<HTMLElement>('#doctor')
const dateInput = document.querySelector<HTMLSelectElement>('#date')
const timeInput = document.querySelector<HTMLSelectElement>('#time')
const inputNumber = document.querySelectorAll<HTMLInputElement>('input[data-type="number"]')
const inputPhoneNumber = document.querySelectorAll<HTMLInputElement>('input[data-type="phone-number"]')
const form = document.querySelector('form')


cacheInputValue.forEach(({ element, validation }) => {
  if (!element.value) {
    const key = 'laravel-clinic:' + element.id + '-input'

    element.oninput = () => {
      console.log(element.value)
      localStorage.setItem(key, element.value)
    }

    const savedValue = localStorage.getItem(key)
    if (validation(savedValue)) {
      element.value = savedValue
    }
  }
})


inputNumber.forEach(input => {
  input.onkeypress = event => {
    if (event.which !== 8 && event.which !== 0 && event.which < 48 || event.which > 57) {
      event.preventDefault()
    }
  }

  input.onpaste = () => {
    setTimeout(() => {
      input.value = input.value.replace(/\D/g, '')
    })
  }
})

inputPhoneNumber.forEach(input => {
  input.onkeypress = event => {
    if (!isPhoneNumber(input.value + event.key)) {
      event.preventDefault()
    }
  }
})

form.classList.remove('d-none')


const timeInputPlaceholder = <option hidden disabled ariaHidden>Pilih jam praktek</option>
const OptionElement = ({ value, formatedValue = null, disabled = false }) => (
  <option value={value} disabled={disabled}>{formatedValue ?? value}</option>
)
const getHumanReadableDate = (unixSeconds: string) => {
  const seconds = Number(unixSeconds.replace('exception', ''))
  const isException = unixSeconds.includes('exception')

  const _date = new Date(Number(seconds) * 1000)
  const date = _date.getDate()
  const day = _date.toLocaleString('id-ID', { weekday: 'long' })
  const month = _date.toLocaleString('id-ID', { month: 'long' })
  const year = _date.getFullYear()

  return `${day}, ${date} ${month} ${year} ${isException ? '- Conflict' : ''}`
}

let firstRenderDate = true
const showDateInputOptions = (index: number) => {
  const timeInputWarning = <option disabled ariaHidden>Harap pilih hari praktek terlebih dahulu</option>
  const placeholder = <option hidden disabled ariaHidden>Pilih hari praktek</option>

  dateInput.innerHTML = ''
  dateInput.appendChild(placeholder)

  Object.keys(window.schedules[index]).forEach(unixSeconds => {
    if (!unixSeconds) return

    dateInput.appendChild(
      <OptionElement
        value={unixSeconds}
        formatedValue={getHumanReadableDate(unixSeconds)}
      />
    )
  })

  timeInput.innerHTML = ''
  timeInput.appendChild(timeInputPlaceholder)
  timeInput.appendChild(timeInputWarning)

  if ((doctorInput as HTMLInputElement).value === window.selected?.doctor) {
    const { date } = window.selected
    let dateOption = dateInput.querySelector(`option[value="${date}"]`) as HTMLOptionElement
    if (!dateOption && date) {
      dateOption = <OptionElement value={date} formatedValue={getHumanReadableDate(date)} disabled />
      dateInput.appendChild(dateOption)
    }
    dateOption.innerText += ' - Tanggal yang sebelumnya dipilih'

    if (firstRenderDate) {
      firstRenderDate = false
      dateInput.value = date
    }
  }
  else {
    // gatau kenapa kalo gapake settimeout ga keselect
    setTimeout(() => {
      placeholder.selected = true
      timeInputPlaceholder.selected = true
    })
  }
}

if (doctorInput.tagName === 'SELECT') {
  doctorInput.onchange = event => {
    const index = Math.max((event.target as HTMLSelectElement).selectedIndex - 1, 0)
    showDateInputOptions(index)
    dateInput['pointer'] = index
  }
}
else {
  showDateInputOptions(0)
  doctorInput.parentElement.appendChild(
    <input type="hidden" name="doctor" value={(doctorInput as HTMLInputElement).value} />
  )
}

let firstRenderTime = true
dateInput.onchange = event => {
  timeInput.innerHTML = ''
  timeInput.appendChild(timeInputPlaceholder)

  const workingSchedule = window.schedules[Number(event.target['pointer']) || 0]

  const index = Number((event.target as HTMLSelectElement).value) || Object.keys(workingSchedule)[0]
  const selectedWorkingSchedule = workingSchedule[index]

  let firstOption
  selectedWorkingSchedule?.forEach((hour: string) => {
    if (!hour) return

    const option = <OptionElement value={hour} />
    if (!firstOption) {
      firstOption = option
    }
    timeInput.appendChild(option)
  })

  if (
    (doctorInput as HTMLInputElement).value === window.selected?.doctor &&
    dateInput.value === window.selected.date
  ) {
    const { time } = window.selected
    let timeOption = timeInput.querySelector(`option[value="${time}"]`) as HTMLOptionElement
    if (!timeOption && time) {
      timeOption = <OptionElement value={time} disabled />
      timeInput.appendChild(timeOption)
    }
    timeOption.innerText += ' - Jam yang sebelumnya dipilih'

    if (firstRenderTime) {
      firstRenderTime = false
      timeInput.value = time
    }
  }
  else {
    setTimeout(() => {
      if (selectedWorkingSchedule.length === 1) {
        firstOption.selected = true
      }
      else {
        timeInputPlaceholder.selected = true
      }
    })
  }
}

if (window.selected) {
  if (doctorInput.tagName === 'SELECT') {
    (doctorInput as HTMLSelectElement).value = window.selected.doctor
    doctorInput.dispatchEvent(new Event('change', { bubbles: true }))
  }
  dateInput.value = window.selected.date
  dateInput.dispatchEvent(new Event('change', { bubbles: true }))
}
