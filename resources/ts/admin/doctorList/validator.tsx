import deleteTableRecord from './deleteTableRecord'
import { timeToNumber, validateTime, validateQuota, readFormat, formatTime, fetching } from './logic'

const checkIfScheduleCollided = (element: HTMLElement, start: number, end: number) => {
  const parent = element.parentElement
  const grandParent = parent.parentElement

  const tagName = element.tagName
  const siblings = Array.from(grandParent.children).filter(element => (
    element !== parent && element.tagName === tagName
  ))

  for (let i = 0; i < siblings.length; i++) {
    const time = (siblings[i].firstElementChild as HTMLElement).innerText.split('-')
    const [siblingStart, siblingEnd] = time.map(timeToNumber)

    const message = []
    if (start >= siblingStart && start <= siblingEnd) message.push('mulai')
    if (end >= siblingEnd && end <= siblingEnd) message.push('selesai')

    if (message.length) return `waktu ${message.join(' dan ')} jadwal ini berbentrokan dengan jadwal lain`
  }
}

const validate = {
  name(value: string) {
    if (value.length < 1) return 'nama terlalu pendek (minimal 1 huruf)'
    if (value.length > 255) return 'nama terlalu panjang (maksimal 255 huruf)'
  },

  time(value: string, element: HTMLElement) {
    const time = value.split(value.indexOf('-') > -1 ? '-' : ' ').map(val => val.trim())
    const [start, end] = time.map(timeToNumber)
    if (time.length !== 2) return 'waktu praktek tidak valid'

    if (start > end) return 'waktu mulai lebih besar dari waktu selesai'
    else if (start === end) return 'waktu mulai tidak bisa sama dengan waktu selesai'

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

    const collidedMessage = checkIfScheduleCollided(element, start, end)
    if (collidedMessage) return collidedMessage
  },

  per(value: string, element: HTMLElement) {
    const [start, end] = (element.previousElementSibling as HTMLElement).innerText.split('-')
    const [startNumber, endNumber] = [timeToNumber(start), timeToNumber(end)]
    const time = endNumber - startNumber
    const per = readFormat(value, time)
    if (isNaN(per)) return 'kuota tidak valid'
    else element.innerText = formatTime(per, time)

    const validation = validateQuota(value, [start, end])
    if (validation) return validation

    const collidedMessage = checkIfScheduleCollided(element, startNumber, endNumber)
    if (collidedMessage) return collidedMessage
  }
}

export default (element: HTMLElement) => {
  let text = element.innerText

  const rollback = () => {
    element.innerText = text
    throw 'rollback'
  }

  const rollbackIf = (condition: any) => {
    if (condition) rollback()
  }

  return (checkIfScheduleIsNew: any = true) => {
    try {
      let newText = element.innerText.trim()
      if (newText === text && checkIfScheduleIsNew) return

      rollbackIf(element.dataset.type === 'name' && !newText)

      if (newText === '' || newText.toLowerCase() === 'tutup') {
        const parent = element.parentElement
        const grandParent = parent.parentElement
        const day = (grandParent.previousElementSibling as HTMLElement).innerText.slice(0, -1)

        if (grandParent.childElementCount === 2) {
          if (confirm(`Apakah anda yakin ingin menutup jadwal pada hari ${day}?`)) {
            fetching.close(element, ({ status, message }) => {
              console.log(status, message)
              if (status === 'warning') {
                const grandParentsGrandChild = grandParent.firstElementChild.firstElementChild
                grandParentsGrandChild.classList.add('text-warning');
                (grandParentsGrandChild as HTMLElement).title = message
              }
              else {
                // ngecek kalo dari hari senin - minggu jadwalnya tutup semua
                // kalo tutup semua, tanyain adminnya mau hapus layanannya atau engga
                const rowElement = grandParent.parentElement.parentElement.parentElement
                const blank = Array.from(rowElement.children).every(child => (
                  Array.from(child.children).every(grandChild => (
                    (grandChild.children[1] as HTMLElement).innerText.trim() === 'Tutup'
                  ))
                ))
                if (blank) deleteTableRecord(rowElement.parentElement)
              }
            })

            grandParent.firstElementChild.replaceWith(
              <span className="one-line">
                <span>Tutup</span>
              </span>
            )
          }
        }
        else {
          if (element.dataset.id === 'new') {
            const plusMinusIcon = grandParent.lastElementChild as HTMLImageElement
            return plusMinusIcon.click()
          }

          const grandParentChild = Array.from(grandParent.children)
          const parentNthChild = grandParentChild.indexOf(parent) + 1
          if (confirm(`Apakah anda yakin ingin menghapus sesi ke ${parentNthChild} pada hari ${day}?`)) {
            // gabisa langsung dikirim parent.remove, soalnya native function
            return fetching.close(element, () => parent.remove())
          }
        }

        rollback()
      }

      const validation = validate[element.dataset.type](newText, element)
      if (validation) {
        element.title = validation
        return element.classList.add('text-danger')
      }
      else {
        newText = element.innerText.trim() //ada kemungkinan validasinya ngubah textnya

        //kalo ada newline bearti ada conflict
        if (element.title.indexOf('\n') === -1) {
          element.title = ''
        }
      }
      element.classList.remove('text-danger')

      if (newText === text && checkIfScheduleIsNew) return

      const prefix = 'Apakah anda yakin ingin '
      if (element.dataset.id === 'new') {
        rollbackIf(!confirm(`${prefix}menambahkan jadwal ini?\n\n${newText}`))
      }
      else {
        rollbackIf(!confirm(`${prefix}menyimpan perubahan ini?\n\n${text}\nMenjadi\n${newText}`))
      }

      if (element.dataset.id === 'new') {
        const parent = element.parentElement
        parent.classList.remove('text-success');
        (parent.nextElementSibling as HTMLImageElement).src = window.origin + '/svg/plus.svg'
      }

      text = newText
      const type = element.dataset.type
      fetching[type === 'time' || type === 'per' ? 'service' : type](element, newText)
    }
    catch (exception) {
      if (exception !== 'rollback') throw exception
    }
  }
}
