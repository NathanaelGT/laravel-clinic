export default (rowElement: HTMLElement) => {
  const trElement = rowElement.parentElement
  const doctorName = (rowElement.previousElementSibling as HTMLElement).innerText

  setTimeout(() => {
    if (confirm(`Apakah anda yakin ingin menghapus layanan Dr. ${doctorName}?`)) {
      trElement.classList.add('text-secondary');
      (trElement.lastElementChild.firstElementChild as HTMLFormElement).submit()
    }
  }, 0)
}