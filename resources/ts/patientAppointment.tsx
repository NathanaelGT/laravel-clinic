interface Window {
  schedules: {
    Senin?: string[],
    Selasa?: string[],
    Rabu?: string[],
    Kamis?: string[],
    Sabtu?: string[],
    Jumat?: string[]
  }[]
}

const isNumber = (val: string) => !isNaN(Number(val)) && !/\./.test(val)

const cacheInputValue = [
  { validation: () => true, element: document.getElementById('name') as HTMLInputElement },
  { validation: isNumber, element: document.getElementById('nik') as HTMLInputElement },
  { validation: isNumber, element: document.getElementById('phone-number') as HTMLInputElement },
  { validation: () => true, element: document.getElementById('address') as HTMLInputElement }
]

const doctorInput = document.getElementById('doctor')
const dateInput = document.getElementById('date') as HTMLSelectElement
const timeInput = document.getElementById('time')
const inputNumber = document.querySelectorAll<HTMLInputElement>('input[data-type="number"]')
const form = document.querySelector('form')


cacheInputValue.forEach(({ element, validation }) => {
  if (!element.value) {
    const key = 'laravel-clinic:' + element.id + '-input';

    element.oninput = (event: Event) => {
      localStorage.setItem(key, (event.target as HTMLInputElement).value)
    }

    const savedValue = localStorage.getItem(key)
    if (validation(savedValue)) element.value = savedValue.slice(0, element.maxLength)
    else localStorage.removeItem(key)
  }
})


inputNumber.forEach(input => {
  input.onkeypress = event => {
    if (event.which !== 8 && event.which !== 0 && event.which < 48 || event.which > 57)
      event.preventDefault()
  }

  input.onpaste = () => {
    setTimeout(() => {
      input.value = input.value.replace(/\D/g, '')
    }, 0)
  }
})

form.classList.remove('d-none')

const OptionElement = ({ value, formatedValue = null }) => <option value={value}>{formatedValue ?? value}</option>

if (doctorInput.tagName === 'SELECT') {
  const timeInputWarning = <option disabled ariaHidden>Harap pilih hari praktek terlebih dahulu</option>
  const placeholder = <option hidden disabled ariaHidden>Pilih hari praktek</option>

  doctorInput.onchange = event => {
    dateInput.innerHTML = ''
    dateInput.appendChild(placeholder)
    dateInput['pointer'] = (event.target as HTMLSelectElement).selectedIndex - 1

    Object.keys(window.schedules[(event.target as HTMLSelectElement).selectedIndex - 1]).forEach(unixSeconds => {
      const _date = new Date(Number(unixSeconds) * 1000)
      const date = _date.getDate()
      const day = _date.toLocaleString('id-ID', { weekday: 'long' })
      const month = _date.toLocaleString('id-ID', { month: 'long' })
      const year = _date.getFullYear()

      dateInput.appendChild(
        <OptionElement
          value={unixSeconds}
          formatedValue={`${day}, ${date} ${month} ${year}`}
        />
      )
    })

    timeInput.innerHTML = ''
    timeInput.appendChild(timeInputPlaceholder)
    timeInput.appendChild(timeInputWarning)

    placeholder.selected = true
    timeInputPlaceholder.selected = true
  }
}

const timeInputPlaceholder = <option hidden disabled ariaHidden>Pilih jam praktek</option>
dateInput.onchange = event => {
  timeInput.innerHTML = ''
  timeInput.appendChild(timeInputPlaceholder)

  const workingSchedule = window.schedules[Number(event.target['pointer']) || 0]

  let firstOption
  workingSchedule[(event.target as HTMLSelectElement).value].forEach((hour: string) => {
    const option = <OptionElement value={hour} />
    if (!firstOption) firstOption = option
    timeInput.appendChild(option)
  })

  if (workingSchedule[(event.target as HTMLSelectElement).value].length === 1)
    firstOption.selected = true
  else
    timeInputPlaceholder.selected = true
}