export default (rowElement: HTMLElement, deleteService: (id: string) => void) => {
  const doctorScheduleElement = rowElement.parentElement
  const doctorNameElement = doctorScheduleElement.previousElementSibling as HTMLElement

  const removeDoctorService = rowSpanElement => {
    doctorScheduleElement.nextElementSibling.remove()
    doctorScheduleElement.remove()
    rowSpanElement.rowSpan -= 1
    deleteService(doctorNameElement.getAttribute('data-id'))
    doctorNameElement.remove()
  }

  const searchServiceNameElement = currentElement => {
    const sibling = currentElement.previousElementSibling
    const siblingFirstChild = sibling.children[0]
    if (siblingFirstChild.rowSpan) removeDoctorService(siblingFirstChild)
    else searchServiceNameElement(sibling)
  }

  setTimeout(() => {
    if (confirm(`Apakah anda ingin sekalian menghapus layanan Dr. ${doctorNameElement.innerText}?`)) {
      const serviceNameContainerRowSpan = (
        (doctorNameElement.previousElementSibling as HTMLTableDataCellElement)?.rowSpan
      )

      if (serviceNameContainerRowSpan === 1) {
        deleteService(doctorNameElement.getAttribute('data-id'))
        doctorScheduleElement.parentElement.remove()
      }
      else if (serviceNameContainerRowSpan > 1) {
        removeDoctorService(doctorNameElement.previousElementSibling)
      }
      else {
        searchServiceNameElement(doctorNameElement.parentElement)
      }
    }
  }, 0)
}