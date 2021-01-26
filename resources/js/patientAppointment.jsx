const doctorInput = document.getElementById('doctor')
const dayInput = document.getElementById('day')
const timeInput = document.getElementById('time')
const inputNumber = document.querySelectorAll('input[data-type="number"]')
const form = document.querySelector('form')

inputNumber.forEach(input => {
  input.onkeypress = event => {
    if (event.which !== 8 && event.which !== 0 && event.which < 48 || event.which > 57)
      event.preventDefault()
  }
})

form.classList.remove('d-none')

const Option = ({ value }) => <option value={value}>{value}</option>

if (doctorInput.tagName === 'SELECT') {
  const timeInputWarning = <option disabled ariaHidden>Harap pilih hari praktek terlebih dahulu</option>
  const placeholder = <option hidden disabled ariaHidden>Pilih hari praktek</option>

  doctorInput.onchange = event => {
    dayInput.innerHTML = ''
    dayInput.appendChild(placeholder)
    dayInput.pointer = event.target.selectedIndex - 1

    Object.keys(schedules[event.target.selectedIndex - 1]).forEach(day => {
      dayInput.appendChild(<Option value={day} />)
    })

    timeInput.innerHTML = ''
    timeInput.appendChild(timeInputPlaceholder)
    timeInput.appendChild(timeInputWarning)

    placeholder.selected = true
    timeInputPlaceholder.selected = true
  }
}

const timeInputPlaceholder = <option hidden disabled ariaHidden>Pilih jam praktek</option>
dayInput.onchange = event => {
  timeInput.innerHTML = ''
  timeInput.appendChild(timeInputPlaceholder)

  const workingSchedule = schedules[Number(event.target.pointer) || 0]

  let firstOption
  workingSchedule[event.target.value].forEach(hour => {
    const option = <Option value={hour} />
    if (!firstOption) firstOption = option
    timeInput.appendChild(option)
  })

  if (workingSchedule[event.target.value].length === 1)
    firstOption.selected = true
  else
    timeInputPlaceholder.selected = true
}