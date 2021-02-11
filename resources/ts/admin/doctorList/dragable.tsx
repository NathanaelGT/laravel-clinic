import fetch from './fetch'

export default (tableBody: HTMLElement) => {
  let draggedElement: HTMLTableRowElement[]
  let available: HTMLTableRowElement[]
  const blank = <tr />

  let serviceOrder: number[]
  const services = Array.from(tableBody.querySelectorAll<HTMLTableRowElement>('tr:not([data-drag-target])'))
  services.forEach(service => {
    service.draggable = true

    service.addEventListener('dragstart', () => {
      serviceOrder = Array.from(tableBody.querySelectorAll<HTMLTableDataCellElement>('td[data-drag]'))
        .map(service => {
          return Number(service.dataset.drag)
        })

      const dragId = (service.firstElementChild as HTMLTableDataCellElement).dataset.drag
      draggedElement = [
        service,
        ...dragId
          ? Array.from(tableBody.querySelectorAll<HTMLTableRowElement>(`tr[data-drag-target="${dragId}"]`))
          : []
      ]
      available = [...services.filter(_service => _service !== service), blank]

      draggedElement.forEach(schedule => {
        schedule.classList.add('opacity-half')
      })
    })

    service.addEventListener('dragend', () => {
      const removeOpacityHalf = () => draggedElement.forEach((element: HTMLTableRowElement) => {
        element.classList.remove('opacity-half')
      })
      const newOrder = Array.from(tableBody.querySelectorAll<HTMLTableDataCellElement>('td[data-drag]'))
        .map(service => {
          return Number(service.dataset.drag)
        })

      if (serviceOrder.toString() === newOrder.toString()) return removeOpacityHalf()
      fetch('reorderService', 'POST', { order: newOrder }, removeOpacityHalf, message => {
        draggedElement.forEach((element: HTMLTableRowElement) => {
          element.classList.replace('opacity-half', 'text-danger')
          element.title = message
        })
      })
    })
  })

  let order: number[]
  tableBody.querySelectorAll<HTMLTableRowElement>('tr[data-drag-target]').forEach(schedule => {
    schedule.draggable = true
    const service = tableBody.querySelector<HTMLTableDataCellElement>(
      `td[data-drag="${schedule.dataset.dragTarget}"]`
    ).parentElement

    schedule.addEventListener('dragstart', () => {
      draggedElement = [schedule]
      const tableBodyChildren = Array.from(tableBody.children) as HTMLTableRowElement[]
      const serviceSchedule = tableBody.querySelectorAll(`tr[data-drag-target="${schedule.dataset.dragTarget}"]`)
      const lastServiceSchedule = serviceSchedule[serviceSchedule.length - 1]
      const serviceIndex = tableBodyChildren.findIndex(schedule => schedule === service)
      const lastServiceScheduleIndex = tableBodyChildren.findIndex(schedule => schedule === lastServiceSchedule)

      order = Array.from(serviceSchedule).map(schedule => (
        Number((schedule.firstElementChild as HTMLElement).dataset.id)
      ))

      const serviceSchedules = tableBodyChildren.slice(serviceIndex + 1, lastServiceScheduleIndex + 2)
      available = serviceSchedules.filter(_schedule => _schedule !== schedule)

      schedule.classList.add('opacity-half')
    })

    schedule.addEventListener('dragend', () => {
      const serviceSchedule = tableBody.querySelectorAll(`tr[data-drag-target="${schedule.dataset.dragTarget}"]`)
      const firstChild = (schedule: Element) => schedule.firstElementChild as HTMLElement
      const newOrder = Array.from(serviceSchedule).map(schedule => (
        Number(firstChild(schedule).dataset.id)
      ))

      if (order.toString() === newOrder.toString()) return schedule.classList.remove('opacity-half')
      fetch('reorderDoctorService/' + schedule.dataset.dragTarget, 'POST', { order: newOrder }, () => {
        schedule.classList.remove('opacity-half')
      }, message => {
        schedule.classList.replace('opacity-half', 'text-danger')
        schedule.title = message
      })
    })
  })
  tableBody.append(blank)

  let previousY: number
  let timeoutId: NodeJS.Timeout
  tableBody.addEventListener('dragover', event => {
    const yPos = event.clientY
    if (previousY === yPos) return
    if (timeoutId) clearTimeout(timeoutId)
    previousY = yPos

    timeoutId = setTimeout(() => {
      const afterElement = available.reduce((closest, element) => {
        // yang dijadikan patokan first child soalnya kalo rownya isinya cuma nama layanan, tingginya bakal 0
        // ada kemungkinan dia gapunya child (tr kosong, const blank)
        const box = (element.firstElementChild || element).getBoundingClientRect()
        const offset = yPos - box.top - box.height / 2

        return (offset < 0 && offset > closest.offset) ? { offset, element } : closest
      }, { offset: -Infinity, element: null as HTMLTableRowElement }).element

      if (
        !afterElement ||
        afterElement.previousElementSibling === draggedElement[draggedElement.length - 1]
      ) return

      if (Array.isArray(draggedElement)) {
        draggedElement.forEach(element => {
          tableBody.insertBefore(element, afterElement)
        })
      }
      else {
        tableBody.insertBefore(draggedElement, afterElement)
      }
    }, 10)
  })
}
